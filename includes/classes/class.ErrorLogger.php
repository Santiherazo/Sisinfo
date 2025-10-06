<?php

class ErrorLogger {
    private string $logDir;
    private string $dbLogPath;
    private string $phpLogPath;

    public function __construct() {
        $this->logDir = rtrim(__PATH_LOGS__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->dbLogPath = $this->logDir . 'database_errors.log';
        $this->phpLogPath = $this->logDir . 'php_errors.log';

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
    }

    public function logDatabaseError(string $message, string $file = '', int $line = 0): void {
        $entry = $this->formatDetailedEntry('DATABASE', $message, $file, $line);
        $this->writeLog($this->dbLogPath, $entry);
    }

    public function logPhpError(string $message, string $file = '', int $line = 0): void {
        $entry = $this->formatDetailedEntry('PHP', $message, $file, $line);
        $this->writeLog($this->phpLogPath, $entry);
    }

    public function logException(Throwable $e, string $type = 'PHP'): void {
        $entry = $this->formatDetailedEntry($type, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        $this->writeLog($type === 'DATABASE' ? $this->dbLogPath : $this->phpLogPath, $entry);
    }

    public function clearLog(string $type): bool {
        $path = $this->getPathByType($type);
        return file_exists($path) ? file_put_contents($path, '') !== false : false;
    }

    public function getLogLines(string $type, int $limit = 100, string $keyword = '', string $date = ''): array {
        $path = $this->getPathByType($type);
        if (!file_exists($path)) return [];

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines);

        if ($keyword !== '') {
            $lines = array_filter($lines, fn($line) => stripos($line, $keyword) !== false);
        }

        if ($date !== '') {
            $lines = array_filter($lines, fn($line) => str_contains($line, "[$date]"));
        }

        return array_slice($lines, 0, $limit);
    }

    public function getLogSummary(): array {
        return [
            'DATABASE' => $this->getLogStats($this->dbLogPath),
            'PHP' => $this->getLogStats($this->phpLogPath),
        ];
    }

    private function getLogStats(string $path): array {
        if (!file_exists($path)) return ['entries' => 0, 'last_modified' => null, 'size_kb' => 0];

        return [
            'entries' => count(file($path)),
            'last_modified' => date('Y-m-d H:i:s', filemtime($path)),
            'size_kb' => round(filesize($path) / 1024, 2),
        ];
    }

    private function getPathByType(string $type): string {
        return match (strtoupper($type)) {
            'DATABASE' => $this->dbLogPath,
            'PHP' => $this->phpLogPath,
            default => throw new InvalidArgumentException("Tipo de log inválido: $type"),
        };
    }

    private function formatDetailedEntry(string $type, string $message, string $file, int $line, string $trace = ''): string {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $uri = $_SERVER['REQUEST_URI'] ?? 'N/A';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'N/A';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';

        $session = isset($_SESSION) ? json_encode($_SESSION, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR) : '{}';
        $get = !empty($_GET) ? json_encode($_GET, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR) : '{}';
        $post = !empty($_POST) ? json_encode($_POST, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR) : '{}';

        $entry = "[$timestamp] [$type] $message in $file on line $line\n";
        $entry .= "IP: $ip | URI: $uri | Method: $method\n";
        $entry .= "User-Agent: $agent\n";
        $entry .= "SESSION: $session\n";
        $entry .= "GET: $get\n";
        $entry .= "POST: $post\n";

        if (!empty($trace)) {
            $entry .= "Stack Trace:\n$trace\n";
        }

        $entry .= str_repeat('-', 120) . PHP_EOL;
        return $entry;
    }

    private function writeLog(string $path, string $entry): void {
        file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
    }

    public function log(string $message, string $file = '', int $line = 0): void {
        $entry = $this->formatDetailedEntry('INFO', $message, $file, $line);
        $this->writeLog($this->phpLogPath, $entry);
    }
}