<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();

// Configurar headers para download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=backup_' . date('Y-m-d_H-i-s') . '.csv');

$output = fopen('php://output', 'w');

// Cabeçalho
fputcsv($output, ['Tabela', 'Dados']);

// Exportar dados principais
$tables = [
    'users' => 'SELECT id, username, first_name, last_name, is_admin, created_at FROM users',
    'user_progress' => 'SELECT user_id, content_id, content_type, status, score, progress_percent FROM user_progress',
    'user_achievements' => 'SELECT user_id, achievement_type, earned_at FROM user_achievements'
];

foreach ($tables as $table => $query) {
    $result = $conn->query($query);
    
    fputcsv($output, ["=== $table ==="]);
    
    if ($result && $result->num_rows > 0) {
        // Cabeçalhos das colunas
        $fields = $result->fetch_fields();
        $headers = [];
        foreach ($fields as $field) {
            $headers[] = $field->name;
        }
        fputcsv($output, $headers);
        
        // Dados
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }
    
    fputcsv($output, ['']); // Linha vazia
}

fclose($output);
?>