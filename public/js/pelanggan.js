// Move helper functions and state outside to be accessible
function syncQtyDisplay(idMenu, newQty) {
    const spans = document.querySelectorAll('.qty-value[data-id="' + idMenu + '"]');
    const inputs = document.querySelectorAll('.qty-input[data-id="' + idMenu + '"]');

    spans.forEach(span => span.textContent = newQty);
    inputs.forEach(input => input.value = newQty);
}

function updateBadge(cartCount) {
    const cartBadge = document.getElementById('cart-count-badge');
    if (!cartBadge) return;
    const c = parseInt(cartCount || 0, 10);
    cartBadge.textContent = c;

    if (c > 0) {
        cartBadge.classList.remove('d-none');
        cartBadge.classList.add('badge-bump');
        setTimeout(() => cartBadge.classList.remove('badge-bump'), 250);
    } else {
        cartBadge.classList.add('d-none');
    }
}

function sendUpdate(idMenu, action, cb, value) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || window.location.origin;
    
    const formData = new FormData();
    formData.append('id_menu', idMenu);
    formData.append('action', action);
    if (action === 'set' && value !== undefined) {
        formData.append('value', value);
    }

    const url = (baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl) + '/pelanggan/cart/update';

    fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': token || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(data => {
        if (data && data.success) {
            syncQtyDisplay(idMenu, data.new_qty);
            updateBadge(data.cart_count);
            
            const totalEl = document.getElementById('cart-total-display');
            if (totalEl && data.cart_total !== undefined) {
                totalEl.textContent = 'Rp ' + Number(data.cart_total).toLocaleString('id-ID');
            }

            if (parseInt(data.new_qty, 10) === 0) {
                const row = document.querySelector('.cart-item[data-row-id="' + idMenu + '"]');
                if (row) row.remove();
                if (!document.querySelector('.cart-item')) {
                    const wrap = document.querySelector('.cart-modal-wrap');
                    if (wrap) wrap.innerHTML = '<div class="cart-empty-state text-center py-4"><p class="mb-1 fw-semibold">Keranjang kosong</p></div>';
                }
            } else {
                const row = document.querySelector('.cart-item[data-row-id="' + idMenu + '"]');
                if (row) {
                    const subtotalEl = row.querySelector('.item-subtotal');
                    if (subtotalEl) subtotalEl.textContent = 'Rp ' + Number(data.item_subtotal).toLocaleString('id-ID');
                    const metaEl = row.querySelector('.cart-item-meta');
                    if (metaEl) {
                        const price = parseInt(metaEl.dataset.price || '0', 10);
                        metaEl.textContent = 'x ' + data.new_qty + ' • Rp ' + price.toLocaleString('id-ID');
                    }
                }
            }
        }
        if (typeof cb === 'function') cb(data);
    })
    .catch(err => {
        console.error('MFC Cart Error:', err);
        if (typeof cb === 'function') cb({ success: false, error: err });
    });
}

// Global Delegation for Clicks (Always Active)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.qty-btn, .qty-btn-modal');
    if (!btn) return;

    e.preventDefault();

    const idMenu = btn.getAttribute('data-id');
    const action = btn.getAttribute('data-action');
    if (!idMenu || !action) return;

    btn.disabled = true;
    btn.style.opacity = '0.5';

    sendUpdate(idMenu, action, () => {
        btn.disabled = false;
        btn.style.opacity = '1';
    });
});

// Qty Input Handlers
document.addEventListener('change', function (e) {
    const input = e.target.closest('.qty-input');
    if (!input) return;

    const idMenu = input.dataset.id;
    if (!idMenu) return;

    const prev = parseInt(input.dataset.prev || '0', 10);
    let target = parseInt(input.value, 10);
    if (isNaN(target) || target < 0) target = 0;
    input.value = target;

    if (target === prev) return;

    input.disabled = true;
    sendUpdate(idMenu, 'set', (data) => {
        input.disabled = false;
        input.dataset.prev = (data && data.success) ? data.new_qty : prev;
    }, target);
});

document.addEventListener('focusin', function (e) {
    const input = e.target.closest('.qty-input');
    if (input) input.dataset.prev = input.value;
});