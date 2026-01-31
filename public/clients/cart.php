<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Panier</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./style/cart.css">
</head>
<body>

<div class="cart-container">
  <h1>Mon panier</h1>

  <div id="cartItem"></div>

  <div class="cart-summary">
    <span>Total :</span>
    <strong id="totalPrice">0 €</strong>
  </div>

  <div class="cart-actions">
    <button class="btn-secondary" onclick="history.back()">Retour</button>
    <button class="btn-primary" onclick="goCheckout()">Commander</button>
  </div>
</div>

<script src="./js/cart.js"></script>
</body>
</html>
