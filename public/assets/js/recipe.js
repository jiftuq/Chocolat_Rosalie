// Page recette : notation par étoiles (FE-34) et commentaires (FE-35, FE-36),
// le tout sans rechargement de page.
import { api } from './api.js';
import { clearErrors, formData, ratingLabel, renderStars, setBusy, showErrors, toast, validateForm } from './ui.js';

const article = document.querySelector('[data-recipe]');
const slug = article.dataset.recipe;

/* ---------- Note moyenne ---------- */

const summaryStars = article.querySelector('[data-stars]');
const summaryText = article.querySelector('[data-rating-text]');

function updateSummary(average, count) {
    summaryStars.replaceChildren(renderStars(average));
    summaryText.textContent = ratingLabel(average, count);
}
summaryStars.replaceChildren(renderStars(Number(summaryStars.dataset.stars)));

/* ---------- Notation ---------- */

const ratingForm = article.querySelector('[data-rating-form]');
const ratingFeedback = ratingForm.querySelector('[data-rating-feedback]');
let ratingTimer = null;
let ratingPending = false;

function setUserRating(value) {
    ratingForm.querySelectorAll('input[name="rating"]').forEach((input) => {
        input.checked = Number(input.value) === value;
    });
    ratingFeedback.textContent = value ? `Votre note : ${value}/5. Vous pouvez la modifier.` : 'Cliquez sur une étoile pour noter.';
}

async function sendRating(value) {
    if (ratingPending) {
        return;
    }
    ratingPending = true;
    ratingForm.setAttribute('aria-busy', 'true');
    ratingFeedback.textContent = 'Enregistrement de votre note…';
    try {
        const { data, message } = await api('ratings', { method: 'POST', body: { recipe: slug, rating: value } });
        updateSummary(data.average, data.count);
        setUserRating(data.userRating);
        toast(message);
    } catch (error) {
        ratingFeedback.textContent = error.message;
        toast(error.message, 'error');
    } finally {
        ratingPending = false;
        ratingForm.removeAttribute('aria-busy');
    }
}

// au clic, la note part tout de suite ; au clavier, on laisse un court délai
// pour que les flèches puissent parcourir les étoiles sans envoyer chaque valeur
let clickedRating = false;
ratingForm.addEventListener('pointerdown', () => {
    clickedRating = true;
});
ratingForm.addEventListener('change', (event) => {
    clearTimeout(ratingTimer);
    const value = Number(event.target.value);
    ratingTimer = setTimeout(() => sendRating(value), clickedRating ? 0 : 700);
    clickedRating = false;
});
ratingForm.addEventListener('submit', (event) => event.preventDefault());

/* ---------- Commentaires ---------- */

const list = article.querySelector('[data-comments-list]');
const state = article.querySelector('[data-comments-state]');
const moreButton = article.querySelector('[data-comments-more]');
const countBadge = article.querySelector('[data-comments-count]');
const commentForm = article.querySelector('[data-comment-form]');
const counter = commentForm.querySelector('[data-counter]');
const messageField = commentForm.elements.message;
let nextOffset = 0;

const dateFormat = new Intl.DateTimeFormat('fr-BE', { dateStyle: 'long', timeStyle: 'short' });

function setCount(total) {
    countBadge.textContent = `(${total})`;
    state.textContent = total === 0 ? 'Aucun commentaire pour le moment. Soyez le premier à donner votre avis !' : '';
    state.hidden = total !== 0;
}

/** Le texte saisi par les visiteurs est inséré avec textContent : jamais interprété (FE-63). */
function renderComment(comment) {
    const item = document.createElement('li');
    item.className = 'comment';
    item.dataset.id = comment.id;

    const header = document.createElement('div');
    header.className = 'comment__header';
    const author = document.createElement('strong');
    author.textContent = comment.author;
    const date = document.createElement('time');
    date.dateTime = comment.createdAt;
    date.textContent = dateFormat.format(new Date(comment.createdAt));
    header.append(author, date);
    item.append(header);

    if (comment.subject) {
        const subject = document.createElement('p');
        subject.className = 'comment__subject';
        subject.textContent = comment.subject;
        item.append(subject);
    }
    const message = document.createElement('p');
    message.className = 'comment__message';
    message.textContent = comment.message;
    item.append(message);

    if (comment.canDelete) {
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-link btn-sm comment__delete';
        remove.textContent = 'Supprimer';
        remove.setAttribute('aria-label', `Supprimer le commentaire de ${comment.author}`);
        remove.addEventListener('click', () => deleteComment(comment.id, item, remove));
        item.append(remove);
    }
    return item;
}

async function loadComments(reset = false) {
    if (reset) {
        nextOffset = 0;
        list.replaceChildren();
    }
    setBusy(moreButton, true);
    try {
        const { data } = await api('comments', { params: { recipe: slug, offset: nextOffset } });
        list.append(...data.items.map(renderComment));
        nextOffset = data.nextOffset;
        setCount(data.total);
        moreButton.classList.toggle('d-none', !data.hasMore);
    } catch (error) {
        state.hidden = false;
        state.textContent = 'Les commentaires sont momentanément indisponibles.';
    } finally {
        setBusy(moreButton, false);
    }
}

async function deleteComment(id, item, button) {
    if (!window.confirm('Supprimer définitivement ce commentaire ?')) {
        return;
    }
    setBusy(button, true);
    try {
        const { data, message } = await api('comments', { method: 'DELETE', params: { id } });
        item.remove();
        nextOffset = Math.max(0, nextOffset - 1);
        setCount(data.total);
        toast(message);
    } catch (error) {
        toast(error.message, 'error');
        setBusy(button, false);
    }
}

function updateCounter() {
    const length = messageField.value.trim().length;
    counter.textContent = `${length} / 500 caractères`;
}

messageField.addEventListener('input', updateCounter);
moreButton.addEventListener('click', () => loadComments());

commentForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearErrors(commentForm);
    if (!validateForm(commentForm)) {
        return;
    }
    const button = commentForm.querySelector('[type="submit"]');
    setBusy(button, true);
    try {
        const { data, message } = await api('comments', { method: 'POST', body: { ...formData(commentForm), recipe: slug } });
        list.prepend(renderComment(data.comment));
        nextOffset += 1;
        setCount(data.total);
        commentForm.reset();
        updateCounter();
        toast(message);
    } catch (error) {
        // la saisie est conservée en cas d'erreur
        showErrors(commentForm, error);
    } finally {
        setBusy(button, false);
    }
});

/* ---------- Changement d'état connecté / déconnecté ---------- */

document.addEventListener('auth:change', async ({ detail }) => {
    loadComments(true);
    if (!detail.user) {
        setUserRating(null);
        return;
    }
    try {
        const { data } = await api('recipe', { params: { slug } });
        setUserRating(data.userRating);
    } catch {
        setUserRating(null);
    }
});

loadComments(true);
