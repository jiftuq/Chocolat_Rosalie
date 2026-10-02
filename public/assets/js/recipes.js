// Page Recettes : liste des cartes avec états chargement / erreur / vide (FE-20, FE-21).
import { api } from './api.js';
import { renderRecipeCard } from './cards.js';
import { renderState } from './ui.js';

const container = document.querySelector('[data-recipes]');

try {
    const { data } = await api('recipes');
    if (data.length === 0) {
        renderState(container, 'empty', 'Aucune recette n’est encore disponible. Revenez bientôt !');
    } else {
        container.replaceChildren(...data.map(renderRecipeCard));
    }
} catch (error) {
    renderState(container, 'error', 'Impossible de charger les recettes pour le moment. Merci de réessayer dans quelques instants.');
}
