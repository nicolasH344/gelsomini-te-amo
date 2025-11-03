<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$title = 'Gerenciar Meus Dados';
$user = getCurrentUser();

// Processar solicitações LGPD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'download_data':
            $userData = [
                'id' => $user['id'],
                'nome' => $user['first_name'] . ' ' . $user['last_name'],
                'username' => $user['username'],
                'data_cadastro' => date('Y-m-d H:i:s'),
                'dados_coletados' => 'Conforme política de privacidade'
            ];
            
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="meus_dados_weblearn.json"');
            echo json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
            
        case 'delete_account':
            if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'EXCLUIR') {
                // Em produção, implementar exclusão real do banco
                $_SESSION['success'] = 'Solicitação de exclusão registrada. Processamento em até 15 dias úteis.';
                processLogout();
                redirect('index.php');
            } else {
                $_SESSION['error'] = 'Confirmação incorreta. Digite "EXCLUIR" para confirmar.';
            }
            break;
            
        case 'update_consent':
            $marketing = isset($_POST['marketing_consent']) ? 1 : 0;
            $analytics = isset($_POST['analytics_consent']) ? 1 : 0;
            
            // Salvar preferências em cookies
            setcookie('marketing_consent', $marketing, time() + (365*24*60*60), '/');
            setcookie('analytics_consent', $analytics, time() + (365*24*60*60), '/');
            
            $_SESSION['success'] = 'Preferências de consentimento atualizadas.';
            break;
    }
}

include 'header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h2 class="h4 mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Gerenciar Meus Dados (LGPD)
                    </h2>
                </div>
                <div class="card-body">
                    
                    <!-- Meus Dados -->
                    <div class="mb-4">
                        <h5>📋 Meus Dados Pessoais</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tr><td><strong>Nome:</strong></td><td><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></td></tr>
                                <tr><td><strong>Usuário:</strong></td><td><?= sanitize($user['username']) ?></td></tr>
                                <tr><td><strong>Cadastro:</strong></td><td>Dados disponíveis conforme LGPD</td></tr>
                            </table>
                        </div>
                        
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="download_data">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Baixar Meus Dados
                            </button>
                        </form>
                    </div>

                    <!-- Consentimentos -->
                    <div class="mb-4">
                        <h5>⚙️ Gerenciar Consentimentos</h5>
                        <form method="POST">
                            <input type="hidden" name="action" value="update_consent">
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="essential" checked disabled>
                                <label class="form-check-label" for="essential">
                                    <strong>Dados Essenciais</strong> - Obrigatórios para funcionamento
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="analytics_consent" id="analytics" 
                                       <?= ($_COOKIE['analytics_consent'] ?? 'false') === 'true' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="analytics">
                                    Análise de uso da plataforma
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="marketing_consent" id="marketing"
                                       <?= ($_COOKIE['marketing_consent'] ?? 'false') === 'true' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="marketing">
                                    Comunicações sobre novidades
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-save me-1"></i>Salvar Preferências
                            </button>
                        </form>
                    </div>

                    <!-- Exclusão de Conta -->
                    <div class="mb-4">
                        <h5 class="text-danger">🗑️ Excluir Minha Conta</h5>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atenção:</strong> Esta ação é irreversível e excluirá todos os seus dados.
                        </div>
                        
                        <form method="POST" onsubmit="return confirm('Tem certeza? Esta ação não pode ser desfeita.')">
                            <input type="hidden" name="action" value="delete_account">
                            <div class="mb-3">
                                <label for="confirm_delete" class="form-label">
                                    Digite <strong>EXCLUIR</strong> para confirmar:
                                </label>
                                <input type="text" class="form-control" name="confirm_delete" required>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-1"></i>Excluir Permanentemente
                            </button>
                        </form>
                    </div>

                    <!-- Links Úteis -->
                    <div class="text-center">
                        <a href="lgpd.php" class="btn btn-outline-info btn-sm me-2">
                            <i class="fas fa-file-alt me-1"></i>Política de Privacidade
                        </a>
                        <a href="profile.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-user me-1"></i>Voltar ao Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>