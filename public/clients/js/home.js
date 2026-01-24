let offset = 0;
let loading = false;
let category = null; 
let activeCategoryBtn = null;

const productGrid = document.getElementById('productGrid');
const categoryList = document.getElementById('categoryList');
const searchInput = document.getElementById('searchInput');

/* ============================
   CHARGEMENT DES PRODUITS
============================ */
function loadProducts(reset = false) {
  if (loading) return;
  loading = true;

  if (reset) {
    productGrid.innerHTML = '';
    offset = 0;
  }

  fetch(`/backend/clients/produit.php?offset=${offset}&category=${category ?? ''}`)
    .then(res => {
      if (!res.ok) throw new Error('Erreur chargement produits');
      return res.json();
    })
    .then(data => {
      if (!data || data.length === 0) {
        loading = false;
        return;
      }

      data.forEach(p => {
        let imgSrc = p.image_path;
        if (!imgSrc.startsWith('http')) {
          imgSrc = `/clients/${p.image_path.replace(/^\/?/, '')}`;
        }

        productGrid.innerHTML += `
          <div class="product-card" data-id="${p.product_id}">
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

/* ============================
   CLIC SUR UN PRODUIT
============================ */
productGrid.addEventListener('click', (e) => {
  const card = e.target.closest('.product-card');
  if (!card) return;

  const productId = card.dataset.id;
  if (!productId) return;

  window.location.href = `/clients/product.html?id=${productId}`;
});

/* ============================
   SCROLL INFINI
============================ */
window.addEventListener('scroll', () => {
  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
    loadProducts();
  }
});

/* ============================
   CHARGEMENT DES CATÉGORIES
============================ */
function loadCategories() {
  fetch('/backend/clients/categories.php')
    .then(res => {
      if (!res.ok) throw new Error('Erreur chargement catégories');
      return res.json();
    })
    .then(categories => {
      categoryList.innerHTML = '';

      categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.textContent = cat.name;

        btn.addEventListener('click', () => {
          category = cat.id;
          loadProducts(true);

          if (activeCategoryBtn) activeCategoryBtn.classList.remove('active');
          btn.classList.add('active');
          activeCategoryBtn = btn;
        });

        categoryList.appendChild(btn);
      });
    })
    .catch(err => console.error(err));
}

/* ============================
   RECHERCHE LOCALE
============================ */
searchInput.addEventListener('input', () => {
  const term = searchInput.value.toLowerCase();
  const cards = document.querySelectorAll('.product-card');

  cards.forEach(card => {
    const title = card.querySelector('h4').textContent.toLowerCase();
    card.style.display = title.includes(term) ? 'block' : 'none';
  });
});

/* ============================
   INITIALISATION
============================ */
loadCategories();
loadProducts();
