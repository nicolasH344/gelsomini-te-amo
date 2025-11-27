<?php
/**
 * TESTE DE NAVEGAÇÃO - Sistema de Interligação de Páginas
 * Este arquivo testa se todas as navegações estão funcionando corretamente
 */

require_once 'config.php';

$title = 'Teste de Navegação';
include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-route me-2"></i>
                        Teste de Navegação - Sistema Interligado
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Teste das Interligações:</strong> Clique nos links abaixo para testar se a navegação está funcionando corretamente.
                    </div>

                    <div class="row g-4">
                        <!-- Tutoriais -->
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-book-open me-2"></i>
                                        Tutoriais
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="tutorials_index.php" class="btn btn-outline-primary">
                                            <i class="fas fa-list me-2"></i>Lista de Tutoriais
                                        </a>
                                        <a href="show.php?type=tutorial&id=1" class="btn btn-outline-primary">
                                            <i class="fas fa-eye me-2"></i>Ver Tutorial #1
                                        </a>
                                        <a href="show.php?type=tutorial&id=2" class="btn btn-outline-primary">
                                            <i class="fas fa-eye me-2"></i>Ver Tutorial #2
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Exercícios -->
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-dumbbell me-2"></i>
                                        Exercícios
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="exercises_index.php" class="btn btn-outline-success">
                                            <i class="fas fa-list me-2"></i>Lista de Exercícios
                                        </a>
                                        <a href="show.php?type=exercise&id=1" class="btn btn-outline-success">
                                            <i class="fas fa-eye me-2"></i>Ver Exercício #1
                                        </a>
                                        <a href="exercise_detail.php?id=1" class="btn btn-success">
                                            <i class="fas fa-play me-2"></i>Iniciar Exercício #1
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fluxo de Navegação -->
                    <div class="mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-sitemap me-2"></i>
                            Fluxo de Navegação Esperado
                        </h5>
                        <div class="alert alert-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Para Tutoriais:</h6>
                                    <ol class="small">
                                        <li><code>tutorials_index.php</code> → Lista todos os tutoriais</li>
                                        <li><code>show.php?type=tutorial&id=X</code> → Mostra detalhes do tutorial</li>
                                        <li>Botão "Ler Tutorial" → Abre o conteúdo completo</li>
                                    </ol>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">Para Exercícios:</h6>
                                    <ol class="small">
                                        <li><code>exercises_index.php</code> → Lista todos os exercícios</li>
                                        <li><code>show.php?type=exercise&id=X</code> → Mostra detalhes do exercício</li>
                                        <li><code>exercise_detail.php?id=X</code> → Editor de código interativo</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status dos Links -->
                    <div class="mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-check-circle me-2"></i>
                            Status das Correções Implementadas
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check me-2"></i>
                                    <strong>exercises_index.php</strong><br>
                                    <small>Botão "Começar" → exercise_detail.php</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check me-2"></i>
                                    <strong>show.php (exercícios)</strong><br>
                                    <small>Botão "Iniciar Exercício" → exercise_detail.php</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check me-2"></i>
                                    <strong>exercise_detail.php</strong><br>
                                    <small>Links de recursos → exercises_index.php</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teste Rápido -->
                    <div class="mt-4 p-3 bg-warning bg-opacity-10 border border-warning rounded">
                        <h6 class="text-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Teste Rápido
                        </h6>
                        <p class="mb-2 small">Para testar rapidamente:</p>
                        <ol class="small mb-0">
                            <li>Clique em "Lista de Exercícios" acima</li>
                            <li>Clique no botão "Começar" de qualquer exercício</li>
                            <li>Verifique se abre o editor de código (exercise_detail.php)</li>
                            <li>Teste os links "Exercícios Práticos" na aba Solução</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar indicadores visuais aos links
    const links = document.querySelectorAll('a[href]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            // Adicionar feedback visual
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Mostrar toast de boas-vindas
    setTimeout(() => {
        showToast('Sistema de navegação carregado! Teste os links acima.', 'info');
    }, 1000);
});

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 4000);
}
</script>

<?php include 'footer.php'; ?>