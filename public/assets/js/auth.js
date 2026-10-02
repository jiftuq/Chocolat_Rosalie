// Connexion, inscription et déconnexion dans des fenêtres modales (FE-50 à FE-53),
// sans rechargement de page (FE-60). L'état connecté est diffusé à toute la page
// par l'événement « auth:change ».
import { api } from './api.js';
import { clearErrors, formData, setBusy, showErrors, toast, validateForm } from './ui.js';

function applyAuthState(user) {
    document.querySelectorAll('[data-auth]').forEach((block) => {
        block.hidden = (block.dataset.auth === 'user') !== Boolean(user);
    });
    document.querySelectorAll('[data-username]').forEach((element) => {
        element.textContent = user ? user.username : '';
    });
    document.dispatchEvent(new CustomEvent('auth:change', { detail: { user } }));
}

function handleForm(formId, route) {
    const form = document.getElementById(formId);
    if (!form) {
        return;
    }
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);
        if (!validateForm(form)) {
            return;
        }
        const button = form.querySelector('[type="submit"]');
        setBusy(button, true);
        try {
            const { message, data } = await api(route, { method: 'POST', body: formData(form) });
            bootstrap.Modal.getOrCreateInstance(form.closest('.modal')).hide();
            form.reset();
            applyAuthState(data.user);
            toast(message);
        } catch (error) {
            showErrors(form, error);
        } finally {
            setBusy(button, false);
        }
    });
}

handleForm('loginForm', 'auth/login');
handleForm('registerForm', 'auth/register');

document.querySelectorAll('[data-logout]').forEach((button) => {
    button.addEventListener('click', async () => {
        setBusy(button, true);
        try {
            const { message } = await api('auth/logout', { method: 'POST' });
            applyAuthState(null);
            toast(message);
        } catch (error) {
            toast(error.message, 'error');
        } finally {
            setBusy(button, false);
        }
    });
});

// Remise à zéro des erreurs à la réouverture d'une modale
document.querySelectorAll('#loginModal, #registerModal').forEach((modal) => {
    modal.addEventListener('show.bs.modal', () => clearErrors(modal.querySelector('form')));
});
