<?php
require_once 'config.php';
$title = 'Política de Privacidade - LGPD';
include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0">Política de Privacidade e Proteção de Dados</h1>
                    <small class="text-muted">Conforme Lei Geral de Proteção de Dados (LGPD)</small>
                </div>
                <div class="card-body mb-0">
                    <h4>1. Dados Coletados</h4>
                    <p>Coletamos apenas os dados necessários para o funcionamento da plataforma:</p>
                    <ul>
                        <li><strong>Dados de cadastro:</strong> Nome, sobrenome, e-mail, senha</li>
                        <li><strong>Dados de uso:</strong> Progresso nos exercícios, participação no fórum</li>
                        <li><strong>Dados técnicos:</strong> IP, cookies de sessão</li>
                    </ul>

                    <h4>2. Finalidade do Tratamento</h4>
                    <ul>
                        <li>Fornecer acesso à plataforma educacional</li>
                        <li>Acompanhar progresso de aprendizagem</li>
                        <li>Melhorar a experiência do usuário</li>
                        <li>Comunicação sobre atualizações da plataforma</li>
                    </ul>

                    <h4>3. Seus Direitos</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li>Confirmação da existência de tratamento</li>
                                <li>Acesso aos dados</li>
                                <li>Correção de dados incompletos</li>
                                <li>Anonimização ou exclusão</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li>Portabilidade dos dados</li>
                                <li>Eliminação dos dados</li>
                                <li>Revogação do consentimento</li>
                                <li>Oposição ao tratamento</li>
                            </ul>
                        </div>
                    </div>

                    <h4>4. Contato do Encarregado</h4>
                    <p>Para exercer seus direitos ou esclarecer dúvidas:</p>
                    <p><strong>E-mail:</strong> lgpd@weblearn.com.br<br>
                    <strong>Prazo de resposta:</strong> até 15 dias úteis</p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Importante:</strong> Você pode gerenciar seus dados e exercer seus direitos através do seu perfil ou entrando em contato conosco.
                    </div>

                    <div class="text-center mt-4">
                        <a href="profile.php" class="btn btn-primary me-2">
                            <i class="fas fa-user-cog me-1"></i>
                            Gerenciar Meus Dados
                        </a>
                        <a href="index.php" class="btn btn-outline-primary">
                            <i class="fas fa-home me-1"></i>
                            Voltar ao Início
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>