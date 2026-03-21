document.addEventListener('DOMContentLoaded', function () {
    const guestTriggers = document.querySelectorAll('.guest-trigger');

    function showLoginModal() {
        const modalEl = document.getElementById('loginRequiredModal');
        if (!modalEl) return;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    guestTriggers.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            showLoginModal();
        });
    });
});
