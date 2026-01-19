let offset = 0;
let loading = false;
let category = null; 
let activeCategoryBtn = null;

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

  fetch(`/backend/clients/produit.php?offset=${offset}&category=${category ?? ''}`)
    .then(res => {
      if (!res.ok) throw new Error('Erreur lors du chargement des produits');
      return res.json();
    })
    .then(data => {
      if (!data || data.length === 0) {
        loading = false;
        return;
      }

      data.forEach(p => {
        // Construire chemin absolu de l'image si besoin
        let imgSrc = p.image_path;
        if (!imgSrc.startsWith('http')) {
          imgSrc = `/clients/${p.image_path.replace(/^\/?/, '')}`;
        }

        productGrid.innerHTML += `
          <div class="product-card">
            <img src="${imgSrc}" alt="${p.title}">
            <div class="product-info">
              <h4>${p.title}</h4>
              <p class="price-tag">${p.price} €</p>
            </div>
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
  fetch('/backend/clients/categories.php')
    .then(res => {
      if (!res.ok) throw new Error('Erreur lors du chargement des catégories');
      return res.json();
    })
    .then(categories => {
      categoryList.innerHTML = '';
      categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.textContent = cat.name;

        btn.addEventListener('click', () => {
          category = cat.id;
          loadProducts(true); // reset produits

          // Gestion du bouton actif
          if (activeCategoryBtn) activeCategoryBtn.classList.remove('active');
          btn.classList.add('active');
          activeCategoryBtn = btn;
        });

        categoryList.appendChild(btn);
      });
    })
    .catch(err => console.error(err));
}

// Filtre par recherche
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('input', () => {
  const term = searchInput.value.toLowerCase();
  const cards = document.querySelectorAll('.product-card');
  cards.forEach(card => {
    const title = card.querySelector('h4').textContent.toLowerCase();
    card.style.display = title.includes(term) ? 'block' : 'none';
  });
});

// Initialisation
loadCategories();
loadProducts();
