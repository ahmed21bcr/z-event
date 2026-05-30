document.addEventListener('DOMContentLoaded', function () {

// ===== 1. NAVBAR ANIMATION AU SCROLL =====
const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', function () {
    if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 4px 25px rgba(233, 69, 96, 0.6)';
        navbar.style.borderBottom = '2px solid rgba(233, 69, 96, 0.8)';
        navbar.style.transition = 'all 0.3s ease';
    } else {
        navbar.style.boxShadow = 'none';
        navbar.style.borderBottom = '2px solid var(--accent)';
    }
});

    // ===== 3. MESSAGES DE SUCCÈS QUI DISPARAISSENT =====
    const successAlerts = document.querySelectorAll('.alert-success-zevent');
    successAlerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 500);
        }, 3000);
    });

    // ===== 4. CONFIRMATION DÉCONNEXION =====
    const deconnexionLinks = document.querySelectorAll('a[href*="deconnexion"]');
    deconnexionLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                window.location.href = this.href;
            }
        });
    });

    // ===== 5. VALIDATION FORMULAIRES CÔTÉ CLIENT =====
    const forms = document.querySelectorAll('form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;

            requiredFields.forEach(function (field) {
                field.style.border = '';
                if (!field.value.trim()) {
                    field.style.border = '2px solid #ff0000';
                    valid = false;
                }
            });

            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(function (field) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    field.style.border = '2px solid #ff0000';
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });
    });
});