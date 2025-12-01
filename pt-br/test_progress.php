<?php
require_once 'config.php';

if (!isLoggedIn()) {
    echo "Faça login primeiro!";
    exit;
}

$user_id = getCurrentUser()['id'];
echo "<h2>Teste do Sistema de Progresso</h2>";
echo "<p>Usuário logado: ID $user_id</p>";

// Testar salvamento de exercício
echo "<h3>Testando Exercício</h3>";
echo "<button onclick=\"saveExerciseProgress(1)\">Completar Exercício 1</button>";
echo "<button onclick=\"checkExerciseProgress(1)\">Ver Progresso Exercício 1</button>";

// Testar salvamento de tutorial
echo "<h3>Testando Tutorial</h3>";
echo "<button onclick=\"saveTutorialProgress(1)\">Completar Tutorial 1</button>";
echo "<button onclick=\"checkTutorialProgress(1)\">Ver Progresso Tutorial 1</button>";

echo "<div id='result'></div>";
?>

<script>
function saveExerciseProgress(id) {
    fetch('save_progress.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'type=exercise&item_id=' + id + '&status=completed&score=10'
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('result').innerHTML = 'Exercício: ' + JSON.stringify(data);
    });
}

function saveTutorialProgress(id) {
    fetch('save_progress.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'type=tutorial&item_id=' + id + '&status=completed&progress=100'
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('result').innerHTML = 'Tutorial: ' + JSON.stringify(data);
    });
}

function checkExerciseProgress(id) {
    // Verificar no banco via PHP
    fetch('check_progress.php?type=exercise&id=' + id)
    .then(response => response.text())
    .then(data => {
        document.getElementById('result').innerHTML = 'Progresso Exercício: ' + data;
    });
}

function checkTutorialProgress(id) {
    fetch('check_progress.php?type=tutorial&id=' + id)
    .then(response => response.text())
    .then(data => {
        document.getElementById('result').innerHTML = 'Progresso Tutorial: ' + data;
    });
}
</script>