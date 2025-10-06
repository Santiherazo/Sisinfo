<?php
class BlogManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ==================== POST METHODS ====================

    public function createPost(array $data): int {
        $sql = "INSERT INTO " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                (title, slug, content, author_id, published, status) 
                VALUES (:title, :slug, :content, :author_id, :published, :status)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':author_id' => $data['author_id'],
            ':published' => $data['published'] ?? false,
            ':status' => $data['status'] ?? POST_STATUS_DRAFT
        ]);
        return $this->pdo->lastInsertId();
    }

    public function updatePost(int $id, array $data): bool {
        $sql = "UPDATE " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                SET title = :title, slug = :slug, content = :content, 
                    published = :published, status = :status 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':published' => $data['published'] ?? false,
            ':status' => $data['status'] ?? POST_STATUS_DRAFT,
            ':id' => $id
        ]);
    }

    public function deactivatePost(int $id, string $newStatus = POST_STATUS_INACTIVE): bool {
        $allowedStatuses = [POST_STATUS_INACTIVE, POST_STATUS_ARCHIVED];
        if (!in_array($newStatus, $allowedStatuses)) {
            throw new InvalidArgumentException("Invalid status for deactivation");
        }

        $sql = "UPDATE " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                SET status = :status, published = 0 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $newStatus,
            ':id' => $id
        ]);
    }

    public function activatePost(int $id, string $newStatus = POST_STATUS_ACTIVE): bool {
        $sql = "UPDATE " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                SET status = :status 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $newStatus,
            ':id' => $id
        ]);
    }

    public function getPost(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getPostBySlug(string $slug): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAllPosts(int $limit = 0, int $offset = 0, bool $publishedOnly = true, ?string $status = null): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " WHERE 1=1";
        
        if ($publishedOnly) {
            $sql .= " AND published = 1";
        }
        
        if ($status !== null) {
            $sql .= " AND status = :status";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($status !== null) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostsByAuthor(int $authorId, bool $publishedOnly = true): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " WHERE author_id = ?";
        
        if ($publishedOnly) {
            $sql .= " AND published = 1";
        }
        
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$authorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchPosts(string $searchTerm, bool $publishedOnly = true): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                WHERE (title LIKE :search OR content LIKE :search)";
        
        if ($publishedOnly) {
            $sql .= " AND published = 1";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':search' => "%$searchTerm%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentPosts(int $limit = 5, bool $publishedOnly = true): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_BLOG_POSTS_;
        
        if ($publishedOnly) {
            $sql .= " WHERE published = 1";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPostsCount(bool $publishedOnly = true): int {
        $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_POSTS_;
        
        if ($publishedOnly) {
            $sql .= " WHERE published = 1";
        }
        
        $stmt = $this->pdo->query($sql);
        return (int)$stmt->fetchColumn();
    }

    public function togglePostPublishStatus(int $postId, ?bool $status = null): bool {
        if ($status === null) {
            $sql = "UPDATE " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                    SET published = NOT published 
                    WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$postId]);
        } else {
            $sql = "UPDATE " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                    SET published = ? 
                    WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$status, $postId]);
        }
    }

    // ==================== CATEGORY METHODS ====================

    public function createCategory(string $name, string $slug, string $status = CATEGORY_STATUS_ACTIVE): int {
        $stmt = $this->pdo->prepare("INSERT INTO " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " 
                                    (name, slug, status) VALUES (?, ?, ?)");
        $stmt->execute([$name, $slug, $status]);
        return $this->pdo->lastInsertId();
    }

    public function updateCategory(int $categoryId, string $name, string $slug): bool {
        $stmt = $this->pdo->prepare("UPDATE " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " 
                                    SET name = ?, slug = ? 
                                    WHERE id = ?");
        return $stmt->execute([$name, $slug, $categoryId]);
    }

    public function deactivateCategory(int $categoryId): bool {
        $sql = "UPDATE " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " 
                SET status = :status 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => CATEGORY_STATUS_INACTIVE,
            ':id' => $categoryId
        ]);
    }

    public function activateCategory(int $categoryId): bool {
        $sql = "UPDATE " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " 
                SET status = :status 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => CATEGORY_STATUS_ACTIVE,
            ':id' => $categoryId
        ]);
    }

    public function getAllCategories(?string $status = null): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_BLOG_CATEGORIES_;
        
        if ($status !== null) {
            $sql .= " WHERE status = :status";
        }
        
        $sql .= " ORDER BY name";
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($status !== null) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategory(int $categoryId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " WHERE id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getCategoryBySlug(string $slug): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function assignCategoryToPost(int $postId, int $categoryId): bool {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO " . _TBL_WEBENGINE_BLOG_POST_CATEGORIES_ . " 
                                    (post_id, category_id) VALUES (?, ?)");
        return $stmt->execute([$postId, $categoryId]);
    }

    public function removeCategoryFromPost(int $postId, int $categoryId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM " . _TBL_WEBENGINE_BLOG_POST_CATEGORIES_ . " 
                                    WHERE post_id = ? AND category_id = ?");
        return $stmt->execute([$postId, $categoryId]);
    }

    public function getCategoriesByPost(int $postId): array {
        $sql = "SELECT c.* FROM " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " c
                INNER JOIN " . _TBL_WEBENGINE_BLOG_POST_CATEGORIES_ . " pc ON pc.category_id = c.id
                WHERE pc.post_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostsByCategory(int $categoryId, bool $publishedOnly = true): array {
        $sql = "SELECT p.* FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " p
                INNER JOIN " . _TBL_WEBENGINE_BLOG_POST_CATEGORIES_ . " pc ON pc.post_id = p.id
                WHERE pc.category_id = ?";
        
        if ($publishedOnly) {
            $sql .= " AND p.published = 1";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryIdsByPost(int $postId): array {
        $stmt = $this->pdo->prepare("SELECT category_id FROM " . _TBL_WEBENGINE_BLOG_POST_CATEGORIES_ . " 
                                    WHERE post_id = ?");
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function updatePostCategories(int $postId, array $categoryIds): bool {
        $this->pdo->beginTransaction();
        try {
            // Eliminar categorías actuales
            $stmt = $this->pdo->prepare("DELETE FROM " . _TBL_WEBENGINE_BLOG_POST_CATEGORIES_ . " 
                                        WHERE post_id = ?");
            $stmt->execute([$postId]);
            
            // Añadir nuevas categorías
            $stmt = $this->pdo->prepare("INSERT INTO " . _TBL_WEBENGINE_BLOG_POST_CATEGORIES_ . " 
                                        (post_id, category_id) VALUES (?, ?)");
            foreach ($categoryIds as $categoryId) {
                $stmt->execute([$postId, $categoryId]);
            }
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getTotalCategoryCount(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_CATEGORIES_);
        return (int)$stmt->fetchColumn();
    }

    // ==================== COMMENT METHODS ====================

    public function addComment(int $postId, string $userName, string $comment, string $status = COMMENT_STATUS_PENDING): int {
        $stmt = $this->pdo->prepare("INSERT INTO " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " 
                                    (post_id, user_name, comment, status) 
                                    VALUES (?, ?, ?, ?)");
        $stmt->execute([$postId, $userName, $comment, $status]);
        return $this->pdo->lastInsertId();
    }

    public function getCommentsByPost(int $postId, int $limit = 0, int $offset = 0, ?string $status = null): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " 
                WHERE post_id = :post_id";
        
        if ($status !== null) {
            $sql .= " AND status = :status";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        
        if ($status !== null) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getComment(int $commentId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " WHERE id = ?");
        $stmt->execute([$commentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAllComments(int $limit = 0, int $offset = 0): array {
        $sql = "SELECT * FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " ORDER BY created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->pdo->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteComment(int $commentId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " WHERE id = ?");
        return $stmt->execute([$commentId]);
    }

    public function approveComment(int $commentId): bool {
        return $this->updateCommentStatus($commentId, COMMENT_STATUS_APPROVED);
    }

    public function rejectComment(int $commentId): bool {
        return $this->updateCommentStatus($commentId, COMMENT_STATUS_REJECTED);
    }

    private function updateCommentStatus(int $commentId, string $status): bool {
        $sql = "UPDATE " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " 
                SET status = :status 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $commentId
        ]);
    }

    public function getCommentCountByPost(int $postId): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " 
                                    WHERE post_id = ?");
        $stmt->execute([$postId]);
        return (int)$stmt->fetchColumn();
    }

    public function getTotalCommentCount(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_);
        return (int)$stmt->fetchColumn();
    }

    public function getRecentComments(int $limit = 5): array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " 
                                    ORDER BY created_at DESC 
                                    LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== STATISTICS METHODS ====================

    public function getBlogStatistics(): array {
        return [
            'total_posts' => $this->getTotalPostsCount(false),
            'published_posts' => $this->getTotalPostsCount(true),
            'total_categories' => $this->getTotalCategoryCount(),
            'total_comments' => $this->getTotalCommentCount(),
            'pending_comments' => $this->getCommentCountByStatus(COMMENT_STATUS_PENDING),
            'most_commented_post' => $this->getMostCommentedPost(),
            'latest_post' => $this->getLatestPost(),
            'latest_comment' => $this->getLatestComment()
        ];
    }

    public function getCommentCountByStatus(string $status): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " 
                                    WHERE status = ?");
        $stmt->execute([$status]);
        return (int)$stmt->fetchColumn();
    }

    public function getMostCommentedPost(): ?array {
        $sql = "SELECT p.*, COUNT(c.id) as comment_count 
                FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " p
                LEFT JOIN " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " c ON c.post_id = p.id
                GROUP BY p.id
                ORDER BY comment_count DESC
                LIMIT 1";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getLatestPost(): ?array {
        $stmt = $this->pdo->query("SELECT * FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                                  ORDER BY created_at DESC 
                                  LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getLatestComment(): ?array {
        $stmt = $this->pdo->query("SELECT * FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " 
                                  ORDER BY created_at DESC 
                                  LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getPopularPosts(int $limit = 5): array {
        $sql = "SELECT p.*, COUNT(c.id) as comment_count 
                FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " p
                LEFT JOIN " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " c ON c.post_id = p.id
                GROUP BY p.id
                ORDER BY comment_count DESC
                LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== UTILITY METHODS ====================

    public function isPostActive(int $postId): bool {
        $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " 
                WHERE id = ? AND status = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$postId, POST_STATUS_ACTIVE]);
        return (bool)$stmt->fetchColumn();
    }

    public function isCategoryActive(int $categoryId): bool {
        $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " 
                WHERE id = ? AND status = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$categoryId, CATEGORY_STATUS_ACTIVE]);
        return (bool)$stmt->fetchColumn();
    }

    public function isCommentApproved(int $commentId): bool {
        $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_COMMENTS_ . " 
                WHERE id = ? AND status = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$commentId, COMMENT_STATUS_APPROVED]);
        return (bool)$stmt->fetchColumn();
    }

    public function isPostSlugUnique(string $slug, ?int $excludePostId = null): bool {
        $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_POSTS_ . " WHERE slug = ?";
        
        if ($excludePostId !== null) {
            $sql .= " AND id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$slug, $excludePostId]);
        } else {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$slug]);
        }
        
        return $stmt->fetchColumn() == 0;
    }

    public function isCategorySlugUnique(string $slug, ?int $excludeCategoryId = null): bool {
        $sql = "SELECT COUNT(*) FROM " . _TBL_WEBENGINE_BLOG_CATEGORIES_ . " WHERE slug = ?";
        
        if ($excludeCategoryId !== null) {
            $sql .= " AND id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$slug, $excludeCategoryId]);
        } else {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$slug]);
        }
        
        return $stmt->fetchColumn() == 0;
    }

    public function generateUniquePostSlug(string $title, ?int $excludePostId = null): string {
        $slug = $this->slugify($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while (!$this->isPostSlugUnique($slug, $excludePostId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    public function generateUniqueCategorySlug(string $name, ?int $excludeCategoryId = null): string {
        $slug = $this->slugify($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (!$this->isCategorySlugUnique($slug, $excludeCategoryId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    private function slugify(string $text): string {
        // Reemplaza caracteres no alfanuméricos con guiones
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Translitera caracteres especiales a ASCII
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Elimina caracteres no deseados
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Elimina guiones al principio y al final
        $text = trim($text, '-');
        // Convierte a minúsculas
        $text = strtolower($text);
        
        return $text ?: 'n-a';
    }
}