<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Détail du produit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./style/product.css">
</head>
<body>

  <div class="product-detail">

    <div class="product-left">
      <img id="productImage" alt="">
    </div>
  
    <div class="product-content">
      <h1 id="productTitle"></h1>
  
      <p id="productPrice"></p>
  
      <p class="meta">
        <span id="productBrand"></span>
        <span id="productCondition"></span>
      </p>
  
      <p id="productDescription"></p>
  
      <p class="stock" id="productStock"></p>
      <p class="delivery" id="productDelivery"></p>
  
      <div class="actions">
        <button id="buyBtn" class="btn-buy">Acheter</button>
        <button id="contactBtn" class="btn-contact">Contacter le vendeur</button>
      </div>
    </div>
  
  </div>
  
  <script src="./js/product.js"></script>
</body>
</html>
