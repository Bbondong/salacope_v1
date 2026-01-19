let offset = 0;
let loading = false;
let category = null; // changer si tu veux filtrer par catégorie

function loadProducts() {
  if (loading) return;
  loading = true;

  fetch(`backend/clients/produit.php?offset=${offset}&category=${category ?? ''}`)
    .then(response => {
      if (!response.ok) throw new Error("Erreur lors du chargement des produits");
      return response.json();
    })
    .then(data => {
      if (!data || data.length === 0) {
        loading = false;
        return; // plus de produits
      }

      const grid = document.getElementById('productGrid');
      data.forEach(p => {
        const imagePath = p.image_path.startsWith('http') ? p.image_path : p.image_path;
        grid.innerHTML += `
          <div class="product-card">
            <img src="${imagePath}" alt="${p.title}">
            <h4>${p.title}</h4>
            <p>${p.price} €</p>
          </div>
        `;
      });

      offset += data.length; // incrément offset
      loading = false;
    })
    .catch(err => {
      console.error(err);
      loading = false;
    });
}

// Scroll infini
window.addEventListener('scroll', () => {
  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
    loadProducts();
  }
});

// Chargement initial
loadProducts();
