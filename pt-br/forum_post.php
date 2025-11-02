<?php
require_once 'config.php';
require_once 'forum_functions.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('forum_index.php');

$post = getForumPost($id);
if (!$post) redirect('forum_index.php');

$comments = getForumComments($id);
$title = $post['title'];

// Processar novo comentário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment']) && isLoggedIn()) {
    $content = sanitize($_POST['content']);
    $user_id = getCurrentUser()['id'];
    
    $conn = getDBConnection();
    if ($conn) {
        $stmt = $conn->prepare("INSERT INTO forum_comments (post_id, user_id, content) VALUES (?, ?, ?)");
        if ($stmt->execute([$id, $user_id, $content])) {
            $_SESSION['success'] = 'Comentário adicionado!';
            redirect("forum_post.php?id=$id");
        }
    }
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="h4 mb-2"><?php echo sanitize($post['title']); ?></h1>
                            <div class="d-flex align-items-center text-muted small">
                                <span class="me-3">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo sanitize($post['username']); ?>
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                </span>
                                <span class="badge bg-primary">
                                    <?php echo sanitize($post['category_name']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="content">
                        <?php echo nl2br(sanitize($post['content'])); ?>
                    </div>
                </div>
            </div>

            <!-- Comentários -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Comentários (<?php echo count($comments); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($comments)): ?>
                        <p class="text-muted">Nenhum comentário ainda. Seja o primeiro!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <strong><?php echo sanitize($comment['username']); ?></strong>
                                    <small class="text-muted ms-2">
                                        <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                                    </small>
                                </div>
                                <p class="mb-0"><?php echo nl2br(sanitize($comment['content'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isLoggedIn()): ?>
                        <form method="POST" class="mt-4">
                            <div class="mb-3">
                                <label for="content" class="form-label">Adicionar Comentário</label>
                                <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                            </div>
                            <button type="submit" name="add_comment" class="btn btn-primary">
                                <i class="fas fa-comment"></i> Comentar
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <a href="login.php">Faça login</a> para comentar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Ações</h6>
                </div>
                <div class="card-body">
                    <a href="forum_index.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar ao Fórum
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>