const cartEl = document.getElementById('cartItem');
const totalEl = document.getElementById('totalPrice');

let cart = JSON.parse(localStorage.getItem('cart'));

if (!cart) {
  cartEl.innerHTML = '<p>Panier vide</p>';
} else {
  renderCart();
}

function renderCart() {
  const total = cart.price * cart.quantity;

  cartEl.innerHTML = `
    <div class="cart-item">
      <img src="${cart.image}">
      <div class="info">
        <h3>${cart.title}</h3>
        <p>${cart.price} € / unité</p>

        <div class="qty">
          <button onclick="changeQty(-1)">−</button>
          <span>${cart.quantity}</span>
          <button onclick="changeQty(1)">+</button>
        </div>
      </div>
      <strong>${total.toFixed(2)} €</strong>
    </div>
  `;

  totalEl.textContent = total.toFixed(2) + ' €';
}

function changeQty(delta) {
  cart.quantity += delta;
  if (cart.quantity < 1) cart.quantity = 1;

  localStorage.setItem('cart', JSON.stringify(cart));
  renderCart();
}

function goCheckout() {
  window.location.href = '/clients/checkout.php';
}
