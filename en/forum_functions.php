<?php
require_once 'config.php';

function getForumPosts($category = '', $search = '', $page = 1, $perPage = 10) {
    $conn = getDBConnection();
    if (!$conn) return [];
    
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT fp.*, u.username, fc.name as category_name,
            (SELECT COUNT(*) FROM forum_comments WHERE post_id = fp.id) as replies
            FROM forum_posts fp 
            LEFT JOIN users u ON fp.user_id = u.id
            LEFT JOIN forum_categories fc ON fp.category_id = fc.id
            WHERE 1=1";
    $params = [];
    
    if ($category) {
        $sql .= " AND fc.name = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $sql .= " AND (fp.title LIKE ? OR fp.content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY fp.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getForumCategories() {
    $conn = getDBConnection();
    if (!$conn) return [
        ['id' => 1, 'name' => 'HTML', 'color' => 'danger'],
        ['id' => 2, 'name' => 'CSS', 'color' => 'primary'],
        ['id' => 3, 'name' => 'JavaScript', 'color' => 'warning'],
        ['id' => 4, 'name' => 'PHP', 'color' => 'info']
    ];
    
    $stmt = $conn->query("SELECT * FROM forum_categories ORDER BY name");
    return $stmt->fetchAll();
}

function createForumPost($title, $content, $category_id, $user_id) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    $stmt = $conn->prepare("INSERT INTO forum_posts (title, content, category_id, user_id) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$title, $content, $category_id, $user_id]);
}

function getForumPost($id) {
    $conn = getDBConnection();
    if (!$conn) return null;
    
    $stmt = $conn->prepare("SELECT fp.*, u.username, fc.name as category_name 
                           FROM forum_posts fp 
                           LEFT JOIN users u ON fp.user_id = u.id
                           LEFT JOIN forum_categories fc ON fp.category_id = fc.id
                           WHERE fp.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getForumComments($post_id) {
    $conn = getDBConnection();
    if (!$conn) return [];
    
    $stmt = $conn->prepare("SELECT fc.*, u.username 
                           FROM forum_comments fc 
                           LEFT JOIN users u ON fc.user_id = u.id
                           WHERE fc.post_id = ? 
                           ORDER BY fc.created_at ASC");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll();
}
?>