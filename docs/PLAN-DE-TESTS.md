# Plan de tests — Maison Rosalie (QC-07)

Deux niveaux de tests :

1. **Automatisés** : `php tests/api-test.php http://localhost:8000` (site lancé, base importée).
   Le script envoie de vraies requêtes HTTP, y compris des requêtes « fabriquées à la main » (sans jeton CSRF, notes falsifiées, injections…), et affiche ✔ / ✘ pour chaque cas.
2. **Manuels** dans le navigateur, pour l'interface (modales, étoiles, affichage mobile).

Résultat obtenu lors de la dernière exécution : **48 tests automatisés réussis sur 48**, et tous les tests manuels conformes (Chromium, 1280 px et 360 px).

## Tests automatisés (`tests/api-test.php`)

| # | Cas | Résultat attendu | Obtenu |
|---|-----|------------------|--------|
| A1 | Les 4 pages et une recette | HTTP 200 | ✔ |
| A2 | Recette inexistante (`?page=recette&slug=inexistante`) | 404, page conviviale avec lien retour | ✔ |
| A3 | `recipes/menu` | les 5 recettes de la base (BE-10) | ✔ |
| A4 | `recipe` : détail | ingrédients, ≥ 4 étapes avec photo, temps total = préparation + cuisson (BE-12, BE-14) | ✔ |
| A5 | Slug piégé `x' OR '1'='1` | 404 sans détail technique (BE-13, SEC-01) | ✔ |
| A6 | Noter sans être connecté | 401 (scénario 4) | ✔ |
| A7 | Commenter sans être connecté | 401 (scénario 4) | ✔ |
| A8 | Requête POST avec un faux jeton CSRF | 403 (SEC-03) | ✔ |
| A9 | Méthode non prévue (`DELETE recipes`) | 405 | ✔ |
| A10 | Inscription invalide | 422, une erreur par champ (scénario 5) | ✔ |
| A11 | Inscription avec un e-mail déjà pris | 422, erreur sur `email` (scénario 5) | ✔ |
| A12 | Inscription valide | 201, utilisateur connecté | ✔ |
| A13 | Mot de passe en base | empreinte bcrypt, jamais le texte clair (SEC-04) | ✔ |
| A14 | Mauvais mot de passe / compte inexistant | même message 401 (BE-02) | ✔ |
| A15 | Injection SQL dans la connexion | 401, aucun effet (scénario 14) | ✔ |
| A16 | Connexion valide | 200 | ✔ |
| A17 | 6e tentative ratée en 15 min | 429 (BE-03) | ✔ |
| A18 | Note de 4 | moyenne recalculée, un vote de plus (scénario 6) | ✔ |
| A19 | Nouvelle note sur la même recette | remplace l'ancienne, pas de second vote (scénario 7, BE-21) | ✔ |
| A20 | Notes 0, 6, "4.5", "abc", "", null | 422 (scénario 8, BE-20) | ✔ |
| A21 | Note sur une recette inexistante | 404 | ✔ |
| A22 | Note personnelle renvoyée dans le détail | `userRating` = dernière note | ✔ |
| A23 | Top 3 | 3 recettes triées par moyenne décroissante (scénario 9, BE-30) | ✔ |
| A24 | Commentaire de 1 caractère entouré d'espaces | 422 (BE-41) | ✔ |
| A25 | Commentaire de 501 caractères | 422 (BE-41) | ✔ |
| A26 | Commentaire contenant `<script>` | 201, texte renvoyé tel quel, en tête de liste (BE-42) | ✔ |
| A27 | Page de la recette après ce commentaire | aucune balise `<script>` injectée (scénario 11, SEC-02) | ✔ |
| A28 | Supprimer le commentaire d'un autre | 403 (scénario 12, BE-43) | ✔ |
| A29 | Supprimer son propre commentaire | 200 | ✔ |
| A30 | Liste des commentaires | tranches de 10, `hasMore` (BE-40) | ✔ |
| A31 | Visiteur | aucun commentaire supprimable | ✔ |
| A32 | 6 commentaires en 10 minutes | le 6e est refusé, 429 (BE-44) | ✔ |
| A33 | Contact invalide | 422, 4 erreurs (scénario 13) | ✔ |
| A34 | Contact valide | 201, message enregistré (BE-50) | ✔ |
| A35 | Contact avec champ piège rempli | 201 mais rien enregistré (BE-50) | ✔ |
| A36 | Action après déconnexion | 401 (SEC-05) | ✔ |

## Tests manuels (navigateur)

| # | Cas | Résultat attendu | Obtenu |
|---|-----|------------------|--------|
| M1 | Ouvrir les 5 recettes depuis le menu déroulant | chaque page s'affiche (scénario 1) | ✔ |
| M2 | Ajouter une 6e recette directement en base | elle apparaît dans le menu et la liste sans modifier le code (scénario 2) | ✔ |
| M3 | Page recette | ingrédients avec quantités, étapes avec photos, temps et difficulté (scénario 3) | ✔ |
| M4 | Formulaire de connexion vide | erreurs affichées sous les champs (FE-61) | ✔ |
| M5 | Connexion depuis l'invitation de la page recette | la modale s'ouvre ; après connexion, nom dans la navigation, formulaires visibles, **sans rechargement** (FE-36, FE-53, FE-60) | ✔ |
| M6 | Clic sur 3 étoiles puis sur 4 | moyenne mise à jour sans rechargement, note personnelle rappelée (scénarios 6 et 7) | ✔ |
| M7 | Étoiles au clavier (Tab puis flèches) | sélection possible, envoi après une courte pause (FE-65) | ✔ |
| M8 | Commentaire avec `<img src=x onerror=alert(1)>` | affiché comme texte, aucune alerte (scénario 11) | ✔ |
| M9 | Commentaire ajouté puis actualisation de la page | visible immédiatement puis après actualisation (scénario 10) | ✔ |
| M10 | « Afficher plus de commentaires » | les commentaires au-delà de 10 se chargent (FE-35) | ✔ |
| M11 | Supprimer son commentaire | confirmation, disparition, compteur mis à jour | ✔ |
| M12 | Double clic sur « Publier » / « Envoyer » | bouton désactivé pendant l'envoi, un seul envoi (FE-62) | ✔ |
| M13 | Déconnexion | invitation à se connecter réaffichée sans rechargement | ✔ |
| M14 | Contact vide puis valide | erreurs par champ, puis message de succès ; la saisie est conservée en cas d'erreur (FE-41, FE-42) | ✔ |
| M15 | Largeur 360 px et 768 px sur toutes les pages | aucun défilement horizontal, menu replié (FE-06, FE-64, scénario 15) | ✔ |
| M16 | Base arrêtée (`mysqladmin shutdown`) | accueil et pages affichent un message propre, recette : page « Petit contretemps en cuisine » (503), API : message générique ; détail dans `logs/app.log` (scénario 16, SEC-10) | ✔ |
| M17 | Base vide + `data/maison_rosalie.sql` + README | site fonctionnel ; le script peut être rejoué sans erreur (scénario 17, BE-61) | ✔ |
| M18 | Note 6 insérée directement en SQL | refusée par la contrainte `chk_ratings_value` de la base | ✔ |
