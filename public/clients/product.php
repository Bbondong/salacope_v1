<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Détail du produit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SEO minimum -->
  <meta name="description" content="Détail du produit">

  <!-- CSS -->
  <link rel="stylesheet" href="./style/product.css">
</head>
<body>

  <main class="product-detail">

    <!-- IMAGE PRODUIT -->
    <section class="product-left">
      <img
        id="productImage"
        src=""
        alt="Image du produit"
        loading="lazy"
      >
    </section>

    <!-- INFOS PRODUIT -->
    <section class="product-content">

      <h1 id="productTitle"></h1>

      <p id="productPrice" class="price"></p>

      <p class="meta">
        <span id="productBrand"></span>
        <span id="productCondition"></span>
      </p>

      <p id="productDescription" class="description"></p>

      <p id="productStock" class="stock"></p>
      <p id="productDelivery" class="delivery"></p>

      <!-- ACTIONS -->
      <div class="actions">
        <button id="buyBtn" class="btn-buy" type="button">
          Acheter
        </button>

        <button id="contactBtn" class="btn-contact" type="button">
          Contacter le vendeur
        </button>
      </div>

    </section>

  </main>

  <!-- JS (chargé après le DOM) -->
  <script src="./js/product.js" defer></script>
</body>
</html>
