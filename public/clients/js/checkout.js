const summaryEl = document.getElementById('orderSummary');
const messageEl = document.getElementById('message');

let cart = localStorage.getItem('cart');
cart = cart ? JSON.parse(cart) : null;

if (!cart) {
  summaryEl.innerHTML = '<p>Panier vide</p>';
  throw new Error('Panier vide');
}

/* ========= AFFICHAGE ========= */
function renderSummary() {
  const total = cart.price * cart.quantity;

  summaryEl.innerHTML = `
    <div class="summary-item">
      <img src="${cart.image}">
      <div>
        <h4>${cart.title}</h4>
        <p>${cart.price} € × ${cart.quantity}</p>
        <strong>Total : ${total.toFixed(2)} €</strong>
      </div>
    </div>
  `;
}

renderSummary();

/* ========= ENVOI COMMANDE ========= */
function submitOrder() {
  const data = {
    product_id: cart.product_id,
    quantity: cart.quantity,
    name: document.getElementById('name').value,
    phone: document.getElementById('phone').value,
    address: document.getElementById('address').value,
    city: document.getElementById('city').value,
    delivery: document.getElementById('delivery').value,
    payment: document.querySelector('input[name="payment"]:checked').value
  };

  fetch('/backend/clients/checkout.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(res => {
    if (!res.success) {
      messageEl.textContent = res.message;
      return;
    }

    localStorage.removeItem('cart');
    window.location.href = 'confirmation.html';
  })
  .catch(() => {
    messageEl.textContent = 'Erreur lors de la commande';
  });
}
