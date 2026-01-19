let offset = 0;
let loading = false;
let category = null; 

const productGrid = document.getElementById('productGrid');
const categoryList = document.getElementById('categoryList');

// Fonction pour charger les produits
function loadProducts(reset = false) {
  if (loading) return;
  loading = true;

  if (reset) {
    productGrid.innerHTML = '';
    offset = 0;
  }

  fetch(`backend/clients/produit.php?offset=${offset}&category=${category ?? ''}`)
    .then(res => res.json())
    .then(data => {
      if (!data || data.length === 0) {
        loading = false;
        return;
      }

      data.forEach(p => {
        productGrid.innerHTML += `
          <div class="product-card">
            <img src="${p.image_path}" alt="${p.title}">
            <h4>${p.title}</h4>
            <p>${p.price} €</p>
          </div>
        `;
      });

      offset += data.length;
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

// Charger catégories
function loadCategories() {
  fetch('backend/clients/categories.php')
    .then(res => res.json())
    .then(categories => {
      categoryList.innerHTML = '';
      categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.textContent = cat.name;
        btn.addEventListener('click', () => {
          category = cat.id;
          loadProducts(true); // reset pour la catégorie
        });
        categoryList.appendChild(btn);
      });
    });
}

// Initialisation
loadCategories();
loadProducts();
