// assets/js/cart.js
document.addEventListener('DOMContentLoaded', function () {
  const cartButton = document.getElementById('btn-show-cart');
  const cartModal  = document.getElementById('cartModal');
  const cartBody   = document.getElementById('cartModalBody');

  // deteksi lagi di folder apa
  const currentPath   = window.location.pathname || '';
  const inPelanggan   = currentPath.indexOf('/pelanggan/') !== -1;
  const basePath      = inPelanggan ? '' : 'pelanggan/';

  // === load isi modal dari cart_content.php ===
  function loadCartModal() {
    if (!cartBody) return;

    fetch(basePath + 'cart_content.php')
      .then(res => res.text())
      .then(html => {
        cartBody.innerHTML = html;
        attachCartModalEvents();
      })
      .catch(err => console.error('Gagal load cart_content:', err));
  }

  // === pasang event + / - di dalam modal ===
  function attachCartModalEvents() {
    if (!cartBody) return;

    // tombol qty di dalam modal
    cartBody.querySelectorAll('.qty-btn-modal').forEach(btn => {
      btn.addEventListener('click', function () {
        const idMenu = this.dataset.id;
        const action = this.dataset.action;

        fetch(basePath + 'update_cart.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'id_menu=' + encodeURIComponent(idMenu) +
                '&action=' + encodeURIComponent(action)
        })
        .then(res => res.json())
        .then(data => {
          if (!data || !data.success) {
            console.error('Update cart gagal:', data);
            return;
          }

          // update qty
          const qtyLabel   = cartBody.querySelector('.qty-value[data-id="' + idMenu + '"]');
          const meta       = cartBody.querySelector('.cart-item-meta[data-id="' + idMenu + '"]');
          const subtotalEl = cartBody.querySelector('.item-subtotal[data-id="' + idMenu + '"]');
          const totalEl    = document.getElementById('cart-total-display');

          if (data.new_qty > 0) {
            if (qtyLabel) {
              qtyLabel.textContent = data.new_qty;
            }

            if (meta) {
              const price = parseInt(meta.dataset.price, 10) || 0;
              const hargaFormat = price.toLocaleString('id-ID');
              meta.textContent = 'x ' + data.new_qty + ' • Rp ' + hargaFormat;
            }

            if (subtotalEl) {
              const subtotal = data.item_subtotal || 0;
              subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            }
          } else {
            // hapus row item
            const row = cartBody.querySelector('.cart-item[data-row-id="' + idMenu + '"]');
            if (row) row.remove();

            // kalau sudah tidak ada item lagi -> tampilkan empty state
            if (!cartBody.querySelector('.cart-item')) {
              cartBody.innerHTML =
                '<div class="cart-empty-state text-center py-4">' +
                  '<p class="mb-1 fw-semibold">Keranjang kosong</p>' +
                  '<p class="text-muted small mb-0">Silakan pilih dulu menu favoritmu di halaman utama.</p>' +
                '</div>';
            }
          }

          // total di bawah
          if (totalEl) {
            const total = data.cart_total || 0;
            totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
          }

          // badge di floating cart (kalau ada)
          const badge = document.getElementById('cart-count-badge');
          if (badge) {
            const c = data.cart_count || 0;
            badge.textContent = c;

            if (c > 0) {
              badge.classList.remove('d-none');
              badge.classList.add('badge-bump');
              setTimeout(() => badge.classList.remove('badge-bump'), 250);
            } else {
              badge.classList.add('d-none');
            }
          }
        })
        .catch(err => console.error('Error update_cart:', err));
      });
    });


    // === INPUT MANUAL ===
    cartBody.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function () {
            let idMenu = this.dataset.id;
            let newValue = parseInt(this.value) || 0;

            fetch('update_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:
                    'id_menu=' + encodeURIComponent(idMenu) +
                    '&action=set' +
                    '&value=' + encodeURIComponent(newValue)
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                // update tampilan qty
                this.value = data.new_qty;

                // update subtotal
                let subtotalEl = cartBody.querySelector('.item-subtotal[data-id="'+idMenu+'"]');
                if (subtotalEl) {
                    subtotalEl.textContent = 'Rp ' + data.item_subtotal.toLocaleString('id-ID');
                }

                // update total keranjang
                document.getElementById('cart-total-display').textContent =
                    'Rp ' + data.cart_total.toLocaleString('id-ID');

                // update badge
                let badge = document.getElementById('cart-count-badge');
                if (badge) {
                    badge.textContent = data.cart_count;
                    badge.classList.add('badge-bump');
                    setTimeout(()=>badge.classList.remove('badge-bump'), 250);
                }
            });
        });
    });


    // tombol checkout di modal
    const btnCheckout = document.getElementById('btn-modal-checkout');
    if (btnCheckout) {
      btnCheckout.addEventListener('click', function () {
        // kalau sekarang di /pelanggan/, cukup 'checkout.php'
        // kalau di root, arahkan ke 'pelanggan/checkout.php'
        const target = inPelanggan ? 'checkout.php' : (basePath + 'checkout.php');
        window.location.href = target;
      });
    }
    
  }

    // ketika tombol floating cart diklik -> load isi + (Bootstrap yang buka modal)
    if (cartButton && cartModal && cartBody) {
        cartButton.addEventListener('click', function () {
        loadCartModal();
        });
    }

});