<?php

class UserPhotoManager
{
	private PDO $pdo;
	private string $avatarDir;
	private string $uploadDir;
	private UploadManager $uploadManager;

	public function __construct(PDO $pdo, string $avatarDir, string $uploadDir)
	{
		$this->pdo = $pdo;
		$this->avatarDir = rtrim($avatarDir, '/') . '/';
		$this->uploadDir = rtrim($uploadDir, '/') . '/';
		$this->uploadManager = new UploadManager();
	}

	public function getCurrentAvatar(int $userId): ?string
	{
		$sql = "SELECT " . _DETAIL_PROFILE_IMG_ . " FROM " . _TBL_WEBENGINE_USER_DETAILS_ . " WHERE " . _DETAIL_UID_ . " = :uid LIMIT 1";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(['uid' => $userId]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$row || empty($row[_DETAIL_PROFILE_IMG_])) {
			return null;
		}

		$relativePath = $row[_DETAIL_PROFILE_IMG_];
		$filePath = (str_starts_with($relativePath, 'avatars/') ? $this->avatarDir : $this->uploadDir) . basename($relativePath);

		if (file_exists($filePath)) {
			return $relativePath;
		}

		return null;
	}

	public function updateUserAvatar(int $userId, string $relativePath): bool
	{
		$sql = "UPDATE " . _TBL_WEBENGINE_USER_DETAILS_ . " SET " . _DETAIL_PROFILE_IMG_ . " = :path WHERE " . _DETAIL_UID_ . " = :uid";
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute(['path' => $relativePath, 'uid' => $userId]);
	}

	public function deleteCustomAvatar(int $userId): bool
	{
		$sql = "SELECT " . _DETAIL_PROFILE_IMG_ . " FROM " . _TBL_WEBENGINE_USER_DETAILS_ . " WHERE " . _DETAIL_UID_ . " = :uid LIMIT 1";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute(['uid' => $userId]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$row || empty($row[_DETAIL_PROFILE_IMG_])) {
			return true;
		}

		$relativePath = $row[_DETAIL_PROFILE_IMG_];
		if (str_starts_with($relativePath, '')) {
			return true;
		}

		$filePath = $this->uploadDir . $relativePath;
		if (file_exists($filePath)) {
			unlink($filePath);
		}

		return $this->updateUserAvatar($userId, '');
	}

	public function useDefaultAvatar(int $userId, string $filename): bool
	{
		if (!preg_match('/^avatar_\d+\.png$/', $filename)) {
			return false;
		}

		$sourcePath = $this->avatarDir . $filename;
		if (!file_exists($sourcePath)) {
			return false;
		}

		$destinationDir = $this->uploadDir . $userId . '/';
		if (!is_dir($destinationDir)) {
			mkdir($destinationDir, 0755, true);
		}

		$destinationPath = $destinationDir . $filename;
		if (!copy($sourcePath, $destinationPath)) {
			return false;
		}

		return $this->updateUserAvatar($userId, 'avatars/'.$filename);
	}

    public function getDefaultAvatars(): array
    {
        $avatars = [];

        for ($i = 1; $i <= 9; $i++) {
            $file = 'avatar_' . $i . '.png';
            $fullPath = $this->avatarDir . $file;
            if (file_exists($fullPath)) {
                $avatars[] = $file;
            } else {
                echo "No encontrado: $fullPath\n";
            }
        }
        
        return $avatars;
    }

	public function uploadAvatarFile(array $file, int $userId): bool
	{
		$this->uploadManager->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
		$this->uploadManager->setMaxFileSize(2 * 1024 * 1024);
		$this->uploadManager->setUploadPath($this->uploadDir);
		$this->uploadManager->setSubfolder((string)$userId);

		$filename = $this->uploadManager->uploadFile($file);
		if (!$filename) {
			return false;
		}

		return $this->updateUserAvatar($userId, $userId . '/' . $filename);
	}

	public function getLastUploadError(): ?string
	{
		return $this->uploadManager->getError();
	}
}