// Carte de recette partagée par l'accueil (top 3) et la page Recettes.
import { ratingLabel, renderStars } from './ui.js';

const el = (tag, className, text) => {
    const element = document.createElement(tag);
    if (className) {
        element.className = className;
    }
    if (text !== undefined) {
        element.textContent = text;
    }
    return element;
};

export function recipeUrl(slug) {
    return `index.php?${new URLSearchParams({ page: 'recette', slug })}`;
}

export function renderRecipeCard(recipe) {
    const column = el('div', 'col-12 col-md-6 col-lg-4');
    const card = el('article', 'card recipe-card h-100');

    if (recipe.rank) {
        const rank = el('span', 'recipe-card__rank', `${recipe.rank}`);
        rank.setAttribute('aria-label', `Rang ${recipe.rank}`);
        card.append(rank);
    }

    const img = el('img', 'card-img-top recipe-card__img');
    img.src = `assets/img/${recipe.image}`;
    img.alt = recipe.title;
    img.loading = 'lazy';
    img.width = 600;
    img.height = 600;

    const body = el('div', 'card-body d-flex flex-column');
    if (recipe.categories && recipe.categories.length) {
        body.append(el('span', 'badge badge-category align-self-start mb-2', recipe.categories.join(', ')));
    }
    body.append(el('h3', 'card-title recipe-card__title', recipe.title));

    const rating = el('p', 'recipe-card__rating');
    rating.append(renderStars(recipe.averageRating), el('span', '', ` ${ratingLabel(recipe.averageRating, recipe.ratingCount)}`));
    body.append(rating);

    const meta = el('ul', 'recipe-card__meta');
    meta.append(el('li', '', `⏱ ${recipe.totalTimeLabel}`));
    const difficulty = el('li', '', `${'●'.repeat(recipe.difficultyLevel)}${'○'.repeat(3 - recipe.difficultyLevel)} ${recipe.difficultyLabel}`);
    difficulty.setAttribute('aria-label', `Difficulté : ${recipe.difficultyLabel}`);
    meta.append(difficulty);
    body.append(meta);

    const link = el('a', 'btn btn-brown mt-auto stretched-link', 'Voir la recette');
    link.href = recipeUrl(recipe.slug);
    link.setAttribute('aria-label', `Voir la recette : ${recipe.title}`);
    body.append(link);

    card.append(img, body);
    column.append(card);
    return column;
}
