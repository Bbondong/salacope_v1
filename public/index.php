<?php
session_start();
ob_start();

// ============================================
// CONFIGURATION & FONCTIONS UTILITAIRES
// ============================================

/**
 * Détecte si la requête vient d'une application Android WebView
 */
function isAndroidWebView() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Patterns spécifiques à WebView Android
    $patterns = [
        'WebView',                    // Standard WebView
        'Android.*Chrome/[0-9]+\.[0-9]+.*Mobile', // Chrome WebView
        '; wv\)',                     // Android WebView pattern
        'SalaCopeApp',                // Notre app custom
        'com.salacope.marketplace'    // Package name
    ];
    
    foreach ($patterns as $pattern) {
        if (stripos($userAgent, $pattern) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Vérifie si c'est la première visite dans l'app
 */
function isFirstAppVisit() {
    // Vérifier cookie de première visite
    if (!isset($_COOKIE['salacope_first_visit'])) {
        return true;
    }
    
    // Vérifier session localeStorage via JS (fallback)
    if (isset($_SESSION['first_visit_checked']) && $_SESSION['first_visit_checked'] === false) {
        return false;
    }
    
    return null; // Indéterminé, besoin de vérification JS
}

/**
 * Détermine où rediriger l'utilisateur
 */
function getRedirectPath() {
    // 1. Vérifier si l'utilisateur est déjà connecté
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
        return '/dashboard.php'; // Déjà connecté
    }
    
    // 2. Vérifier si c'est une première visite dans l'app mobile
    if (isAndroidWebView()) {
        $firstVisit = isFirstAppVisit();
        
        if ($firstVisit === true) {
            return '/onboarding.php'; // Première visite
        } elseif ($firstVisit === false) {
            return '/auth/login.php'; // Déjà visité, aller au login
        }
        // Si null, on laisse JavaScript décider
    }
    
    // 3. Par défaut pour navigateur web
    return '/auth/login.php'; // Page d'accueil normale
}

/**
 * Redirige avec en-têtes appropriés
 */
function safeRedirect($url) {
    // Nettoyer le buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // URL absolue
    $baseUrl = 'https://' . $_SERVER['HTTP_HOST'];
    $fullUrl = $baseUrl . $url;
    
    // Redirection
    header("Location: $fullUrl", true, 302);
    exit();
}

// ============================================
// LOGIQUE PRINCIPALE
// ============================================

// Déterminer la redirection
$redirectTo = getRedirectPath();

// Si on a une décision claire, rediriger immédiatement
if ($redirectTo !== '/home.php' && !is_null(isFirstAppVisit())) {
    safeRedirect($redirectTo);
}

// Sinon, afficher la page de décision avec JavaScript
?>
<!DOCTYPE html>
<html lang="fr" data-app="salacope-entry">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SalaCope - Marketplace</title>
    <meta name="description" content="Marketplace Camerounaise">
    
    <!-- PWA Configuration -->
    <meta name="theme-color" content="#10B981">
    <link rel="manifest" href="/manifest.json">
    
    <!-- Icons -->
    <link rel="icon" href="/images/favicon.png">
    <link rel="apple-touch-icon" href="/images/icon-192.png">
    
    <!-- Styles -->
    <style>
        /* Styles minimaux pour la page d'entrée */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        
        .container {
            padding: 2rem;
            max-width: 500px;
        }
        
        .logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .loader {
            margin: 2rem auto;
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .message {
            margin-top: 2rem;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .btn-fallback {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.8rem 2rem;
            background: white;
            color: #667eea;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s;
        }
        
        .btn-fallback:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🛍️</div>
        <h1>SalaCope</h1>
        <p>Marketplace Camerounaise</p>
        
        <div class="loader"></div>
        <p>Chargement de l'application...</p>
        
        <!-- Fallback pour navigateurs sans JS -->
        <noscript>
            <div style="margin-top: 2rem; padding: 1rem; background: rgba(255,255,255,0.1); border-radius: 10px;">
                <p>JavaScript est requis pour cette application.</p>
                <a href="/home.php" class="btn-fallback">Accéder au site</a>
            </div>
        </noscript>
        
        <div class="message" id="debugInfo"></div>
    </div>

    <!-- Script de détection et redirection -->
    <script>
    // ============================================
    // DÉTECTION ET REDIRECTION INTELLIGENTE
    // ============================================
    
    (function() {
        'use strict';
        
        // Configuration
        const CONFIG = {
            APP_NAME: 'SalaCope',
            FIRST_VISIT_KEY: 'salacope_first_visit',
            USER_TOKEN_KEY: 'salacope_user_token',
            ONBOARDING_DONE_KEY: 'salacope_onboarding_done',
            REDIRECT_DELAY: 1000, // 1 seconde pour l'animation
            DEBUG: <?php echo isset($_GET['debug']) ? 'true' : 'false'; ?>
        };
        
        // Éléments DOM
        const debugInfo = document.getElementById('debugInfo');
        
        /**
         * Détecte si on est dans une application Android
         */
        function detectAndroidApp() {
            const ua = navigator.userAgent || navigator.vendor || window.opera;
            const isAndroid = /android/i.test(ua);
            const isWebView = /wv\)/.test(ua) || /WebView/.test(ua);
            const isCustomApp = window.SalaCopeApp !== undefined;
            
            // Vérifier les paramètres d'URL (peuvent être passés par l'app)
            const urlParams = new URLSearchParams(window.location.search);
            const isAppParam = urlParams.get('source') === 'android_app';
            
            return {
                isAndroid: isAndroid,
                isWebView: isWebView,
                isCustomApp: isCustomApp,
                isAppParam: isAppParam,
                isMobileApp: (isAndroid && isWebView) || isCustomApp || isAppParam
            };
        }
        
        /**
         * Vérifie si c'est la première visite
         */
        function checkFirstVisit() {
            // 1. Vérifier localStorage
            if (localStorage.getItem(CONFIG.FIRST_VISIT_KEY) === 'done') {
                return false;
            }
            
            // 2. Vérifier sessionStorage (session courante)
            if (sessionStorage.getItem(CONFIG.FIRST_VISIT_KEY) === 'checked') {
                return false;
            }
            
            // 3. Vérifier si l'onboarding a été fait
            if (localStorage.getItem(CONFIG.ONBOARDING_DONE_KEY) === 'true') {
                return false;
            }
            
            // 4. Vérifier les cookies (fallback)
            const cookies = document.cookie.split(';').reduce((acc, cookie) => {
                const [key, value] = cookie.trim().split('=');
                acc[key] = value;
                return acc;
            }, {});
            
            if (cookies[CONFIG.FIRST_VISIT_KEY] === 'done') {
                return false;
            }
            
            // C'est une première visite
            return true;
        }
        
        /**
         * Marquer la première visite comme terminée
         */
        function markFirstVisit() {
            // Marquer dans localStorage
            localStorage.setItem(CONFIG.FIRST_VISIT_KEY, 'done');
            
            // Marquer dans sessionStorage
            sessionStorage.setItem(CONFIG.FIRST_VISIT_KEY, 'checked');
            
            // Définir un cookie (expire dans 1 an)
            const expiryDate = new Date();
            expiryDate.setFullYear(expiryDate.getFullYear() + 1);
            document.cookie = `${CONFIG.FIRST_VISIT_KEY}=done; expires=${expiryDate.toUTCString()}; path=/; samesite=strict`;
            
            // Informer le serveur via une requête silencieuse
            fetch('/api/visit/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ type: 'first_visit' })
            }).catch(() => {
                // Ignorer les erreurs, c'est non critique
            });
        }
        
        /**
         * Vérifier si l'utilisateur a un token valide
         */
        function checkAuthToken() {
            // 1. Vérifier localStorage
            const token = localStorage.getItem(CONFIG.USER_TOKEN_KEY);
            if (token) {
                return validateToken(token);
            }
            
            // 2. Vérifier les cookies
            const cookies = document.cookie.split(';').reduce((acc, cookie) => {
                const [key, value] = cookie.trim().split('=');
                acc[key] = value;
                return acc;
            }, {});
            
            if (cookies[CONFIG.USER_TOKEN_KEY]) {
                return validateToken(cookies[CONFIG.USER_TOKEN_KEY]);
            }
            
            return false;
        }
        
        /**
         * Valider un token (simulé - à remplacer par appel API)
         */
        async function validateToken(token) {
            try {
                const response = await fetch('/api/auth/verify', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                
                return response.ok;
            } catch (error) {
                return false;
            }
        }
        
        /**
         * Déterminer la destination
         */
        async function determineDestination() {
            const detection = detectAndroidApp();
            
            // Afficher les infos de débogage si activé
            if (CONFIG.DEBUG) {
                debugInfo.innerHTML = `
                    UserAgent: ${navigator.userAgent}<br>
                    Is Android: ${detection.isAndroid}<br>
                    Is WebView: ${detection.isWebView}<br>
                    Is Mobile App: ${detection.isMobileApp}
                `;
            }
            
            // Si ce n'est pas l'app mobile, aller à la page d'accueil normale
            if (!detection.isMobileApp) {
                return '/home.php';
            }
            
            // Si c'est l'app mobile
            const isFirstVisit = checkFirstVisit();
            const hasAuth = await checkAuthToken();
            
            // Logique de décision
            if (hasAuth) {
                return '/dashboard.php'; // Déjà connecté
            } else if (isFirstVisit) {
                // Marquer la première visite et aller à l'onboarding
                markFirstVisit();
                return '/onboarding.php';
            } else {
                return '/auth/login.php'; // Pas de première visite, pas connecté
            }
        }
        
        /**
         * Rediriger vers la destination
         */
        async function redirectToDestination() {
            try {
                const destination = await determineDestination();
                
                // Ajouter un délai pour l'expérience utilisateur
                setTimeout(() => {
                    window.location.href = destination;
                }, CONFIG.REDIRECT_DELAY);
                
            } catch (error) {
                console.error('Erreur de redirection:', error);
                // Fallback vers la page d'accueil
                setTimeout(() => {
                    window.location.href = '/home.php';
                }, CONFIG.REDIRECT_DELAY);
            }
        }
        
        /**
         * Initialiser la communication avec l'app Android
         */
        function initAndroidCommunication() {
            // Interface pour l'app Android
            window.SalaCopeInterface = {
                // Méthode appelée par l'app pour savoir si c'est chargé
                pageLoaded: function() {
                    return 'SalaCope Entry Page Loaded';
                },
                
                // Méthode pour obtenir les infos utilisateur
                getUserInfo: function() {
                    return {
                        hasToken: localStorage.getItem(CONFIG.USER_TOKEN_KEY) !== null,
                        firstVisit: localStorage.getItem(CONFIG.FIRST_VISIT_KEY) === null
                    };
                },
                
                // Méthode appelée quand l'app démarre
                appStarted: function() {
                    console.log('App SalaCope démarrée');
                    // L'app peut appeler cette méthode pour déclencher des actions
                }
            };
            
            // Émettre un événement pour informer que l'interface est prête
            window.dispatchEvent(new Event('SalaCopeReady'));
        }
        
        /**
         * Enregistrer le Service Worker pour PWA
         */
        function registerServiceWorker() {
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(registration => {
                        console.log('ServiceWorker enregistré avec succès:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Échec enregistrement ServiceWorker:', error);
                    });
            }
        }
        
        // ============================================
        // EXÉCUTION PRINCIPALE
        // ============================================
        
        // Initialiser
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser la communication Android
            initAndroidCommunication();
            
            // Enregistrer le Service Worker
            registerServiceWorker();
            
            // Démarrer la redirection
            redirectToDestination();
        });
        
        // Gestion des erreurs non capturées
        window.addEventListener('error', function(event) {
            console.error('Erreur JavaScript:', event.error);
            // Fallback vers la page d'accueil en cas d'erreur
            window.location.href = '/home.php';
        });
        
    })();
    </script>
</body>
</html>
<?php
// Nettoyer le buffer
ob_end_flush();
?>