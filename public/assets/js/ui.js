// Petits outils d'interface partagés : retours visuels, validation, étoiles.

const assetUrl = (path) => `assets/${path}`;

/** Notification temporaire (succès / erreur) — FE-62. */
export function toast(message, type = 'success') {
    const container = document.getElementById('toasts');
    const element = document.createElement('div');
    element.className = `toast align-items-center border-0 text-bg-${type === 'error' ? 'danger' : 'success'}`;
    element.setAttribute('role', type === 'error' ? 'alert' : 'status');

    const wrapper = document.createElement('div');
    wrapper.className = 'd-flex';
    const body = document.createElement('div');
    body.className = 'toast-body';
    body.textContent = message;
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'btn-close btn-close-white me-2 m-auto';
    close.setAttribute('data-bs-dismiss', 'toast');
    close.setAttribute('aria-label', 'Fermer');
    wrapper.append(body, close);
    element.append(wrapper);

    container.append(element);
    element.addEventListener('hidden.bs.toast', () => element.remove());
    new bootstrap.Toast(element, { delay: 4000 }).show();
}

/** Bouton en cours d'envoi : désactivé (pas de double clic) avec indicateur. */
export function setBusy(button, busy) {
    if (!button) {
        return;
    }
    if (busy) {
        button.dataset.label = button.innerHTML;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Envoi…';
    } else {
        button.disabled = false;
        button.removeAttribute('aria-busy');
        if (button.dataset.label) {
            button.innerHTML = button.dataset.label;
        }
    }
}

export function formAlert(form, message, type = 'danger') {
    const alert = form.querySelector('[data-form-alert]');
    if (!alert) {
        return;
    }
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.classList.toggle('d-none', !message);
}

function setFieldError(field, message) {
    const feedback = field.closest('.mb-3, .mb-2, [class*="col-"]')?.querySelector('.invalid-feedback');
    field.classList.toggle('is-invalid', Boolean(message));
    field.setAttribute('aria-invalid', message ? 'true' : 'false');
    if (feedback) {
        feedback.textContent = message || '';
        if (!feedback.id) {
            feedback.id = `${field.id}-error`;
        }
        const described = (field.getAttribute('aria-describedby') || '').split(' ').filter((id) => id && id !== feedback.id);
        if (message) {
            described.push(feedback.id);
        }
        field.setAttribute('aria-describedby', described.join(' '));
    }
}

export function clearErrors(form) {
    form.querySelectorAll('.is-invalid').forEach((field) => setFieldError(field, ''));
    formAlert(form, '');
}

/** Erreurs renvoyées par le backend, affichées près des champs (FE-41, FE-52). */
export function showErrors(form, error) {
    const errors = error.errors || {};
    let firstField = null;
    Object.entries(errors).forEach(([name, message]) => {
        const field = form.elements[name];
        if (field) {
            setFieldError(field, message);
            firstField ??= field;
        }
    });
    if (Object.keys(errors).length === 0 || !firstField) {
        formAlert(form, error.message);
    }
    firstField?.focus();
}

const messages = {
    required: 'Ce champ est obligatoire.',
    email: 'Saisissez une adresse e-mail valide (ex. : nom@domaine.be).',
    username: 'Lettres, chiffres, « . », « - » ou « _ » uniquement.',
    password: 'Le mot de passe doit contenir au moins une lettre et un chiffre.',
    match: 'Les deux mots de passe ne correspondent pas.',
};

/**
 * Validation côté client (FE-61) : champs requis, formats, longueurs.
 * Elle améliore l'expérience mais le serveur revalide toujours tout.
 */
export function validateForm(form) {
    let firstInvalid = null;
    Array.from(form.elements).forEach((field) => {
        if (!field.name || field.type === 'radio' || field.closest('.hp-field')) {
            return;
        }
        const value = field.type === 'password' ? field.value : field.value.trim();
        const min = Number(field.getAttribute('minlength') || 0);
        const max = Number(field.getAttribute('maxlength') || 0);
        let error = '';

        if (field.required && value === '') {
            error = messages.required;
        } else if (value !== '') {
            if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                error = messages.email;
            } else if ((min && value.length < min) || (max && value.length > max)) {
                error = min ? `Entre ${min} et ${max} caractères (actuellement ${value.length}).` : `${max} caractères maximum.`;
            } else if (field.dataset.rule === 'username' && !/^[\p{L}0-9_.-]+$/u.test(value)) {
                error = messages.username;
            } else if (field.dataset.rule === 'password' && !(/\p{L}/u.test(value) && /\d/.test(value))) {
                error = messages.password;
            }
        }
        if (!error && field.dataset.match && value !== document.getElementById(field.dataset.match).value) {
            error = messages.match;
        }

        setFieldError(field, error);
        if (error && !firstInvalid) {
            firstInvalid = field;
        }
    });
    firstInvalid?.focus();
    return firstInvalid === null;
}

export function formData(form) {
    const data = {};
    new FormData(form).forEach((value, key) => {
        data[key] = value;
    });
    return data;
}

/** Étoiles de la note moyenne (affichage seul). */
export function renderStars(value) {
    const wrapper = document.createElement('span');
    wrapper.className = 'stars';
    wrapper.setAttribute('aria-hidden', 'true');
    const rounded = Math.round((Number(value) || 0) * 2) / 2;
    for (let i = 1; i <= 5; i += 1) {
        const img = document.createElement('img');
        img.width = 18;
        img.height = 18;
        img.alt = '';
        img.src = assetUrl(`img/${rounded >= i ? 'star' : rounded >= i - 0.5 ? 'star-half' : 'star-empty'}.svg`);
        wrapper.append(img);
    }
    return wrapper;
}

export function ratingLabel(average, count) {
    if (!count) {
        return 'Pas encore noté';
    }
    return `${average.toLocaleString('fr-BE', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}/5 (${count} avis)`;
}

/** Message d'état d'une zone de contenu : chargement, erreur, liste vide (FE-21). */
export function renderState(container, type, message) {
    container.replaceChildren();
    const column = document.createElement('div');
    column.className = 'col-12 text-center py-5';
    column.dataset.state = type;
    const text = document.createElement('p');
    text.className = type === 'error' ? 'state-message state-message--error' : 'state-message';
    text.textContent = message;
    column.append(text);
    container.append(column);
}
