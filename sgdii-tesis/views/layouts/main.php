<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\models\User;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title) ?> - SGDII</title>
    <?php $this->head() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/site.css">
</head>
<body>
<?php $this->beginBody() ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= Yii::$app->homeUrl ?>">
            <strong>SGDII - Módulo Tesis</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <?php
                    /** @var User $user */
                    $user = Yii::$app->user->identity;
                    $unreadCount = \app\models\Notificacion::getUnreadCount($user->id);
                    ?>
                    <li class="nav-item">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/notification/index']) ?>" 
                           class="nav-link position-relative text-white" 
                           title="Notificaciones"
                           id="notification-bell">
                            <i class="bi bi-bell-fill" style="font-size: 1.2rem;"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                  id="notification-badge" 
                                  style="font-size: 0.7rem; <?= $unreadCount > 0 ? '' : 'display: none;' ?>">
                                <?= $unreadCount ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            <strong><?= Html::encode($user->nombre) ?></strong>
                            <span class="badge bg-light text-primary ms-2"><?= Html::encode(ucfirst($user->rol)) ?></span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline'])
                            . Html::submitButton(
                                'Cerrar Sesión',
                                ['class' => 'btn btn-outline-light']
                            )
                            . Html::endForm() ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main role="main" class="flex-shrink-0">
    <div class="container mt-4">
        <?= $content ?>
    </div>
</main>

<!-- Page Loader -->
<div class="page-loader" id="pageLoader">
    <div class="spinner"></div>
</div>

<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <div class="text-muted text-center">
            <strong>SGDII - Departamento de Ingeniería Industrial</strong> - Prototipo
            <br>
            <small>&copy; <?= date('Y') ?> - Sistema de Gestión de Tesis</small>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Global JavaScript for UI Enhancements -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show page loader on navigation
    const pageLoader = document.getElementById('pageLoader');
    
    // Add loading state to all links (except anchors and logout)
    document.querySelectorAll('a:not([href^="#"]):not([href*="logout"])').forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Only show loader for internal navigation
            if (!link.target || link.target === '_self') {
                pageLoader.classList.add('active');
            }
        });
    });
    
    // Form submission loading state
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.classList.contains('btn-loading')) {
                submitBtn.classList.add('btn-loading');
                submitBtn.disabled = true;
                
                // Store original text
                if (!submitBtn.dataset.originalText) {
                    submitBtn.dataset.originalText = submitBtn.textContent || submitBtn.value;
                }
                
                // Show loading text
                const loadingText = submitBtn.dataset.loadingText || 'Procesando...';
                if (submitBtn.tagName === 'BUTTON') {
                    submitBtn.textContent = loadingText;
                } else {
                    submitBtn.value = loadingText;
                }
                
                // Show page loader for POST requests
                if (form.method.toLowerCase() === 'post') {
                    pageLoader.classList.add('active');
                }
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert:not(.alert-important)').forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Client-side form validation enhancement
    document.querySelectorAll('.form-control, .form-select').forEach(function(field) {
        field.addEventListener('blur', function() {
            if (this.checkValidity && !this.checkValidity()) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') || this.classList.contains('is-valid')) {
                if (this.checkValidity && !this.checkValidity()) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else if (this.value) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-invalid', 'is-valid');
                }
            }
        });
    });
    
    // Confirm dialogs for dangerous actions
    document.querySelectorAll('[data-confirm]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });
    
    // AJAX request handler with loading states
    window.ajaxRequest = function(url, options) {
        options = options || {};
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        const fetchOptions = Object.assign({}, defaultOptions, options);
        
        // Show loader if specified
        if (options.showLoader !== false) {
            pageLoader.classList.add('active');
        }
        
        return fetch(url, fetchOptions)
            .finally(function() {
                if (options.showLoader !== false) {
                    pageLoader.classList.remove('active');
                }
            });
    };
    
    // Toast notification system
    window.showToast = function(message, type) {
        type = type || 'info';
        const alertClass = 'alert-' + type;
        const container = document.querySelector('.container');
        
        if (container) {
            const alert = document.createElement('div');
            alert.className = 'alert ' + alertClass + ' alert-dismissible fade show mt-3';
            alert.setAttribute('role', 'alert');
            alert.innerHTML = message + 
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            
            container.insertBefore(alert, container.firstChild);
            
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    };
    
    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        pageLoader.classList.remove('active');
    });
    
    // Debug mode indicator (only in development)
    <?php if (YII_DEBUG): ?>
    console.log('%c🚀 SGDII Debug Mode Enabled', 'color: #0d6efd; font-weight: bold; font-size: 14px;');
    console.log('Pretty URLs: <?= Yii::$app->urlManager->enablePrettyUrl ? "✓ Enabled" : "✗ Disabled" ?>');
    <?php endif; ?>
});
</script>

</body>
</html>
<?php $this->endPage() ?>
