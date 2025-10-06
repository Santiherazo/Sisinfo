<?php

class IpBlockManager {
    private PDO $db;
    private ErrorLogger $logger;

    public function __construct(PDO $db, ?ErrorLogger $logger = null) {
        $this->db = $db;
        $this->logger = $logger ?? new ErrorLogger();
    }

    public function getDb(): PDO {
        return $this->db;
    }

    public function isBlocked(string $ip): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT 1 FROM " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                WHERE " . _BLOCK_IP_ADDR_ . " = ?
                AND " . _BLOCK_UNTIL_ . " > NOW()
            ");
            $stmt->execute([$ip]);
            return $stmt->fetchColumn() !== false;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'IP_IS_BLOCKED');
            return false;
        }
    }

    public function getBlockInfo(string $ip): ?array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                WHERE " . _BLOCK_IP_ADDR_ . " = ?
            ");
            $stmt->execute([$ip]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            $this->logger->logException($e, 'IP_GET_BLOCK_INFO');
            return null;
        }
    }

    public function getAttempts(string $ip): array {
        try {
            $stmt = $this->db->prepare("
                SELECT fail_count, ban_count, updated_at FROM " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                WHERE " . _BLOCK_IP_ADDR_ . " = ?
            ");
            $stmt->execute([$ip]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['fail_count' => 0, 'ban_count' => 0, 'updated_at' => null];
        } catch (Throwable $e) {
            $this->logger->logException($e, 'IP_GET_ATTEMPTS');
            return ['fail_count' => 0, 'ban_count' => 0, 'updated_at' => null];
        }
    }

    public function blockIp(string $ip, string $until, ?string $reason = null): bool {
        try {
            $stmt = $this->db->prepare("
                REPLACE INTO " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                (" . _BLOCK_IP_ADDR_ . ", " . _BLOCK_UNTIL_ . ", " . _BLOCK_REASON_ . ", updated_at)
                VALUES (?, ?, ?, NOW())
            ");
            return $stmt->execute([$ip, $until, $reason]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'IP_BLOCK');
            return false;
        }
    }

    public function unblockIp(string $ip): bool {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                WHERE " . _BLOCK_IP_ADDR_ . " = ?
            ");
            return $stmt->execute([$ip]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'IP_UNBLOCK');
            return false;
        }
    }

    public function purgeExpiredBlocks(): int {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                WHERE " . _BLOCK_UNTIL_ . " < NOW()
            ");
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Throwable $e) {
            $this->logger->logException($e, 'IP_PURGE_EXPIRED');
            return 0;
        }
    }

    public function clear(string $ip): void {
        $this->unblockIp($ip);
    }

    public function trackFailure(string $ip, ?int $uid, int $maxAttempts, int $timeout, int $lockoutDuration, array $config): void {
        try {
            $record = $this->getAttempts($ip);
            $failures = (int)$record['fail_count'];
            $bans = (int)$record['ban_count'];
            $lastUpdated = $record['updated_at'] ?? null;

            // Restablecer si han pasado más de 24h y está habilitado
            if (
                !empty($config['enable_failed_reset_after_24h']) &&
                (int)$config['enable_failed_reset_after_24h'] === 1 &&
                $lastUpdated !== null
            ) {
                $lastFail = new DateTime($lastUpdated);
                $limit = (new DateTime())->sub(new DateInterval("PT1440M")); // 24h
                if ($lastFail < $limit) {
                    $failures = 0;
                    $bans = 0;
                }
            }

            $failures++;

            // Seguridad extendida activada
            $extendedEnabled = !empty($config['extended_block_ip_security']) && (int)$config['extended_block_ip_security'] === 1;
            $triggerBans = (int)($config['extended_block_trigger_ban_count'] ?? 1);
            $maxAfterBan = (int)($config['extended_block_max_failures_after_ban'] ?? 3);
            $timeWindowMin = (int)($config['extended_block_time_window'] ?? 1440);

            if ($extendedEnabled && $bans >= $triggerBans) {
                if ($lastUpdated) {
                    $lastFail = new DateTime($lastUpdated);
                    $limitWindow = (new DateTime())->sub(new DateInterval("PT{$timeWindowMin}M"));

                    if ($lastFail >= $limitWindow && $failures >= $maxAfterBan) {
                        // Bloqueo permanente a IP y cuenta
                        $permanentUntil = (new DateTime('+10 years'))->format('Y-m-d H:i:s');

                        $stmt = $this->db->prepare("
                            REPLACE INTO " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                            (" . _BLOCK_IP_ADDR_ . ", fail_count, ban_count, " . _BLOCK_UNTIL_ . ", " . _BLOCK_REASON_ . ", updated_at)
                            VALUES (?, ?, ?, ?, 'Bloqueo extendido permanente tras reintentos', NOW())
                        ");
                        $stmt->execute([$ip, $failures, $bans + 1, $permanentUntil]);

                        if ($uid !== null) {
                            $this->blockAccountPermanently($uid, $config);
                            $this->logBlock($uid, 'perm', 'Bloqueo extendido: múltiples fallos tras baneo previo');
                        }

                        return;
                    }
                }
            }

            if ($failures >= $maxAttempts) {
                $bans++;
                $failures = 0;

                $blockMinutes = $lockoutDuration * $bans;
                $blockUntil = (new DateTime())->add(new DateInterval("PT{$blockMinutes}M"))->format('Y-m-d H:i:s');

                $stmt = $this->db->prepare("
                    REPLACE INTO " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                    (" . _BLOCK_IP_ADDR_ . ", fail_count, ban_count, " . _BLOCK_UNTIL_ . ", " . _BLOCK_REASON_ . ", updated_at)
                    VALUES (?, 0, ?, ?, 'Demasiados intentos fallidos', NOW())
                ");
                $stmt->execute([$ip, $bans, $blockUntil]);

                if ($uid !== null) {
                    $this->logBlock($uid, 'temp', 'Demasiados intentos fallidos');
                }

                if (
                    !empty($config['enable_permanent_blocking']) &&
                    (int)$config['enable_permanent_blocking'] === 1 &&
                    $bans >= (int)($config['permanent_block_after_bans'] ?? 3)
                ) {
                    if ($uid !== null) {
                        $this->blockAccountPermanently($uid, $config);
                        $this->logBlock($uid, 'perm', 'Superó el número de bloqueos temporales permitidos');
                    }
                }
            } else {
                $stmt = $this->db->prepare("
                    REPLACE INTO " . _TBL_WEBENGINE_BLOCKED_IPS_ . "
                    (" . _BLOCK_IP_ADDR_ . ", fail_count, ban_count, " . _BLOCK_UNTIL_ . ", " . _BLOCK_REASON_ . ", updated_at)
                    VALUES (?, ?, ?, NOW(), 'Intento fallido', NOW())
                ");
                $stmt->execute([$ip, $failures, $bans]);
            }
        } catch (Throwable $e) {
            $this->logger->logException($e, 'IP_TRACK_FAILURE');
        }
    }

    private function blockAccountPermanently(int $uid, array $config): void {
        try {
            $stmt = $this->db->prepare("
                UPDATE " . _TBL_WEBENGINE_U_CORE_ . "
                SET " . _CORE_USTATUS_ . " = ?
                WHERE " . _CORE_UID_ . " = ?
            ");
            $stmt->execute([
                (int)($config['user_status_perm_block'] ?? 3),
                $uid
            ]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'PERMANENT_ACCOUNT_BLOCK');
        }
    }

    private function logBlock(int $uid, string $type, string $reason): void {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO " . _TBL_WEBENGINE_BLOCK_LOG_ . "
                (" . _BLOCK_LOG_UID_ . ", " . _BLOCK_LOG_TYPE_ . ", " . _BLOCK_LOG_REASON_ . ")
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$uid, $type, $reason]);
        } catch (Throwable $e) {
            $this->logger->logException($e, 'BLOCK_LOG_INSERT');
        }
    }
}