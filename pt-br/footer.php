    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5" role="contentinfo">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="text-gradient fw-bold mb-3">
                        <i class="fas fa-code me-2" aria-hidden="true"></i>
                        <?php echo t('site_title'); ?>
                    </h5>
                    <p class="fw-semibold mb-3">
                        <?php echo t('footer_description', 'Plataforma interativa para aprender desenvolvimento web com exercícios práticos, tutoriais detalhados e uma comunidade ativa de desenvolvedores.'); ?>
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="fw-semibold mb-3" aria-label="Facebook" title="Facebook">
                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="fw-semibold mb-3" aria-label="Twitter" title="Twitter">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="fw-semibold mb-3" aria-label="LinkedIn" title="LinkedIn">
                            <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="fw-semibold mb-3" aria-label="GitHub" title="GitHub">
                            <i class="fab fa-github" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="fw-semibold mb-3" aria-label="YouTube" title="YouTube">
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-semibold mb-3"><?php echo t('learning', 'Aprendizado'); ?></h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="exercises_index.php" class="fw-semibold mb-3">
                                <?php echo t('exercises'); ?>
                            </a>
                        </li>
                        <li class="mb-2"> 
                            <a href="tutorials_index.php" class="fw-semibold mb-3">
                                <?php echo t('tutorials'); ?>
                            </a>
                        </li> 
                        <li class="mb-2">
                            <a href="forum_index.php" class="fw-semibold mb-3">
                                <?php echo t('forum'); ?>
                            </a>
                        </li> 
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('challenges', 'Desafios'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-semibold mb-3"><?php echo t('resources', 'Recursos'); ?></h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('documentation', 'Documentação'); ?>
                            </a>
                        </li>
                        <li class="mb-2"> 
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('api', 'API'); ?>
                            </a>
                        </li> 
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('blog', 'Blog'); ?>
                            </a>
                        </li> 
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('newsletter', 'Newsletter'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-semibold mb-3"><?php echo t('support', 'Suporte'); ?></h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('help_center', 'Central de Ajuda'); ?>
                            </a>
                        </li>
                        <li class="mb-2"> 
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('contact', 'Contato'); ?>
                            </a>
                        </li> 
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('faq', 'FAQ'); ?>
                            </a>
                        </li> 
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('feedback', 'Feedback'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-semibold mb-3"><?php echo t('legal', 'Legal'); ?></h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="lgpd.php" class="fw-semibold mb-3">
                                <?php echo t('privacy_policy', 'Política de Privacidade'); ?>
                            </a>
                        </li>
                        <li class="mb-2"> 
                            <a href="#" class="fw-semibold mb-3>
                                <?php echo t('terms_of_service', 'Termos de Serviço'); ?>
                            </a>
                        </li> 
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('cookies', 'Cookies'); ?>
                            </a>
                        </li> 
                        <li class="mb-2">
                            <a href="#" class="fw-semibold mb-3">
                                <?php echo t('accessibility_statement', 'Declaração de Acessibilidade'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="fw-semibold mb-3">
                        &copy; <?php echo date('Y'); ?> <?php echo t('site_title'); ?>. 
                        <?php echo t('all_rights_reserved', 'Todos os direitos reservados.'); ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex justify-content-md-end gap-3 mt-3 mt-md-0">
                        <span class="fw-semibold mb-3">
                            <i class="fas fa-heart text-danger me-1" aria-hidden="true"></i>
                            <?php echo t('made_with_love', 'Feito com amor'); ?>
                        </span>
                        <span class="fw-semibold mb-3">
                            <i class="fas fa-universal-access me-1" aria-hidden="true"></i>
                            <?php echo t('accessible_design', 'Design Acessível'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botão Voltar ao Topo -->
    <button id="backToTop" class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle" 
            style="width: 50px; height: 50px; display: none; z-index: 1000;" 
            onclick="scrollToTop()" 
            aria-label="<?php echo t('back_to_top', 'Voltar ao topo'); ?>"
            title="<?php echo t('back_to_top', 'Voltar ao topo'); ?>">
        <i class="fas fa-arrow-up" aria-hidden="true"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Botão voltar ao topo
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Animações de entrada
        function animateOnScroll() {
            const elements = document.querySelectorAll('.card, .alert, .badge');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-up');
                    }
                });
            }, {
                threshold: 0.1
            });

            elements.forEach(element => {
                observer.observe(element);
            });
        }

        // Melhorias de acessibilidade
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar animações
            animateOnScroll();
            
            // Melhorar navegação por teclado
            const focusableElements = document.querySelectorAll(
                'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
            );
            
            // Adicionar indicadores visuais de foco
            focusableElements.forEach(element => {
                element.addEventListener('focus', function() {
                    this.style.outline = '2px solid var(--primary-color)';
                    this.style.outlineOffset = '2px';
                });
                
                element.addEventListener('blur', function() {
                    this.style.outline = '';
                    this.style.outlineOffset = '';
                });
            });

            // Anunciar mudanças de página para leitores de tela
            const pageTitle = document.title;
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'visually-hidden';
            announcement.textContent = `<?php echo t('page_loaded', 'Página carregada'); ?>: ${pageTitle}`;
            document.body.appendChild(announcement);

            // Remover anúncio após 3 segundos
            setTimeout(() => {
                if (announcement.parentNode) {
                    announcement.parentNode.removeChild(announcement);
                }
            }, 3000);

            // Configurar tooltips do Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Configurar popovers do Bootstrap
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        });

        // Função para detectar preferências de movimento reduzido
        function respectMotionPreferences() {
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            
            if (prefersReducedMotion.matches) {
                // Desabilitar animações para usuários que preferem movimento reduzido
                const style = document.createElement('style');
                style.textContent = `
                    *, *::before, *::after {
                        animation-duration: 0.01ms !important;
                        animation-iteration-count: 1 !important;
                        transition-duration: 0.01ms !important;
                        scroll-behavior: auto !important;
                    }
                `;
                document.head.appendChild(style);
            }
        }

        // Aplicar preferências de movimento
        respectMotionPreferences();

        // Função para melhorar contraste em modo de alto contraste
        function handleHighContrast() {
            if (window.matchMedia('(prefers-contrast: high)').matches) {
                document.body.classList.add('high-contrast-mode');
            }
        }

        // Aplicar modo de alto contraste se necessário
        handleHighContrast();

        // Função para anunciar mudanças dinâmicas
        function announceChange(message) {
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.className = 'visually-hidden';
            announcement.textContent = message;
            document.body.appendChild(announcement);
            
            setTimeout(() => {
                if (announcement.parentNode) {
                    announcement.parentNode.removeChild(announcement);
                }
            }, 1000);
        }

        // Função para validação de formulários acessível
        function setupAccessibleValidation() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                const inputs = form.querySelectorAll('input, textarea, select');
                
                inputs.forEach(input => {
                    input.addEventListener('invalid', function(e) {
                        e.preventDefault();
                        
                        // Criar mensagem de erro acessível
                        let errorMessage = this.validationMessage;
                        
                        // Personalizar mensagens em português
                        if (this.validity.valueMissing) {
                            errorMessage = '<?php echo t("field_required", "Este campo é obrigatório"); ?>';
                        } else if (this.validity.typeMismatch) {
                            if (this.type === 'email') {
                                errorMessage = '<?php echo t("invalid_email", "Por favor, insira um email válido"); ?>';
                            }
                        } else if (this.validity.tooShort) {
                            errorMessage = `<?php echo t("min_length", "Mínimo de"); ?> ${this.minLength} <?php echo t("characters", "caracteres"); ?>`;
                        }
                        
                        // Anunciar erro
                        announceChange(errorMessage);
                        
                        // Focar no campo com erro
                        this.focus();
                    });
                });
            });
        }

        // Configurar validação acessível
        setupAccessibleValidation();
    </script>

    <!-- Estilos adicionais para acessibilidade -->
    <style>
        /* Modo de alto contraste */
        .high-contrast-mode {
            filter: contrast(150%);
        }

        /* Melhorias para foco */
        *:focus {
            outline: 2px solid var(--primary-color) !important;
            outline-offset: 2px !important;
        }

        /* Indicadores visuais para elementos interativos */
        button, a, input, textarea, select {
            transition: all 0.2s ease;
        }

        button:hover, a:hover {
            transform: translateY(-1px);
        }

        /* Melhorias para leitores de tela */
        .visually-hidden-focusable:not(:focus):not(:focus-within) {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        /* Animação suave para mudanças de tema */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        /* Melhorias para dispositivos móveis */
        @media (max-width: 768px) {
            .btn {
                min-height: 44px;
                min-width: 44px;
            }
            
            .nav-link {
                padding: 0.75rem 1rem;
            }
        }

        /* Indicadores para modo acessibilidade */
        .accessibility-mode .btn::after {
            content: '';
            position: absolute;
            top: 2px;
            right: 2px;
            width: 6px;
            height: 6px;
            background: currentColor;
            border-radius: 50%;
            opacity: 0.7;
        }
    </style>

</body>
</html>
