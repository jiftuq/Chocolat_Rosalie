<?php
// Catalogue de l'e-shop, tel que présenté dans la maquette Figma (page « chocolat »).

function getShopSections(): array
{
    return [
        [
            'id' => 'pralines-trompe-loeil',
            'title' => 'Pralines Trompe l’oeil',
            'layout' => 'grid-4',
            'products' => [
                ['id' => 'praline-orangette', 'lines' => ['Praline', 'L’orangette'], 'price' => '20€ - 100 grammes', 'image' => 'recettes/orangette-coupe.png', 'favorite' => true],
                ['id' => 'praline-cerisette', 'lines' => ['Praline', 'La cerisette'], 'price' => '20€ - 100 grammes', 'image' => 'recettes/cerise-coupe.png', 'favorite' => true],
                ['id' => 'praline-citronelle', 'lines' => ['Praline', 'La citronelle'], 'price' => '20€ - 100 grammes', 'image' => 'recettes/citron-coupe.png', 'favorite' => true],
                ['id' => 'praline-framboisette', 'lines' => ['Praline', 'La framboisette'], 'price' => '20€ - 100 grammes', 'image' => 'shop/framboise-coupe.png', 'favorite' => true],
            ],
        ],
        [
            'id' => 'tablettes',
            'title' => 'Tablettes de chocolats',
            'layout' => 'grid-4 is-tall',
            'products' => [
                ['id' => 'tablette-orangette', 'lines' => ['Tablette de chocolat', 'L’orangette', '90 G - 65% chocolat'], 'price' => '14,99€', 'image' => 'shop/tablette-4.png'],
                ['id' => 'tablette-cerisette', 'lines' => ['Tablette de chocolat', 'La cerisette', '90 G - 65% chocolat'], 'price' => '14,99€', 'image' => 'shop/tablette-3.png'],
                ['id' => 'tablette-citronelle', 'lines' => ['Tablette de chocolat', 'La citronelle', '90 G - 65% chocolat'], 'price' => '14,99€', 'image' => 'shop/tablette-1.png'],
                ['id' => 'tablette-framboisette', 'lines' => ['Tablette de chocolat', 'La framboisette', '90 G - 65% chocolat'], 'price' => '14,99€', 'image' => 'shop/tablette-2.png'],
            ],
        ],
        [
            'id' => 'pralines-maison-rosalie',
            'title' => 'Pralines Maison Rosalie',
            'layout' => 'grid-3',
            'products' => [
                ['id' => 'bouton-rosalie', 'lines' => ['Bouton Rosalie'], 'price' => '6.99€ - 100 grammes', 'image' => 'shop/rosalie-1.png'],
                ['id' => 'dome-dor', 'lines' => ['Dôme d’or'], 'price' => '6.99€ - 100 grammes', 'image' => 'shop/rosalie-2.png'],
                ['id' => 'gaufrette-gourmande', 'lines' => ['Gaufrette gourmande'], 'price' => '5.99€ - 100 grammes', 'image' => 'shop/rosalie-3.png'],
                ['id' => 'carre-dor', 'lines' => ['Carré d’or'], 'price' => '6.99€ - 100 grammes', 'image' => 'shop/rosalie-4.png'],
                ['id' => 'bulle-dor', 'lines' => ['Bulle d’or'], 'price' => '6.99€ - 100 grammes', 'image' => 'shop/rosalie-5.png'],
                ['id' => 'noisetterie-gourmand', 'lines' => ['Noisetterie gourmand'], 'price' => '5.99€ - 100 grammes', 'image' => 'shop/rosalie-6.png'],
            ],
        ],
        [
            'id' => 'coffrets-tablettes',
            'title' => "Coffrets\nTablette de chocolat",
            'layout' => 'grid-2',
            'products' => [
                ['id' => 'coffret-tablettes-4', 'lines' => ['Coffret Tablettes de chocolat', '4 saveurs de la nouvelles collection'], 'price' => '68.90€', 'image' => 'shop/coffret-tablettes.png', 'tilt' => -29],
                ['id' => 'coffret-tablettes-12', 'lines' => ['Coffret Cadeau', '12 Tablettes de chocolat', '4 saveurs de la nouvelles collection'], 'price' => '120.90€', 'image' => 'shop/coffret-tablettes.png', 'tilt' => 8],
            ],
        ],
        [
            'id' => 'coffrets-trompe-loeil',
            'title' => "Coffrets\nTrompe l’oeil",
            'layout' => 'grid-2 has-feature',
            'products' => [
                ['id' => 'coffret-decouverte-trompe', 'lines' => ['Coffret découverte', 'des 4 pralines trompe l’oeil'], 'price' => '32.90€', 'image' => 'shop/coffret-decouverte-trompe.png'],
                ['id' => 'coffret-gourmand-trompe', 'lines' => ['Coffret gourmand', '12 pralines trompe l’œil'], 'price' => '78.90€', 'image' => 'shop/coffret-trompe.png'],
                ['id' => 'coffret-cadeau-trompe', 'lines' => ['Coffret cadeau', '28 pralines trompe l’œil'], 'price' => '134.95€', 'image' => 'shop/coffret-cadeau-trompe.png', 'tilt' => -9],
            ],
        ],
        [
            'id' => 'ecrin',
            'title' => '',
            'layout' => 'grid-1',
            'products' => [
                ['id' => 'ecrin-2', 'lines' => ['L’écrin de praline 2 pièces', 'un écrin raffiné dans un étui élégant', '( offert dans le coffret cadeau)'], 'price' => '18.95€', 'image' => 'shop/ecrin-2.png'],
            ],
        ],
        [
            'id' => 'coffrets-maison-rosalie',
            'title' => '',
            'layout' => 'grid-2 has-feature',
            'products' => [
                ['id' => 'coffret-gourmand-rosalie', 'lines' => ['Coffret gourmand', '12 pralines Maison Rosalie'], 'price' => '34.90€', 'image' => 'shop/coffret-gourmand-rosalie.png'],
                ['id' => 'coffret-decouverte-rosalie', 'lines' => ['Coffret Découverte', '9 pralines Maison Rosalie'], 'price' => '20.95€', 'image' => 'shop/ecrin.png'],
                ['id' => 'coffret-cadeau-rosalie', 'lines' => ['Coffret Cadeau', '23 pralines Maison Rosalie'], 'price' => '60.95€', 'image' => 'shop/coffret-cadeau-rosalie.png'],
            ],
        ],
    ];
}

// Raccourcis « catégories » en haut de l'e-shop.
function getShopCategories(): array
{
    return [
        ['anchor' => 'pralines-trompe-loeil', 'title' => 'Pralines Trompe l’oeil', 'image' => 'recettes/orangette-coupe.png', 'class' => 'is-square'],
        ['anchor' => 'tablettes', 'title' => 'Tablettes de chocolats', 'image' => 'shop/tablette-4.png', 'class' => 'is-portrait'],
        ['anchor' => 'pralines-maison-rosalie', 'title' => 'Pralines Rosalie', 'image' => 'shop/pralines-rosalie.png', 'class' => 'is-wide'],
        ['anchor' => 'coffrets-tablettes', 'title' => "Coffret\nTablettes de chocolat", 'image' => 'shop/coffret-tablettes.png', 'class' => 'is-tilted'],
        ['anchor' => 'coffrets-trompe-loeil', 'title' => "Coffret\nPralines Trompe l’œil", 'image' => 'shop/coffret-trompe.png', 'class' => ''],
        ['anchor' => 'coffrets-maison-rosalie', 'title' => "Coffret\nMaison Rosalie", 'image' => 'shop/ecrin.png', 'class' => ''],
    ];
}

function findProduct(string $id): ?array
{
    foreach (getShopSections() as $section) {
        foreach ($section['products'] as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }
    }
    return null;
}
