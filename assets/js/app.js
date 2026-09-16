document.addEventListener('DOMContentLoaded', function () {
    autoDismissFlash();
    confirmDangerousLinks();
    disableSubmitOnSend();
});

/**
 * Fait disparaître les messages flash après quelques secondes.
 */
function autoDismissFlash() {
    document.querySelectorAll('.flash').forEach(function (flash) {
        setTimeout(function () {
            flash.classList.add('is-leaving');
            flash.addEventListener('animationend', function () {
                flash.remove();
            }, { once: true });
        }, 4000);
    });
}

/**
 * Remplace les confirm() en ligne par un gestionnaire centralisé
 * sur les liens marqués data-confirm="...".
 */
function confirmDangerousLinks() {
    document.querySelectorAll('[data-confirm]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            if (!window.confirm(link.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
}

/**
 * Désactive le bouton d'envoi d'un formulaire après soumission
 * (évite les doubles clics) et affiche un état "en cours".
 */
function disableSubmitOnSend() {
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            if (!submitButton || submitButton.disabled) {
                return;
            }
            submitButton.dataset.originalLabel = submitButton.value || submitButton.textContent;
            submitButton.disabled = true;

            if (submitButton.tagName === 'INPUT') {
                submitButton.value = 'Veuillez patienter…';
            } else {
                submitButton.textContent = 'Veuillez patienter…';
            }
        });
    });
}
