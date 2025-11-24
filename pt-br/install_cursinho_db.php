<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalar Banco 'cursinho' - WebLearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h1 class="h3 mb-0">🗄️ Instalar Banco de Dados 'cursinho'</h1>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                            <?php
                            $host = 'localhost';
                            $user = 'root';
                            $pass = $_POST['password'] ?? '';
                            
                            try {
                                // Conectar ao MySQL
                                $pdo = new PDO("mysql:host=$host", $user, $pass);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Ler e executar script SQL
                                $sql = file_get_contents('../database/weblearn_complete.sql');
                                $pdo->exec($sql);
                                
                                echo '<div class="alert alert-success">
                                    <h5>✅ Banco "cursinho" instalado com sucesso!</h5>
                                    <ul>
                                        <li>8 exercícios criados</li>
                                        <li>5 badges configurados</li>
                                        <li>4 categorias de conteúdo</li>
                                        <li>Usuário admin: admin/admin123</li>
                                    </ul>
                                    <a href="index.php" class="btn btn-success mt-2">Acessar Site</a>
                                </div>';
                                
                            } catch (PDOException $e) {
                                echo '<div class="alert alert-danger">
                                    <h5>❌ Erro na instalação:</h5>
                                    <p>' . htmlspecialchars($e->getMessage()) . '</p>
                                </div>';
                            }
                            ?>
                        <?php else: ?>
                        
                        <div class="alert alert-info">
                            <h5>📋 Instruções:</h5>
                            <ol>
                                <li>Certifique-se de que o XAMPP está rodando (Apache + MySQL)</li>
                                <li>Digite a senha do MySQL (geralmente vazia ou 'Home@spSENAI2025!')</li>
                                <li>Clique em "Instalar Banco"</li>
                            </ol>
                        </div>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha do MySQL:</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       value="Home@spSENAI2025!" placeholder="Deixe vazio se não tiver senha">
                                <div class="form-text">
                                    Senha padrão: Home@spSENAI2025! (ou deixe vazio)
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-database"></i> Instalar Banco 'cursinho'
                            </button>
                        </form>
                        
                        <hr>
                        
                        <h6>🔧 O que será criado:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li>✅ Banco 'cursinho'</li>
                                    <li>✅ Tabela de usuários</li>
                                    <li>✅ Sistema de exercícios</li>
                                    <li>✅ Sistema de progresso</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li>✅ Sistema de badges</li>
                                    <li>✅ Sistema de feedback</li>
                                    <li>✅ Dados de exemplo</li>
                                    <li>✅ Usuário admin</li>
                                </ul>
                            </div>
                        </div>
                        
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-outline-primary">
                                Voltar ao Site
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>