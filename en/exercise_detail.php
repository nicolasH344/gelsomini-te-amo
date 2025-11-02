<?php
require_once 'config.php';
require_once 'exercise_functions.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('exercises_index.php');

$exercise = getExercise($id);
if (!$exercise) redirect('exercises_index.php');

$title = $exercise['title'];

// Processar submissão de código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_code'])) {
    $user_code = $_POST['user_code'] ?? '';
    $feedback = "Código submetido com sucesso! Continue praticando.";
    $_SESSION['success'] = $feedback;
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
                            <h1 class="h4 mb-2"><?php echo sanitize($exercise['title']); ?></h1>
                            <p class="text-muted mb-0"><?php echo sanitize($exercise['description']); ?></p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary"><?php echo sanitize($exercise['category_name']); ?></span>
                            <br>
                            <span class="badge bg-success mt-1">
                                <?php 
                                $difficulty_map = ['beginner' => 'Iniciante', 'intermediate' => 'Intermediário', 'advanced' => 'Avançado'];
                                echo $difficulty_map[$exercise['difficulty_level']] ?? $exercise['difficulty_level'];
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <h5>Instructions:</h5>
                    <div class="alert alert-info">
                        <?php echo nl2br(sanitize($exercise['instructions'])); ?>
                    </div>

                    <?php if ($exercise['hints']): ?>
                        <div class="alert alert-warning">
                            <strong>Hints:</strong><br>
                            <?php echo nl2br(sanitize($exercise['hints'])); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="user_code" class="form-label">Your Code:</label>
                            <textarea class="form-control" id="user_code" name="user_code" rows="15" style="font-family: monospace;"><?php echo sanitize($exercise['initial_code'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" name="submit_code" class="btn btn-success">
                                <i class="fas fa-play"></i> Run Code
                            </button>
                            <button type="button" class="btn btn-info" onclick="showSolution()">
                                <i class="fas fa-lightbulb"></i> Show Solution
                            </button>
                        </div>
                    </form>

                    <div id="solution" class="mt-4" style="display: none;">
                        <h5>Solution:</h5>
                        <pre class="bg-light p-3 rounded"><code><?php echo sanitize($exercise['solution_code'] ?? 'Solution not available'); ?></code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Category:</strong> <?php echo sanitize($exercise['category_name']); ?></p>
                    <p><strong>Difficulty:</strong> 
                        <?php 
                        $difficulty_map = ['beginner' => 'Iniciante', 'intermediate' => 'Intermediário', 'advanced' => 'Avançado'];
                        echo $difficulty_map[$exercise['difficulty_level']] ?? $exercise['difficulty_level'];
                        ?>
                    </p>
                    <p><strong>Type:</strong> <?php echo strtoupper($exercise['exercise_type']); ?></p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <a href="exercises_index.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Exercises
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showSolution() {
    const solution = document.getElementById('solution');
    if (solution.style.display === 'none') {
        solution.style.display = 'block';
    } else {
        solution.style.display = 'none';
    }
}
</script>

<?php include 'footer.php'; ?>