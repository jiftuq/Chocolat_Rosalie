# Documentation de l'API — Maison Rosalie

Point d'entrée unique : `public/api.php?route=<route>`.

## Format des réponses (BE-70)

Toutes les réponses sont en JSON, avec la même structure :

```json
{
  "success": true,
  "message": "Texte lisible destiné au visiteur (peut être vide)",
  "data": { },
  "errors": { "champ": "Message d'erreur du champ" }
}
```

- `data` vaut `null` en cas d'erreur.
- `errors` n'est rempli que pour les erreurs de validation (422), avec une entrée par champ.

## Codes HTTP (BE-71)

| Code | Signification |
|------|---------------|
| 200 | Succès |
| 201 | Élément créé (inscription, commentaire, message de contact) |
| 401 | Non connecté (ou identifiants incorrects à la connexion) |
| 403 | Accès interdit : jeton CSRF absent ou invalide, ou action sur l'élément d'un autre |
| 404 | Route ou élément introuvable |
| 405 | Méthode HTTP non prévue pour cette route (en-tête `Allow` renvoyé) |
| 422 | Données invalides (détail par champ dans `errors`) |
| 429 | Trop de tentatives (connexion, commentaires, messages de contact) |
| 503 | Erreur serveur ou base indisponible : message générique, détail dans `logs/app.log` |

## Sécurité commune

- **CSRF** : toute requête autre que `GET` doit envoyer l'en-tête `X-CSRF-Token`. Le jeton est présent dans la balise `<meta name="csrf-token">` de chaque page et renvoyé par `auth/me`, `auth/login`, `auth/register` et `auth/logout` (il change à chaque connexion).
- **Corps des requêtes** : JSON (`Content-Type: application/json`) ou formulaire classique.
- **Session** : cookie `rosalie_sid` (HttpOnly, SameSite=Lax, Secure en HTTPS), expirée après 2 h d'inactivité.

---

## Comptes

### `GET auth/me`
Accès : tous. Renvoie l'utilisateur connecté (ou `null`) et le jeton CSRF (BE-04).

```json
{ "user": { "username": "claire", "isAdmin": false }, "csrfToken": "…" }
```

### `POST auth/register`
Accès : tous (+ CSRF). Crée le compte puis connecte l'utilisateur.

| Champ | Règle |
|-------|-------|
| `username` | obligatoire, 3 à 30 caractères, lettres, chiffres, `.`, `-`, `_`, unique |
| `email` | obligatoire, e-mail valide, 254 caractères max., unique |
| `password` | obligatoire, 10 à 72 caractères, au moins une lettre et un chiffre |
| `password_confirm` | identique à `password` |

Réponses : `201` (données comme `auth/me`), `422` (erreurs par champ, dont « déjà utilisé »).

### `POST auth/login`
Accès : tous (+ CSRF). Champs `email`, `password`.

Réponses : `200`, `401` « Adresse e-mail ou mot de passe incorrect. » (même message que l'e-mail existe ou non), `422` (champ vide), `429` après 5 échecs pour un même e-mail ou 20 pour une même IP en 15 minutes.

### `POST auth/logout`
Accès : tous (+ CSRF). Détruit la session et renvoie un nouveau jeton CSRF. Réponse : `200`.

---

## Recettes

### `GET recipes/menu`
Accès : tous. Liste légère pour le menu déroulant (BE-10).

```json
[{ "title": "La Praline Orangette", "slug": "praline-orangette" }]
```

### `GET recipes`
Accès : tous. Toutes les recettes avec note moyenne et nombre de votes (BE-11).

```json
[{
  "title": "La Praline Orangette", "slug": "praline-orangette",
  "image": "recipes/orangette.webp", "categories": ["Mousses"],
  "totalTime": 110, "totalTimeLabel": "1 h 50",
  "difficulty": "hard", "difficultyLabel": "Difficile", "difficultyLevel": 3,
  "averageRating": 4.7, "ratingCount": 6
}]
```

`totalTime` est calculé par le backend (préparation + cuisson, BE-14). `averageRating` vaut `null` si la recette n'a aucun vote.

### `GET recipes/top`
Accès : tous. Les 3 recettes de l'accueil (BE-30), mêmes champs que `recipes` plus `rank` (1 à 3).
Ordre : recettes notées d'abord, par moyenne décroissante, puis nombre de votes, puis la plus récente ; s'il y a moins de 3 recettes notées, les plus récentes complètent.

### `GET recipe&slug=<slug>`
Accès : tous. Détail complet (BE-12) : champs de `recipes` plus `description`, `prepTime`, `cookTime`, `portions`, `ingredients` (`name`, `quantity`, `unit`, `display`), `steps` (`number`, `title`, `description`, `image`) et `userRating` (note de l'utilisateur connecté, sinon `null`).

Réponses : `200`, `404` « Cette recette n'existe pas. » (BE-13).

---

## Notes

### `POST ratings`
Accès : **connecté** (+ CSRF). Champs `recipe` (slug) et `rating` (entier de 1 à 5).
Noter à nouveau remplace la note précédente (BE-21).

```json
{ "average": 4.5, "count": 6, "userRating": 4 }
```

Réponses : `200`, `401`, `404` (recette inconnue), `422` (0, 6, 4.5, texte… sont refusés).

---

## Commentaires

### `GET comments&recipe=<slug>&offset=<n>`
Accès : tous. Commentaires publiés, du plus récent au plus ancien, par tranches de 10 (BE-40).

```json
{
  "items": [{
    "id": 12, "author": "claire", "subject": "Bluffant", "message": "…",
    "createdAt": "2026-09-19T18:05:00+02:00", "canDelete": false
  }],
  "total": 12, "nextOffset": 10, "hasMore": true
}
```

Le texte est renvoyé brut : c'est l'affichage (`textContent` en JS, `htmlspecialchars` en PHP) qui le rend inoffensif (BE-45).

### `POST comments`
Accès : **connecté** (+ CSRF). Champs `recipe` (slug), `subject` (facultatif, 120 caractères max.), `message` (3 à 500 caractères après suppression des espaces superflus).
Limitation : 5 commentaires maximum par utilisateur et par tranche de 10 minutes (BE-44).

Réponses : `201` avec `{ "comment": { … }, "total": 13 }` (BE-42), `401`, `404`, `422`, `429`.

### `DELETE comments&id=<id>`
Accès : **auteur du commentaire ou administrateur** (+ CSRF) (BE-43).

Réponses : `200` avec `{ "id": 12, "total": 11 }`, `401`, `403`, `404`.

---

## Contact

### `POST contact`
Accès : tous (+ CSRF). Champs `name` (2 à 100), `email`, `subject` (3 à 120), `message` (10 à 2000), `website` (champ piège, doit rester vide).

Protection anti-spam (BE-50) :
- champ piège `website` : s'il est rempli, la réponse est identique mais rien n'est enregistré ;
- délai minimum de 3 secondes entre l'affichage de la page Contact et l'envoi (même traitement) ;
- 3 messages maximum par session et par tranche de 10 minutes (`429`).

Réponses : `201`, `422`, `429`.
