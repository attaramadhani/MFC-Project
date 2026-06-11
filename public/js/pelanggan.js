// Debounce timers and pending quantities in memory
const debounceTimers = {};
const pendingQuantities = {};

// Helper to display a custom toast alert programmatically
function showMfcToast(message, type = 'success') {
    // Remove existing toast wrapper if any
    const existing = document.getElementById('mfcToastWrap');
    if (existing) {
        existing.remove();
    }

    const wrap = document.createElement('div');
    wrap.className = 'mfc-toast-wrap';
    wrap.id = 'mfcToastWrap';

    let iconSvg = '';
    if (type === 'error') {
        iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
    } else if (type === 'info') {
        iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
    } else {
        iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
    }

    wrap.innerHTML = `
        <div class="mfc-toast mfc-toast--${type}" id="mfcToast">
            <div class="mfc-toast-icon">
                ${iconSvg}
            </div>
            <div class="mfc-toast-msg">${message}</div>
            <button class="mfc-toast-close" onclick="document.getElementById('mfcToastWrap').remove()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="mfc-toast-progress" id="mfcToastProgress" style="width: 100%;"></div>
        </div>
    `;

    document.body.appendChild(wrap);

    const progress = document.getElementById('mfcToastProgress');
    setTimeout(() => wrap.classList.add('show'), 50);
    progress.style.transition = 'width 4s linear';
    setTimeout(() => progress.style.width = '0%', 100);
    setTimeout(() => {
        wrap.classList.remove('show');
        setTimeout(() => wrap.remove(), 400);
    }, 4200);
}

// Helper to get current quantity from UI, relative to a target element if provided
function getCurrentQty(idMenu, relativeEl) {
    if (pendingQuantities[idMenu] !== undefined) {
        return pendingQuantities[idMenu];
    }
    if (relativeEl) {
        const qtyControl = relativeEl.closest('.qty-control');
        if (qtyControl) {
            const input = qtyControl.querySelector('.qty-input');
            if (input) return parseInt(input.value, 10) || 0;
            const span = qtyControl.querySelector('.qty-value');
            if (span) return parseInt(span.textContent, 10) || 0;
        }
    }
    const input = document.querySelector('.qty-input[data-id="' + idMenu + '"]');
    if (input) {
        return parseInt(input.value, 10) || 0;
    }
    const span = document.querySelector('.qty-value[data-id="' + idMenu + '"]');
    if (span) {
        return parseInt(span.textContent, 10) || 0;
    }
    return 0;
}

// Helper to get max stock boundary from container, relative to a target element if provided
function getMaxStok(idMenu, relativeEl) {
    if (relativeEl) {
        const qtyControl = relativeEl.closest('.qty-control');
        if (qtyControl) {
            const maxVal = parseInt(qtyControl.getAttribute('data-max'), 10);
            if (!isNaN(maxVal)) return maxVal;
        }
    }
    const qtyControl = document.querySelector('.qty-control[data-id="' + idMenu + '"]');
    if (qtyControl) {
        const maxVal = parseInt(qtyControl.getAttribute('data-max'), 10);
        if (!isNaN(maxVal)) return maxVal;
    }
    const input = document.querySelector('.qty-input[data-id="' + idMenu + '"]');
    if (input) {
        const maxVal = parseInt(input.getAttribute('max'), 10);
        if (!isNaN(maxVal)) return maxVal;
    }
    return 999999; // Fallback large number
}

// Synchronizes quantity UI elements
function syncQtyDisplay(idMenu, newQty) {
    const spans = document.querySelectorAll('.qty-value[data-id="' + idMenu + '"]');
    const inputs = document.querySelectorAll('.qty-input[data-id="' + idMenu + '"]');

    spans.forEach(span => span.textContent = newQty);
    inputs.forEach(input => input.value = newQty);
}

// Updates the cart badge count in navigation
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

// Sends cart AJAX updates to the Laravel backend
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
        } else {
            // Show error toast message
            if (data && data.message) {
                showMfcToast(data.message, 'error');
            }
            // Sync/revert to the last valid quantity
            if (data && data.current_qty !== undefined) {
                syncQtyDisplay(idMenu, data.current_qty);
            }
        }
        if (typeof cb === 'function') cb(data);
    })
    .catch(err => {
        console.error('MFC Cart Error:', err);
        showMfcToast('Terjadi kesalahan koneksi.', 'error');
        if (typeof cb === 'function') cb({ success: false, error: err });
    });
}

// Global Delegation for Clicks (Instantly Updates and Debounces)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.qty-btn, .qty-btn-modal');
    if (!btn) return;

    e.preventDefault();

    const idMenu = btn.getAttribute('data-id');
    const action = btn.getAttribute('data-action');
    if (!idMenu || !action) return;

    const currentQty = getCurrentQty(idMenu, btn);
    const maxStok = getMaxStok(idMenu, btn);
    let targetQty = currentQty;

    if (action === 'plus') {
        if (currentQty >= maxStok) {
            showMfcToast('Stok tidak mencukupi. Hanya tersisa ' + maxStok + ' porsi.', 'error');
            return;
        }
        targetQty = currentQty + 1;
    } else if (action === 'minus') {
        if (currentQty <= 0) {
            return;
        }
        targetQty = currentQty - 1;
    }

    // Instantly update UI (Optimistic update)
    pendingQuantities[idMenu] = targetQty;
    syncQtyDisplay(idMenu, targetQty);

    // Debounce the server request
    if (debounceTimers[idMenu]) {
        clearTimeout(debounceTimers[idMenu]);
    }

    debounceTimers[idMenu] = setTimeout(() => {
        sendUpdate(idMenu, 'set', (data) => {
            delete debounceTimers[idMenu];
            if (data && data.success) {
                pendingQuantities[idMenu] = data.new_qty;
            } else {
                if (data && data.current_qty !== undefined) {
                    pendingQuantities[idMenu] = data.current_qty;
                } else {
                    delete pendingQuantities[idMenu];
                    syncQtyDisplay(idMenu, getCurrentQty(idMenu, btn));
                }
            }
        }, targetQty);
    }, 350);
});

// Qty Input Handlers (Clamping manual inputs immediately on input)
document.addEventListener('input', function (e) {
    const input = e.target.closest('.qty-input');
    if (!input) return;

    const idMenu = input.dataset.id;
    if (!idMenu) return;

    const maxStok = getMaxStok(idMenu, input);
    let val = parseInt(input.value, 10);
    if (!isNaN(val)) {
        if (val < 0) {
            input.value = 0;
        } else if (val > maxStok) {
            showMfcToast('Stok tidak mencukupi. Hanya tersisa ' + maxStok + ' porsi.', 'error');
            input.value = maxStok;
        }
    }
});

// Qty Input Handlers (Save on change/blur with stock protection)
document.addEventListener('change', function (e) {
    const input = e.target.closest('.qty-input');
    if (!input) return;

    const idMenu = input.dataset.id;
    if (!idMenu) return;

    const prev = parseInt(input.dataset.prev || '0', 10);
    const maxStok = getMaxStok(idMenu, input);
    let target = parseInt(input.value, 10);
    if (isNaN(target) || target < 0) target = 0;
    if (target > maxStok) target = maxStok;

    input.value = target;

    if (target === prev) return;

    pendingQuantities[idMenu] = target;
    syncQtyDisplay(idMenu, target);

    if (debounceTimers[idMenu]) {
        clearTimeout(debounceTimers[idMenu]);
    }

    input.disabled = true;
    sendUpdate(idMenu, 'set', (data) => {
        input.disabled = false;
        if (data && data.success) {
            input.dataset.prev = data.new_qty;
            pendingQuantities[idMenu] = data.new_qty;
        } else {
            input.dataset.prev = prev;
            if (data && data.current_qty !== undefined) {
                pendingQuantities[idMenu] = data.current_qty;
            } else {
                delete pendingQuantities[idMenu];
                syncQtyDisplay(idMenu, getCurrentQty(idMenu, input));
            }
        }
    }, target);
});

document.addEventListener('focusin', function (e) {
    const input = e.target.closest('.qty-input');
    if (input) input.dataset.prev = input.value;
});