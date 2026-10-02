<?php
// Recettes « trompe-l'œil » affichées sur /recettes et /recette&slug=...
// Contenu repris de la maquette Figma (site-mr). À terme, ces données
// viendront de la table `recipes` (data/maison_rosalie_v1.sql).

const RECIPE_STEPS_DEFAULT = [
    ['title' => '1. La coque', 'text' => 'Tempérer le chocolat blanc puis réaliser une fine coque dans un moule en forme d’orange. Laisser cristalliser avant de démouler.'],
    ['title' => '2. Le confit d’orange', 'text' => 'Préparer les zestes et morceaux d’orange avec le sucre et le jus d’orange. Cuire doucement jusqu’à obtenir une texture fondante et légèrement gélifiée. Ajouter la pectine, puis laisser refroidir.'],
    ['title' => '3. La ganache', 'text' => 'Faire chauffer la crème avec les zestes d’orange. Verser sur le chocolat et émulsionner jusqu’à obtenir une ganache lisse et brillante. Incorporer le beurre.'],
    ['title' => '4. Le croustillant', 'text' => 'Mélanger le chocolat fondu avec le praliné et les éclats croustillants. Étaler une fine couche et laisser cristalliser.'],
    ['title' => '5. Le montage', 'text' => "Déposer successivement dans la coque :\nconfit d’orange → ganache chocolatée → croustillant praliné.\nRefermer délicatement avec du chocolat blanc."],
];

const RECIPE_DESCRIPTION_DEFAULT = "Une création inspirée de l’orangette traditionnelle, revisitée en trompe-l’œil.\nUne coque délicate de chocolat blanc renfermant un cœur d’orange confite, une ganache chocolatée onctueuse et une fine couche croquante.";

function getRecipes(): array
{
    $steps = static function (array $images) {
        $out = RECIPE_STEPS_DEFAULT;
        foreach ($images as $i => $img) {
            $out[$i]['image'] = $img;
        }
        return $out;
    };

    return [
        'orangette' => [
            'slug' => 'orangette',
            'name' => 'Orangette',
            'menu' => 'Pralines l’orangette',
            'color' => 'var(--orange-70)',
            'description' => RECIPE_DESCRIPTION_DEFAULT,
            'hero' => 'praline-orange.png',
            'heroTilt' => -15,
            'cut' => 'recettes/orangette-coupe.png',
            'cutClass' => 'is-mirrored',
            'ingredients' => [
                ['Chocolat blanc', 'orangette/ing-chocolat-blanc.jpg'],
                ['Zeste d’orange', 'orangette/ing-zeste.jpg'],
                ['Orange', 'orangette/ing-orange.jpg'],
                ['Jus d’orange', 'orangette/ing-jus.jpg'],
                ['Sucre blanc', 'orangette/ing-sucre.jpg'],
                ['Pectine', 'orangette/ing-pectine.jpg'],
                ['Crème liquide culinaire', 'orangette/ing-creme.jpg'],
                ['Praliné', 'orangette/ing-praline.jpg'],
                ['Mix de noix', 'orangette/ing-noix.jpg'],
            ],
            'steps' => $steps([
                'orangette/step-1.jpg', 'orangette/step-2.jpg', 'orangette/step-3.jpg',
                'orangette/step-4.jpg', 'orangette/step-5.jpg',
            ]),
            'time' => '5h00',
            'difficulty' => 'Difficile',
            'cost' => 'Moyen',
        ],
        'citronnelle' => [
            'slug' => 'citronnelle',
            'name' => 'Citronnelle',
            'menu' => 'Pralines citronnelle',
            'color' => 'var(--citron)',
            'description' => RECIPE_DESCRIPTION_DEFAULT,
            'hero' => 'praline-citron.png',
            'heroTilt' => 0,
            'cut' => 'recettes/citron-coupe.png',
            'cutClass' => 'is-tilted',
            'ingredients' => [
                ['Chocolat blanc', 'orangette/ing-chocolat-blanc.jpg'],
                ['Zeste d’orange', 'citron/ing-zeste.jpg'],
                ['Orange', 'citron/ing-orange.jpg'],
                ['Sucre blanc', 'orangette/ing-sucre.jpg'],
                ['Pectine', 'orangette/ing-pectine.jpg'],
                ['Crème liquide culinaire', 'orangette/ing-creme.jpg'],
                ['Praliné', 'orangette/ing-praline.jpg'],
                ['Mix de noix', 'orangette/ing-noix.jpg'],
            ],
            'steps' => $steps([
                'orangette/step-1.jpg', 'orangette/step-2.jpg', 'orangette/step-3.jpg',
                'orangette/step-4.jpg', 'citron/step-5.jpg',
            ]),
            'time' => '5h00',
            'difficulty' => 'Facile',
            'cost' => 'Moyen',
        ],
        'cerisette' => [
            'slug' => 'cerisette',
            'name' => 'Cerisette',
            'menu' => 'Pralines cerisette',
            'color' => 'var(--cerise)',
            'description' => RECIPE_DESCRIPTION_DEFAULT,
            'hero' => 'praline-cerise.png',
            'heroTilt' => 0,
            'cut' => 'recettes/cerise-coupe.png',
            'cutClass' => 'is-mirrored',
            'ingredients' => [
                ['Chocolat blanc', 'orangette/ing-chocolat-blanc.jpg'],
                ['Cerise', 'cerise/ing-cerise.jpg'],
                ['Sucre blanc', 'orangette/ing-sucre.jpg'],
                ['Pectine', 'orangette/ing-pectine.jpg'],
                ['Crème liquide culinaire', 'orangette/ing-creme.jpg'],
            ],
            'steps' => $steps([
                'orangette/step-1.jpg', 'cerise/step-2.jpg', 'cerise/step-3.jpg',
                'orangette/step-4.jpg', 'cerise/step-5.jpg',
            ]),
            'time' => '5h00',
            'difficulty' => 'Facile',
            'cost' => '',
        ],
    ];
}

function getRecipe(string $slug): ?array
{
    return getRecipes()[$slug] ?? null;
}
