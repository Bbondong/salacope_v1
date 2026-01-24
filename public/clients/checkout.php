<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Validation de commande</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./style/checkout.css">
</head>
<body>

<div class="checkout-container">
  <h1>Validation de la commande</h1>

  <!-- RÉCAP PRODUIT -->
  <div class="box">
    <h2>Produit</h2>
    <div id="orderSummary"></div>
  </div>

  <!-- INFORMATIONS CLIENT -->
  <div class="box">
    <h2>Informations client</h2>
    <input type="text" id="name" placeholder="Nom complet" required>
    <input type="tel" id="phone" placeholder="Téléphone" required>
    <input type="text" id="address" placeholder="Adresse" required>
    <input type="text" id="city" placeholder="Ville" required>
  </div>

  <!-- LIVRAISON -->
  <div class="box">
    <h2>Livraison</h2>
    <select id="delivery">
      <option value="0">Retrait sur place</option>
      <option value="1">Livraison à domicile</option>
    </select>
  </div>

  <!-- PAIEMENT -->
  <div class="box">
    <h2>Méthode de paiement</h2>
    <label><input type="radio" name="payment" value="cash" checked> Paiement à la livraison</label>
    <label><input type="radio" name="payment" value="mobile_money"> Mobile Money</label>
  </div>

  <button class="btn-primary" onclick="submitOrder()">Confirmer la commande</button>

  <p id="message"></p>
</div>

<script src="./js/checkout.js"></script>
</body>
</html>
