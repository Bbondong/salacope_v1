<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/css/login.css">
    <title>Login S'alacop</title>
</head>
<body>
    <section>
        <!-- Formulaire de connexion -->
        <div class="connexion">
            <form action="#" method="post">
                <img src="../assets/img_log/logo.jpg" alt="logo app">
                <h2>Connexion</h2>
                <br>
                <label for="">Num ou Username</label>
                <input type="text" required name="user">
                <label for="">Mot de passe</label>
                <input type="password" required name="pass">
                <input type="submit" value="Connexion">
            </form>
            <a href="./mot_passe.php">Mot des passe oublie</a>
            <p>Pas des compte <a href="./inscription.php">Inscrivez-vous</a></p>
            <p> © COPYRIGhHT SALACOPE || create by <a href="#">Ben tech</a></p>
        </div>
        <!-- Image illustration -->
        <div class="illustration"></div>
    </section>
    <script src="../style/js/login.js"></script>
</body>
</html>