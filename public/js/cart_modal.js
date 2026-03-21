document.addEventListener('DOMContentLoaded', function () {
    const cartModal = document.getElementById('cartModal');
    const cartModalBody = document.getElementById('cartModalBody');

    if (!cartModal || !cartModalBody) return;

    cartModal.addEventListener('show.bs.modal', function () {
        cartModalBody.innerHTML = `
            <div class="text-center text-muted py-3 small">
                Memuat keranjang...
            </div>
        `;

        fetch('/pelanggan/cart/content', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(res => res.text())
        .then(html => {
            cartModalBody.innerHTML = html;
        })
        .catch(err => {
            console.error('Gagal load cart:', err);
            cartModalBody.innerHTML = `
                <div class="text-center text-danger py-3 small">
                    Gagal memuat keranjang.
                </div>
            `;
        });
    });
});

document.addEventListener('click', function (e) {
    const btnCheckout = e.target.closest('#btn-modal-checkout');
    if (!btnCheckout) return;

    window.location.href = 'pelanggan/checkout';
});