// Formulaire de contact : validation, envoi asynchrone, retour clair (FE-41, FE-42).
import { api } from './api.js';
import { clearErrors, formAlert, formData, setBusy, showErrors, validateForm } from './ui.js';

const form = document.getElementById('contactForm');

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearErrors(form);
    if (!validateForm(form)) {
        return;
    }
    const button = form.querySelector('[type="submit"]');
    setBusy(button, true);
    try {
        const { message } = await api('contact', { method: 'POST', body: formData(form) });
        form.reset();
        formAlert(form, message, 'success');
    } catch (error) {
        // en cas d'erreur, la saisie n'est pas perdue
        showErrors(form, error);
    } finally {
        setBusy(button, false);
    }
});
