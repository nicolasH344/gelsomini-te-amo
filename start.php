<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebLearn - Inicialização</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-rocket me-2"></i>
                            WebLearn - Sistema Iniciado
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Sucesso!</strong> O sistema está funcionando corretamente.
                        </div>
                        
                        <h5>Próximos passos:</h5>
                        <div class="list-group">
                            <a href="localhost_setup.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-cog me-2"></i>
                                <strong>1. Configurar Sistema</strong>
                                <small class="d-block text-muted">Verificar e configurar banco de dados</small>
                            </a>
                            <a href="test.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-flask me-2"></i>
                                <strong>2. Executar Testes</strong>
                                <small class="d-block text-muted">Testar PHP e conexão MySQL</small>
                            </a>
                            <a href="pt-br/" class="list-group-item list-group-item-action">
                                <i class="fas fa-home me-2"></i>
                                <strong>3. Acessar Site</strong>
                                <small class="d-block text-muted">Ir para a página principal</small>
                            </a>
                        </div>
                        
                        <hr>
                        
                        <h6>Informações do Sistema:</h6>
                        <ul class="list-unstyled">
                            <li><strong>PHP:</strong> <?php echo phpversion(); ?></li>
                            <li><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido'; ?></li>
                            <li><strong>Data/Hora:</strong> <?php echo date('d/m/Y H:i:s'); ?></li>
                        </ul>
                        
                        <div class="mt-4">
                            <h6>URLs Importantes:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body p-3">
                                            <h6 class="card-title">Desenvolvimento</h6>
                                            <small>
                                                <a href="http://localhost/gelsomini-te-amo/" target="_blank">
                                                    http://localhost/gelsomini-te-amo/
                                                </a>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body p-3">
                                            <h6 class="card-title">Login Padrão</h6>
                                            <small>
                                                Usuário: <strong>admin</strong><br>
                                                Senha: <strong>admin123</strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>