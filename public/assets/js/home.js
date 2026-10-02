// Accueil : bloc « Les 3 recettes les mieux notées » (FE-11, FE-12).
import { api } from './api.js';
import { renderRecipeCard } from './cards.js';
import { renderState } from './ui.js';

const container = document.querySelector('[data-top-recipes]');

try {
    const { data } = await api('recipes/top');
    if (data.length === 0) {
        renderState(container, 'empty', 'Nos recettes arrivent bientôt dans le livre.');
    } else {
        container.replaceChildren(...data.map(renderRecipeCard));
    }
} catch (error) {
    renderState(container, 'error', 'Les recettes les mieux notées sont momentanément indisponibles.');
}
