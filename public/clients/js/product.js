const params = new URLSearchParams(window.location.search);
const productId = params.get('id');

if (!productId) {
  document.body.innerHTML = 'Produit introuvable';
  throw new Error('ID produit manquant');
}

fetch(`/backend/clients/product_detail.php?id=${productId}`)
  .then(res => {
    if (!res.ok) throw new Error('Erreur chargement produit');
    return res.json();
  })
  .then(p => {
    document.getElementById('productImage').src = p.image_path;
    document.getElementById('productTitle').textContent = p.title;
    document.getElementById('productPrice').textContent = p.price + ' €';
    document.getElementById('productDescription').textContent = p.description;
  })
  .catch(err => {
    console.error(err);
    document.body.innerHTML = 'Erreur lors du chargement du produit';
  });
