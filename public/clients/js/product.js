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
    
    // Afficher le type de vendeur
    const sellerElement = document.getElementById('productSeller');
    if (sellerElement) {
        sellerElement.textContent = `Vendu par : ${p.seller_name}`;
    }
  })
  .catch(() => {
    document.body.innerHTML = 'Erreur chargement produit';
  });

/* ========= BOUTON ACHETER ========= */
document.getElementById('buyBtn').addEventListener('click', async () => {
  if (!currentProduct) return;

  // Vérifier si l'utilisateur est connecté avant d'acheter
  try {
    const response = await fetch('/backend/clients/check_login.php');
    const data = await response.json();
    
    if (!data.logged_in) {
      const confirmLogin = confirm('Vous devez être connecté pour ajouter au panier. Voulez-vous vous connecter ?');
      if (confirmLogin) {
        window.location.href = `login.php?redirect=${encodeURIComponent(window.location.href)}`;
      }
      return;
    }
    
    // Utilisateur connecté, ajouter au panier
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Vérifier si le produit est déjà dans le panier
    const existingIndex = cart.findIndex(item => item.product_id === currentProduct.product_id);
    
    if (existingIndex > -1) {
      cart[existingIndex].quantity += 1;
    } else {
      cart.push({
        product_id: currentProduct.product_id,
        title: currentProduct.title,
        price: currentProduct.price,
        quantity: 1,
        image: currentProduct.image_path,
        seller_id: currentProduct.seller_id,
        seller_type: currentProduct.seller_type
      });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Afficher une notification
    showNotification('Produit ajouté au panier !');
    
    // Rediriger après 1 seconde
    setTimeout(() => {
      window.location.href = 'cart.php';
    }, 1000);
    
  } catch (error) {
    console.error('Erreur vérification connexion:', error);
    alert('Erreur lors de la vérification de connexion');
  }
});

/* ========= CONTACTER LE VENDEUR ========= */
document.getElementById('contactBtn').addEventListener('click', async () => {
  if (!currentProduct) return;

  try {
    // Vérifier la connexion via AJAX
    const response = await fetch('/backend/clients/check_login.php');
    const data = await response.json();
    
    if (!data.logged_in) {
      alert('Veuillez vous connecter pour contacter le vendeur');
      
      // Sauvegarder l'URL de redirection
      const chatUrl = `chat.php?seller=${currentProduct.seller_id}&product=${productId}&seller_type=${currentProduct.seller_type}`;
      localStorage.setItem('redirect_after_login', chatUrl);
      
      window.location.href = `login.php?from=product&id=${productId}&redirect=${encodeURIComponent(chatUrl)}`;
      return;
    }
    
    const clientId = data.user_id;
    
    // Vérifier qu'il ne se contacte pas lui-même (si c'est un client)
    if (currentProduct.seller_type === 'client' && parseInt(clientId) === parseInt(currentProduct.seller_id)) {
      alert('Vous ne pouvez pas vous contacter vous-même');
      return;
    }
    
    // Vérifier si c'est un admin qui contacte un autre admin
    if (data.user_type === 'admin' && currentProduct.seller_type === 'admin') {
      const confirmContact = confirm('Vous êtes administrateur. Voulez-vous vraiment contacter un autre administrateur ?');
      if (!confirmContact) return;
    }
    
    // Rediriger vers le chat
    window.location.href = 
      `chat.php?seller=${currentProduct.seller_id}&product=${productId}&client=${clientId}&seller_type=${currentProduct.seller_type}`;
      
  } catch (error) {
    console.error('Erreur vérification connexion:', error);
    alert('Erreur de vérification de connexion. Veuillez réessayer.');
  }
});

/* ========= FONCTIONS UTILITAIRES ========= */

// Fonction pour afficher une notification
function showNotification(message) {
  // Supprimer les notifications existantes
  const existingNotification = document.querySelector('.notification');
  if (existingNotification) {
    existingNotification.remove();
  }
  
  // Créer la notification
  const notification = document.createElement('div');
  notification.className = 'notification';
  notification.innerHTML = `
    <div class="notification-content">
      <i class="fas fa-check-circle"></i>
      <span>${message}</span>
    </div>
  `;
  
  // Ajouter au DOM
  document.body.appendChild(notification);
  
  // Ajouter les styles si nécessaire
  if (!document.querySelector('#notification-styles')) {
    const styles = document.createElement('style');
    styles.id = 'notification-styles';
    styles.textContent = `
      .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #4CAF50;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s;
        max-width: 300px;
      }
      
      .notification-content {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      
      .notification i {
        font-size: 20px;
      }
      
      @keyframes slideIn {
        from {
          transform: translateX(100%);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
      
      @keyframes fadeOut {
        from {
          opacity: 1;
        }
        to {
          opacity: 0;
        }
      }
    `;
    document.head.appendChild(styles);
  }
  
  // Supprimer après 3 secondes
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 3000);
}

// Fonction pour vérifier le stock en temps réel (optionnel)
function checkStockAvailability() {
  if (!currentProduct || currentProduct.stock_quantity <= 0) return;
  
  // Vérifier périodiquement le stock
  setInterval(async () => {
    try {
      const response = await fetch(`/backend/clients/product_detail.php?id=${productId}`);
      const product = await response.json();
      
      if (product.stock_quantity !== currentProduct.stock_quantity) {
        currentProduct.stock_quantity = product.stock_quantity;
        
        const stockElement = document.getElementById('productStock');
        const buyBtn = document.getElementById('buyBtn');
        
        if (currentProduct.stock_quantity > 0) {
          stockElement.textContent = `Stock : ${currentProduct.stock_quantity}`;
          stockElement.style.color = '#4CAF50';
          buyBtn.disabled = false;
          buyBtn.style.opacity = '1';
        } else {
          stockElement.textContent = 'Rupture de stock';
          stockElement.style.color = '#f44336';
          buyBtn.disabled = true;
          buyBtn.style.opacity = '0.5';
        }
      }
    } catch (error) {
      console.error('Erreur vérification stock:', error);
    }
  }, 30000); // Toutes les 30 secondes
}

// Initialiser après le chargement
setTimeout(checkStockAvailability, 5000);

// Mettre à jour le compteur du panier
function updateCartCounter() {
  const cartCounter = document.getElementById('cartCounter');
  if (cartCounter) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    if (totalItems > 0) {
      cartCounter.textContent = totalItems;
      cartCounter.style.display = 'flex';
    } else {
      cartCounter.style.display = 'none';
    }
  }
}

// Initialiser le compteur
document.addEventListener('DOMContentLoaded', updateCartCounter);

// Écouter les changements de localStorage pour le panier
window.addEventListener('storage', (e) => {
  if (e.key === 'cart') {
    updateCartCounter();
  }
});