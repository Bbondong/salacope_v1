<?php
// Important pour le SEO et les navigateurs
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>404 - Page introuvable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .container {
            text-align: center;
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            font-size: 120px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 10px;
            color: #ff4c4c;
        }

        h2 {
            font-size: 26px;
            margin-bottom: 15px;
        }

        p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        a {
            display: inline-block;
            padding: 12px 28px;
            background: #ff4c4c;
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        a:hover {
            background: #ff1f1f;
            transform: translateY(-2px);
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 80px;
            }
            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>404</h1>
        <h2>Oups ! Page introuvable</h2>
        <p>
            La page que vous recherchez n’existe pas ou a été déplacée.
        </p>
        <a href="/index.php">Retour à l’accueil</a>
    </div>

</body>
</html>
