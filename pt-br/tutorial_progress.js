function startTutorial(tutorialId) {
    fetch('save_progress.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'type=tutorial&item_id=' + tutorialId + '&status=started&progress=0'
    });
}

function completeTutorial(tutorialId) {
    if (!confirm('Marcar este tutorial como concluído?')) return;
    
    fetch('save_progress.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'type=tutorial&item_id=' + tutorialId + '&status=completed&progress=100'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Tutorial concluído!');
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    });
}