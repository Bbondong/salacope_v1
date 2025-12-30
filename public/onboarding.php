<?php
session_start();

// Marquer que l'onboarding est en cours
$_SESSION['onboarding_started'] = true;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur SalaCope</title>
    <style>
        .onboarding {
            max-width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        .slide {
            display: none;
            height: 100vh;
            padding: 2rem;
            text-align: center;
            flex-direction: column;
            justify-content: center;
        }
        .slide.active {
            display: flex;
        }
        .slide-icon {
            font-size: 4rem;
            margin-bottom: 2rem;
        }
        .slide h2 {
            margin-bottom: 1rem;
        }
        .indicators {
            position: fixed;
            bottom: 100px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ccc;
        }
        .indicator.active {
            background: #10B981;
        }
        .btn-skip {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: transparent;
            border: none;
            color: #666;
            cursor: pointer;
        }
        .btn-next {
            position: fixed;
            bottom: 40px;
            right: 20px;
            padding: 15px 30px;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="onboarding">
        <!-- Slide 1 -->
        <div class="slide active" id="slide1">
            <div class="slide-icon">🛍️</div>
            <h2>Bienvenue sur SalaCope</h2>
            <p>La marketplace camerounaise pour tous vos besoins</p>
        </div>
        
        <!-- Slide 2 -->
        <div class="slide" id="slide2">
            <div class="slide-icon">💰</div>
            <h2>Achetez en toute sécurité</h2>
            <p>Transactions sécurisées avec Mobile Money</p>
        </div>
        
        <!-- Slide 3 -->
        <div class="slide" id="slide3">
            <div class="slide-icon">🚀</div>
            <h2>Vendez facilement</h2>
            <p>Publiez vos produits en quelques clics</p>
        </div>
        
        <!-- Slide 4 (Inscription) -->
        <div class="slide" id="slide4">
            <div class="slide-icon">👋</div>
            <h2>Commençons !</h2>
            <p>Créez votre compte ou connectez-vous</p>
            <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 1rem;">
                <button onclick="finishOnboarding('register')" style="padding: 15px; background: #10B981; color: white; border: none; border-radius: 10px;">
                    Créer un compte
                </button>
                <button onclick="finishOnboarding('login')" style="padding: 15px; background: transparent; color: #10B981; border: 2px solid #10B981; border-radius: 10px;">
                    Se connecter
                </button>
            </div>
        </div>
        
        <button class="btn-skip" onclick="skipOnboarding()">Passer</button>
        <button class="btn-next" onclick="nextSlide()">Suivant</button>
        
        <div class="indicators">
            <span class="indicator active"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
            <span class="indicator"></span>
        </div>
    </div>

    <script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.indicator');
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        slides[index].classList.add('active');
        indicators[index].classList.add('active');
        currentSlide = index;
    }
    
    function nextSlide() {
        if (currentSlide < slides.length - 1) {
            showSlide(currentSlide + 1);
        } else {
            finishOnboarding();
        }
    }
    
    function skipOnboarding() {
        finishOnboarding('skip');
    }
    
    function finishOnboarding(action = 'skip') {
        // Marquer l'onboarding comme terminé
        localStorage.setItem('salacope_onboarding_done', 'true');
        
        // Envoyer au serveur
        fetch('/api/onboarding/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: action })
        });
        
        // Rediriger selon l'action
        if (action === 'register') {
            window.location.href = '/auth/register.php';
        } else if (action === 'login') {
            window.location.href = '/auth/login.php';
        } else {
            window.location.href = '/auth/login.php';
        }
    }
    
    // Navigation tactile
    let startX = 0;
    
    document.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    });
    
    document.addEventListener('touchend', (e) => {
        const endX = e.changedTouches[0].clientX;
        const diff = startX - endX;
        
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                nextSlide(); // Swipe gauche
            } else {
                if (currentSlide > 0) showSlide(currentSlide - 1); // Swipe droit
            }
        }
    });
    
    // Initialiser
    showSlide(0);
    </script>
</body>
</html>