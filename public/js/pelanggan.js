document.addEventListener('DOMContentLoaded', function () {
    const qtyButtons = document.querySelectorAll('.qty-btn');
    const qtyInputs = document.querySelectorAll('.qty-input');
    const cartBadge = document.getElementById('cart-count-badge');

    function syncQtyDisplay(idMenu, newQty) {
        const span = document.querySelector('.qty-value[data-id="' + idMenu + '"]');
        const input = document.querySelector('.qty-input[data-id="' + idMenu + '"]');

        if (span) span.textContent = newQty;
        if (input) input.value = newQty;
    }

    function updateBadge(cartCount) {
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


    function sendUpdate(idMenu, action, cb) {
        const token = document.querySelector('meta[name="csrf-token"]');

        fetch('/pelanggan/cart/update', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: 'id_menu=' + encodeURIComponent(idMenu) +
                '&action=' + encodeURIComponent(action)
        })
        .then(async (res) => {
            const text = await res.text();

            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Response bukan JSON:', text);
                throw e;
            }
        })
        .then(data => {
            if (!data || !data.success) {
                console.error('Gagal update cart:', data);
                return;
            }

            syncQtyDisplay(idMenu, data.new_qty);
            updateBadge(data.cart_count);

            if (typeof cb === 'function') cb(data);
        })
        .catch(err => console.error('Error update cart:', err));
    }


    qtyButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const idMenu = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            if (!idMenu || !action) return;

            this.disabled = true;
            sendUpdate(idMenu, action, () => {
                this.disabled = false;
            });
        });
    });

    qtyInputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.dataset.prev = this.value;
        });

        input.addEventListener('change', function () {
            const idMenu = this.dataset.id;
            if (!idMenu) return;

            const prev = parseInt(this.dataset.prev || '0', 10) || 0;
            let target = parseInt(this.value, 10);

            if (isNaN(target) || target < 0) target = 0;
            if (target > 99) target = 99;

            this.value = target;

            if (target === prev) return;

            const diff = target - prev;
            const action = diff > 0 ? 'plus' : 'minus';
            const times = Math.abs(diff);

            const maxTimes = Math.min(times, 50);
            let count = 0;

            function step() {
                if (count >= maxTimes) return;
                sendUpdate(idMenu, action, function () {
                    count++;
                    if (count < maxTimes) {
                        step();
                    }
                });
            }

            step();
        });
    });
});

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.qty-btn-modal');
    if (!btn) return;

    const idMenu = btn.dataset.id;
    const action = btn.dataset.action;
    if (!idMenu || !action) return;

    const token = document.querySelector('meta[name="csrf-token"]');
    const row = btn.closest('.cart-item');

    fetch('/pelanggan/cart/update', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: 'id_menu=' + encodeURIComponent(idMenu) +
              '&action=' + encodeURIComponent(action)
    })
    .then(res => res.json())
    .then(data => {
        if (!data || !data.success) {
            console.error('Gagal update cart:', data);
            return;
        }

        // update badge cart
        const badge = document.getElementById('cart-count-badge');
        if (badge) {
            const c = parseInt(data.cart_count || 0, 10);
            badge.textContent = c;

            if (c > 0) {
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        // update total cart
        const totalEl = document.getElementById('cart-total-display');
        if (totalEl) {
            totalEl.textContent = 'Rp ' + Number(data.cart_total || 0).toLocaleString('id-ID');
        }

        // update juga qty di kartu menu utama
        const menuInput = document.querySelector('.qty-input[data-id="' + idMenu + '"]');
        const menuSpan = document.querySelector('.qty-control .qty-value[data-id="' + idMenu + '"]');

        if (menuInput) menuInput.value = data.new_qty;
        if (menuSpan) menuSpan.textContent = data.new_qty;

        // kalau qty jadi 0, hapus row dari modal
        if (parseInt(data.new_qty, 10) === 0) {
            if (row) row.remove();

            // kalau cart sudah kosong, tampilkan empty state
            const cartList = document.querySelector('.cart-modal-list');
            const remainingItems = document.querySelectorAll('.cart-item');

            if (cartList && remainingItems.length === 0) {
                const wrap = document.querySelector('.cart-modal-wrap');
                if (wrap) {
                    wrap.innerHTML = `
                        <div class="cart-empty-state text-center py-4">
                            <p class="mb-1 fw-semibold">Keranjang kosong</p>
                            <p class="text-muted small mb-0">Silakan pilih dulu menu favoritmu di halaman utama.</p>
                        </div>
                    `;
                }
            }

            return;
        }

        // update row yang sedang diklik
        if (row) {
            const qtyEl = row.querySelector('.qty-value[data-id="' + idMenu + '"]');
            if (qtyEl) qtyEl.textContent = data.new_qty;

            const subtotalEl = row.querySelector('.item-subtotal[data-id="' + idMenu + '"]');
            if (subtotalEl) {
                subtotalEl.textContent = 'Rp ' + Number(data.item_subtotal || 0).toLocaleString('id-ID');
            }

            const metaEl = row.querySelector('.cart-item-meta[data-id="' + idMenu + '"]');
            if (metaEl) {
                const price = parseInt(metaEl.dataset.price || '0', 10);
                metaEl.textContent = 'x ' + data.new_qty + ' • Rp ' + price.toLocaleString('id-ID');
            }
        }
    })
    .catch(err => console.error(err));
});