<?php 
// Verificar se deve mostrar o banner
$show_banner = false;

// Mostrar se não há consentimento
if (!isset($_COOKIE['lgpd_consent'])) {
    $show_banner = true;
}

// Mostrar se mudou de idioma
if (isset($_SESSION['language_changed']) || 
    (isset($_COOKIE['lgpd_language']) && $_COOKIE['lgpd_language'] !== ($_SESSION['language'] ?? 'pt-br'))) {
    $show_banner = true;
}

if ($show_banner): ?>
<div id="cookieBanner" class="cookie-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <i class="fas fa-cookie-bite fa-2x text-warning me-3"></i>
                    <div>
                        <h6 class="mb-1">Uso de Cookies y Datos</h6>
                        <p class="mb-0 small">
                            Utilizamos cookies esenciales para el funcionamiento de la plataforma y datos personales según nuestra 
                            <a href="lgpd.php" class="text-decoration-underline">Política de Privacidad</a>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <button onclick="acceptCookies()" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-check me-1"></i>Aceptar
                </button>
                <button onclick="showCookieSettings()" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-cog me-1"></i>Configurar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="cookieSettings" class="cookie-settings" style="display: none;">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Configuración de Privacidad</h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="essentialCookies" checked disabled>
                    <label class="form-check-label" for="essentialCookies">
                        <strong>Cookies Esenciales</strong> (Obligatorias)
                        <small class="d-block text-muted">Necesarias para login y funcionamiento básico</small>
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="analyticsCookies">
                    <label class="form-check-label" for="analyticsCookies">
                        <strong>Cookies de Análisis</strong> (Opcionales)
                        <small class="d-block text-muted">Ayudan a mejorar la plataforma</small>
                    </label>
                </div>
                <div class="text-end">
                    <button onclick="saveCookieSettings()" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar Preferencias
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--bg-dark);
    color: var(--text-light);
    padding: 1rem 0;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.cookie-settings {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.8);
    padding: 2rem 0;
    z-index: 1001;
}
</style>

<script>
function acceptCookies() {
    const currentLang = getCurrentLanguage();
    document.cookie = "lgpd_consent=accepted; path=/; max-age=" + (30*60);
    document.cookie = "analytics_consent=true; path=/; max-age=" + (30*60);
    document.cookie = "lgpd_language=" + currentLang + "; path=/; max-age=" + (30*60);
    document.getElementById('cookieBanner').style.display = 'none';
}

function getCurrentLanguage() {
    const path = window.location.pathname;
    if (path.includes('/en/')) return 'en';
    if (path.includes('/es/')) return 'es';
    return 'pt-br';
}

function showCookieSettings() {
    document.getElementById('cookieSettings').style.display = 'block';
}

function saveCookieSettings() {
    const analytics = document.getElementById('analyticsCookies').checked;
    const currentLang = getCurrentLanguage();
    document.cookie = "lgpd_consent=configured; path=/; max-age=" + (30*60);
    document.cookie = "analytics_consent=" + analytics + "; path=/; max-age=" + (30*60);
    document.cookie = "lgpd_language=" + currentLang + "; path=/; max-age=" + (30*60);
    document.getElementById('cookieBanner').style.display = 'none';
    document.getElementById('cookieSettings').style.display = 'none';
}
</script>
<?php endif; ?>