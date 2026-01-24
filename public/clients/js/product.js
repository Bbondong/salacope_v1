const params = new URLSearchParams(window.location.search);
const productId = params.get('id');

if (!productId) {
  document.body.innerHTML = 'Produit introuvable';
  throw new Error('ID manquant');
}

let currentProduct = null;

fetch(`/backend/clients/product_detail.php?id=${productId}`)
  .then(res => res.json())
  .then(p => {
    currentProduct = p;

    document.getElementById('productImage').src = p.image_path;
    document.getElementById('productTitle').textContent = p.title;
    document.getElementById('productPrice').textContent = p.price + ' €';
    document.getElementById('productDescription').textContent = p.description;

    document.getElementById('productBrand').textContent = p.brand;
    document.getElementById('productCondition').textContent = p.product_condition;
    document.getElementById('productStock').textContent =
      p.stock_quantity > 0 ? `Stock : ${p.stock_quantity}` : 'Rupture de stock';

    document.getElementById('productDelivery').textContent =
      p.delivery_available == 1
        ? `Livraison : ${p.delivery_cost} €`
        : 'Pas de livraison';
  })
  .catch(() => {
    document.body.innerHTML = 'Erreur chargement produit';
  });

/* ========= BOUTON ACHETER ========= */
document.getElementById('buyBtn').addEventListener('click', () => {
  if (!currentProduct) return;

  localStorage.setItem('cart', JSON.stringify({
    product_id: currentProduct.product_id,
    title: currentProduct.title,
    price: currentProduct.price,
    quantity: 1,
    image: currentProduct.image_path
  }));

  window.location.href = 'clients/cart.php';
});

/* ========= CONTACTER LE VENDEUR ========= */
document.getElementById('contactBtn').addEventListener('click', () => {
  if (!currentProduct) return;

  window.location.href =
    `clients/chat.php?seller=${currentProduct.seller_id}&product=${currentProduct.product_id}`;
});
