<?php
session_start();

// Si déjà connecté, rediriger vers le dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SalaCope</title>
    <style>
        .auth-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .logo {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .links {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">🛍️</div>
        <h2>Connexion</h2>
        
        <form id="loginForm">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Mot de passe" class="form-control" required>
            </div>
            <button type="submit" class="btn-login">Se connecter</button>
        </form>
        
        <div class="links">
            <p>Pas encore de compte ? <a href="/auth/register.php">S'inscrire</a></p>
            <p><a href="/auth/forgot.php">Mot de passe oublié ?</a></p>
        </div>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Stocker le token
            localStorage.setItem('salacope_user_token', result.token);
            
            // Rediriger vers le dashboard
            window.location.href = '/dashboard.php';
        } else {
            alert(result.message || 'Erreur de connexion');
        }
    });
    </script>
</body>
</html>