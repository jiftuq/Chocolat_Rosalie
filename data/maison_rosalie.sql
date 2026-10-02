-- =====================================================================
-- Maison Rosalie — script d'import unique (BE-60 / BE-61)
-- Crée la base, toutes les tables, puis les données de démonstration.
-- Peut être rejoué à volonté : chaque table est supprimée puis recréée.
--
--   mariadb -u root -p < data/maison_rosalie.sql
--   (ou mysql -u root -p < data/maison_rosalie.sql)
--
-- Compatible MySQL 8 et MariaDB 10.5+.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `maison_rosalie`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `maison_rosalie`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `ratings`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `steps`;
DROP TABLE IF EXISTS `recipe_ingredients`;
DROP TABLE IF EXISTS `recipe_categories`;
DROP TABLE IF EXISTS `recipes`;
DROP TABLE IF EXISTS `ingredients`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- Structure
-- ---------------------------------------------------------------------

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(254) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`),
  CONSTRAINT `chk_users_username` CHECK (CHAR_LENGTH(`username`) BETWEEN 3 AND 50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_title` (`title`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recipes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(170) NOT NULL,
  `description` TEXT NOT NULL,
  `main_image` VARCHAR(500) NOT NULL,
  `prep_time_minutes` INT UNSIGNED NOT NULL,
  `cook_time_minutes` INT UNSIGNED NOT NULL,
  `portions` INT UNSIGNED NOT NULL,
  `difficulty` ENUM('easy', 'medium', 'hard') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `author_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recipes_slug` (`slug`),
  KEY `idx_recipes_author` (`author_id`),
  KEY `idx_recipes_created_at` (`created_at`),
  CONSTRAINT `fk_recipes_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_recipes_slug` CHECK (`slug` REGEXP '^[a-z0-9]+(-[a-z0-9]+)*$'),
  CONSTRAINT `chk_recipes_portions` CHECK (`portions` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recipe_categories` (
  `recipe_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`recipe_id`, `category_id`),
  KEY `idx_recipe_categories_category` (`category_id`),
  CONSTRAINT `fk_recipe_categories_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recipe_categories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ingredients` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ingredients_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recipe_ingredients` (
  `recipe_id` INT UNSIGNED NOT NULL,
  `ingredient_id` INT UNSIGNED NOT NULL,
  -- quantité absente possible (« une pincée ») : quantity NULL, unit seule
  `quantity` DECIMAL(10,3) NULL DEFAULT NULL,
  `unit` VARCHAR(40) NULL DEFAULT NULL,
  PRIMARY KEY (`recipe_id`, `ingredient_id`),
  KEY `idx_recipe_ingredients_ingredient` (`ingredient_id`),
  CONSTRAINT `fk_recipe_ingredients_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recipe_ingredients_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_recipe_ingredients_quantity` CHECK (`quantity` IS NULL OR `quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `steps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` INT UNSIGNED NOT NULL,
  `step_number` INT UNSIGNED NOT NULL,
  `title` VARCHAR(120) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_steps_recipe_number` (`recipe_id`, `step_number`),
  CONSTRAINT `fk_steps_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_steps_number` CHECK (`step_number` >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ratings` (
  `user_id` INT UNSIGNED NOT NULL,
  `recipe_id` INT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `rated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  -- une seule note par utilisateur et par recette (BE-21)
  PRIMARY KEY (`user_id`, `recipe_id`),
  KEY `idx_ratings_recipe` (`recipe_id`),
  CONSTRAINT `fk_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ratings_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_ratings_value` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` INT UNSIGNED NOT NULL,
  `recipe_id` INT UNSIGNED NOT NULL,
  `subject` VARCHAR(120) NULL DEFAULT NULL,
  `message` VARCHAR(500) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending', 'published', 'hidden') NOT NULL DEFAULT 'published',
  PRIMARY KEY (`id`),
  KEY `idx_comments_recipe_date` (`recipe_id`, `created_at`),
  KEY `idx_comments_author_date` (`author_id`, `created_at`),
  CONSTRAINT `fk_comments_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_comments_message` CHECK (CHAR_LENGTH(`message`) BETWEEN 3 AND 500)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contact_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(254) NOT NULL,
  `subject` VARCHAR(120) NOT NULL,
  `message` VARCHAR(2000) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_messages_date` (`created_at`),
  CONSTRAINT `chk_contact_messages_message` CHECK (CHAR_LENGTH(`message`) >= 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `login_attempts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `attempted_email` VARCHAR(254) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `successful` TINYINT(1) NOT NULL DEFAULT 0,
  `attempted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_attempts_email_date` (`attempted_email`, `attempted_at`),
  KEY `idx_login_attempts_ip_date` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Données de démonstration
-- ---------------------------------------------------------------------

-- Comptes de test (voir README) :
--   admin_rosalie / admin@maisonrosalie.be / Rosalie#Admin2026  (administrateur)
--   claire, julien, sofia, marc, lea, hugo  / <prenom>@exemple.be / Chocolat2026!
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
  (1, 'admin_rosalie', 'admin@maisonrosalie.be', '$2y$10$F7B8VKvuIe.WHLlBxlrbuO02.nyi2Ek9iBNFUuiAE9HxEjc8f6JdW', 'admin', '2026-09-01 09:00:00'),
  (2, 'claire', 'claire@exemple.be', '$2y$10$QxSBY2g0Z6l4omjpWzwC0OnYYgAyCRzQ.m85yZ67662DPZSxvoivC', 'user', '2026-09-03 18:12:00'),
  (3, 'julien', 'julien@exemple.be', '$2y$10$QxSBY2g0Z6l4omjpWzwC0OnYYgAyCRzQ.m85yZ67662DPZSxvoivC', 'user', '2026-09-05 20:40:00'),
  (4, 'sofia', 'sofia@exemple.be', '$2y$10$QxSBY2g0Z6l4omjpWzwC0OnYYgAyCRzQ.m85yZ67662DPZSxvoivC', 'user', '2026-09-08 12:05:00'),
  (5, 'marc', 'marc@exemple.be', '$2y$10$QxSBY2g0Z6l4omjpWzwC0OnYYgAyCRzQ.m85yZ67662DPZSxvoivC', 'user', '2026-09-10 08:30:00'),
  (6, 'lea', 'lea@exemple.be', '$2y$10$QxSBY2g0Z6l4omjpWzwC0OnYYgAyCRzQ.m85yZ67662DPZSxvoivC', 'user', '2026-09-12 21:15:00'),
  (7, 'hugo', 'hugo@exemple.be', '$2y$10$QxSBY2g0Z6l4omjpWzwC0OnYYgAyCRzQ.m85yZ67662DPZSxvoivC', 'user', '2026-09-15 16:50:00');

INSERT INTO `categories` (`id`, `title`, `slug`, `description`) VALUES
  (1, 'Gâteaux', 'gateaux', 'Petits gâteaux et entremets au chocolat, à partager ou à offrir.'),
  (2, 'Mousses', 'mousses', 'Mousses et cœurs fondants, légers comme un nuage de cacao.'),
  (3, 'Boissons', 'boissons', 'Chocolats chauds et boissons gourmandes de la maison.'),
  (4, 'Glacé', 'glace', 'Desserts glacés au chocolat pour les beaux jours.');

INSERT INTO `recipes` (`id`, `title`, `slug`, `description`, `main_image`, `prep_time_minutes`, `cook_time_minutes`, `portions`, `difficulty`, `created_at`, `author_id`) VALUES
  (1, 'La Praline Orangette', 'praline-orangette',
   'Une création inspirée de l’orangette traditionnelle, revisitée en trompe-l’œil : une coque délicate de chocolat blanc renfermant un cœur d’orange confite, une ganache chocolatée onctueuse et une fine couche croquante.',
   'recipes/orangette.webp', 90, 20, 12, 'hard', '2026-09-18 10:00:00', 1),
  (2, 'La Praline Citronnelle', 'praline-citronnelle',
   'Un citron plus vrai que nature : sous sa coque jaune se cachent un confit de citron parfumé à la citronnelle et une ganache blanche acidulée.',
   'recipes/citronnelle.webp', 75, 15, 12, 'medium', '2026-09-20 10:00:00', 1),
  (3, 'La Praline Cerisette', 'praline-cerisette',
   'Une cerise gourmande en chocolat blanc, garnie d’une compotée de griottes et d’une ganache au chocolat noir. La plus simple des pralines trompe-l’œil, idéale pour débuter.',
   'recipes/cerisette.webp', 60, 10, 10, 'easy', '2026-09-22 10:00:00', 1),
  (4, 'La Praline Framboisette', 'praline-framboisette',
   'Une framboise sculptée en chocolat, au cœur de framboises fraîches et de ganache au chocolat au lait, posée sur un croustillant praliné.',
   'recipes/framboisette.webp', 70, 12, 10, 'medium', '2026-09-24 10:00:00', 1),
  (5, 'Le Dôme glacé Rosalie', 'dome-glace-rosalie',
   'Une glace au chocolat noir onctueuse, moulée en dôme et habillée d’un glaçage croquant aux noisettes. Le dessert d’été de la maison.',
   'recipes/dome-glace.webp', 40, 10, 6, 'easy', '2026-09-26 10:00:00', 1);

INSERT INTO `recipe_categories` (`recipe_id`, `category_id`) VALUES
  (1, 2), (2, 2), (3, 1), (4, 1), (5, 4);

INSERT INTO `ingredients` (`id`, `name`) VALUES
  (1, 'Chocolat blanc'),
  (2, 'Chocolat noir 70 %'),
  (3, 'Crème liquide 35 %'),
  (4, 'Beurre doux'),
  (5, 'Sucre blanc'),
  (6, 'Pectine NH'),
  (7, 'Orange non traitée'),
  (8, 'Jus d’orange'),
  (9, 'Praliné noisette'),
  (10, 'Crêpes dentelle'),
  (11, 'Colorant liposoluble'),
  (12, 'Citron jaune non traité'),
  (13, 'Citronnelle fraîche'),
  (14, 'Griottes dénoyautées'),
  (15, 'Framboises'),
  (16, 'Chocolat au lait 40 %'),
  (17, 'Lait entier'),
  (18, 'Jaune d’œuf'),
  (19, 'Fleur de sel'),
  (20, 'Beurre de cacao'),
  (21, 'Noisettes torréfiées');

INSERT INTO `recipe_ingredients` (`recipe_id`, `ingredient_id`, `quantity`, `unit`) VALUES
  (1, 1, 250, 'g'), (1, 7, 2, 'pièces'), (1, 8, 100, 'ml'), (1, 5, 60, 'g'), (1, 6, 4, 'g'),
  (1, 3, 150, 'ml'), (1, 2, 150, 'g'), (1, 4, 20, 'g'), (1, 9, 80, 'g'), (1, 10, 30, 'g'),
  (1, 11, NULL, 'une pointe de couteau'),
  (2, 1, 400, 'g'), (2, 12, 2, 'pièces'), (2, 13, 2, 'bâtons'), (2, 5, 50, 'g'), (2, 6, 4, 'g'),
  (2, 3, 150, 'ml'), (2, 4, 20, 'g'), (2, 11, NULL, 'une pointe de couteau'),
  (3, 1, 250, 'g'), (3, 14, 200, 'g'), (3, 5, 40, 'g'), (3, 6, 3, 'g'), (3, 3, 120, 'ml'),
  (3, 2, 100, 'g'), (3, 11, NULL, 'une pointe de couteau'),
  (4, 1, 250, 'g'), (4, 15, 200, 'g'), (4, 5, 50, 'g'), (4, 6, 4, 'g'), (4, 3, 120, 'ml'),
  (4, 16, 100, 'g'), (4, 9, 60, 'g'), (4, 10, 25, 'g'),
  (5, 17, 500, 'ml'), (5, 3, 200, 'ml'), (5, 18, 5, 'pièces'), (5, 5, 100, 'g'), (5, 2, 150, 'g'),
  (5, 20, 30, 'g'), (5, 21, 50, 'g'), (5, 19, NULL, 'une pincée');

INSERT INTO `steps` (`recipe_id`, `step_number`, `title`, `description`, `image`) VALUES
  (1, 1, 'La coque',
   'Faire fondre le chocolat blanc à 45 °C, le refroidir à 26 °C puis le remonter à 28 °C. Couler une fine coque dans un moule en forme d’orange, retourner pour égoutter et laisser cristalliser 20 minutes à 17 °C.',
   'recipes/steps/orangette-1.webp'),
  (1, 2, 'Le confit d’orange',
   'Tailler le zeste et la chair des oranges en petits dés. Cuire 10 minutes à feu doux avec le jus d’orange et le sucre, ajouter la pectine puis porter 1 minute à ébullition. Laisser refroidir.',
   'recipes/steps/orangette-2.webp'),
  (1, 3, 'La ganache',
   'Chauffer la crème à 80 °C, la verser en trois fois sur le chocolat noir et émulsionner. Incorporer le beurre quand la ganache est à 35 °C, filmer au contact et laisser prendre 1 heure.',
   'recipes/steps/orangette-3.webp'),
  (1, 4, 'Le croustillant',
   'Mélanger le praliné avec 30 g de chocolat noir fondu et les crêpes dentelle émiettées. Étaler sur 3 mm d’épaisseur et laisser figer 15 minutes au réfrigérateur avant de détailler des disques.',
   'recipes/steps/orangette-4.webp'),
  (1, 5, 'Le montage',
   'Garnir chaque coque de confit d’orange puis de ganache et poser un disque de croustillant. Refermer avec du chocolat blanc tempéré coloré en orange et laisser cristalliser 12 heures à 17 °C avant de démouler.',
   'recipes/steps/orangette-5.webp'),

  (2, 1, 'La coque',
   'Tempérer le chocolat blanc (45 °C, puis 26 °C, puis 28 °C) avec une pointe de colorant jaune. Mouler les coques en forme de citron et laisser cristalliser 20 minutes à 17 °C.',
   'recipes/steps/orangette-1.webp'),
  (2, 2, 'Le confit citron-citronnelle',
   'Prélever les zestes et presser les citrons. Cuire 8 minutes à feu doux le jus, les zestes, le sucre et la citronnelle émincée, puis ajouter la pectine et porter 1 minute à ébullition. Retirer la citronnelle et laisser refroidir.',
   'recipes/steps/orangette-2.webp'),
  (2, 3, 'La ganache citronnelle',
   'Infuser la citronnelle 20 minutes dans la crème chaude à 80 °C, filtrer puis verser sur 150 g de chocolat blanc. Émulsionner, ajouter le beurre à 35 °C et laisser prendre 1 heure.',
   'recipes/steps/orangette-3.webp'),
  (2, 4, 'Le montage',
   'Remplir les coques à moitié de confit puis de ganache, à 2 mm du bord. Refermer avec le reste de chocolat blanc tempéré et laisser cristalliser 12 heures à 17 °C.',
   'recipes/steps/citronnelle-5.webp'),

  (3, 1, 'La coque',
   'Tempérer le chocolat blanc (45 °C, puis 26 °C, puis 28 °C) et y ajouter une pointe de colorant rouge. Mouler les coques en forme de cerise et laisser cristalliser 20 minutes à 17 °C.',
   'recipes/steps/orangette-1.webp'),
  (3, 2, 'La compotée de griottes',
   'Cuire les griottes 6 minutes avec le sucre, ajouter la pectine puis porter 1 minute à ébullition. Laisser refroidir à température ambiante.',
   'recipes/steps/cerisette-2.webp'),
  (3, 3, 'La ganache noire',
   'Verser la crème chauffée à 80 °C sur le chocolat noir en trois fois et émulsionner jusqu’à obtenir une ganache lisse. Laisser tiédir 30 minutes.',
   'recipes/steps/cerisette-3.webp'),
  (3, 4, 'Le montage',
   'Déposer une cuillère de compotée dans chaque coque, recouvrir de ganache et refermer avec le chocolat blanc tempéré. Laisser cristalliser 6 heures à 17 °C avant de démouler.',
   'recipes/steps/cerisette-5.webp'),

  (4, 1, 'La coque',
   'Tempérer le chocolat blanc (45 °C, puis 26 °C, puis 28 °C) et mouler des coques en forme de framboise. Laisser cristalliser 20 minutes à 17 °C.',
   'recipes/steps/orangette-1.webp'),
  (4, 2, 'Le cœur framboise',
   'Écraser les framboises et les cuire 5 minutes avec le sucre. Ajouter la pectine, porter 1 minute à ébullition puis laisser refroidir.',
   'recipes/steps/cerisette-2.webp'),
  (4, 3, 'La ganache au lait',
   'Verser la crème chauffée à 80 °C sur le chocolat au lait et émulsionner. Laisser reposer 1 heure à température ambiante.',
   'recipes/steps/orangette-3.webp'),
  (4, 4, 'Le croustillant et le montage',
   'Mélanger le praliné et les crêpes dentelle et étaler sur 3 mm. Garnir les coques de cœur framboise et de ganache, poser un disque de croustillant puis refermer et laisser cristalliser 12 heures.',
   'recipes/steps/framboisette-coupe.webp'),

  (5, 1, 'La crème anglaise',
   'Chauffer le lait et la crème à 70 °C. Blanchir les jaunes avec le sucre, verser le lait chaud dessus puis cuire à la nappe à 84 °C en remuant sans cesse.',
   'recipes/steps/orangette-3.webp'),
  (5, 2, 'La base chocolat',
   'Verser la crème anglaise chaude sur le chocolat noir avec la fleur de sel et mixer 1 minute. Laisser refroidir puis réserver 4 heures au réfrigérateur.',
   'recipes/steps/orangette-4.webp'),
  (5, 3, 'Le moulage',
   'Turbiner la base 25 minutes en sorbetière, puis la couler dans des moules en demi-sphère. Lisser et placer 3 heures au congélateur à –18 °C.',
   'recipes/steps/dome-glace-moule.webp'),
  (5, 4, 'Le glaçage croquant',
   'Faire fondre 100 g de chocolat noir avec le beurre de cacao à 40 °C et ajouter les noisettes concassées. Démouler les dômes et les tremper rapidement dans le glaçage à 35 °C avant de servir.',
   'recipes/steps/dome-glace-glacage.webp');

-- 24 notes réparties sur les 5 recettes
INSERT INTO `ratings` (`user_id`, `recipe_id`, `rating`, `rated_at`) VALUES
  (2, 1, 5, '2026-09-19 18:00:00'), (3, 1, 5, '2026-09-20 19:00:00'), (4, 1, 4, '2026-09-21 12:00:00'),
  (5, 1, 5, '2026-09-22 09:00:00'), (6, 1, 5, '2026-09-23 20:00:00'), (7, 1, 4, '2026-09-24 17:00:00'),
  (2, 2, 4, '2026-09-21 18:00:00'), (4, 2, 4, '2026-09-22 18:00:00'), (5, 2, 5, '2026-09-23 18:00:00'),
  (6, 2, 3, '2026-09-24 18:00:00'), (7, 2, 4, '2026-09-25 18:00:00'),
  (2, 3, 4, '2026-09-23 10:00:00'), (3, 3, 5, '2026-09-23 11:00:00'), (4, 3, 5, '2026-09-24 11:00:00'),
  (5, 3, 4, '2026-09-25 11:00:00'), (6, 3, 4, '2026-09-26 11:00:00'),
  (3, 4, 4, '2026-09-25 15:00:00'), (4, 4, 5, '2026-09-26 15:00:00'), (6, 4, 5, '2026-09-27 15:00:00'),
  (7, 4, 4, '2026-09-28 15:00:00'),
  (2, 5, 3, '2026-09-27 14:00:00'), (3, 5, 4, '2026-09-28 14:00:00'), (5, 5, 4, '2026-09-29 14:00:00'),
  (7, 5, 3, '2026-09-30 14:00:00');

INSERT INTO `comments` (`author_id`, `recipe_id`, `subject`, `message`, `created_at`) VALUES
  (2, 1, 'Bluffant', 'Mes invités ont cru que c’était une vraie orange ! Le confit est parfait, pas trop sucré.', '2026-09-19 18:05:00'),
  (3, 1, NULL, 'Recette exigeante mais très bien expliquée. Le tempérage à 28 °C est vraiment la clé.', '2026-09-20 19:10:00'),
  (4, 1, 'Petite astuce', 'J’ai remplacé les crêpes dentelle par du riz soufflé, c’est aussi très bon.', '2026-09-21 12:20:00'),
  (5, 1, NULL, 'Trois essais pour réussir les coques, mais quel résultat. Merci la famille Moreau !', '2026-09-22 09:15:00'),
  (6, 1, 'Pour Noël', 'Je les ai offertes dans une jolie boîte, succès garanti.', '2026-09-23 20:30:00'),
  (7, 1, NULL, 'La ganache est un peu ferme chez moi, je mettrai 10 g de crème en plus la prochaine fois.', '2026-09-24 17:40:00'),
  (2, 1, NULL, 'Refaite ce week-end avec ma fille, elle a adoré mouler les coques.', '2026-09-25 16:00:00'),
  (3, 1, 'Conservation', 'Elles se gardent très bien une semaine dans une boîte hermétique à 17 °C.', '2026-09-26 10:00:00'),
  (4, 1, NULL, 'Le colorant orange fait toute la différence, ne pas l’oublier !', '2026-09-27 11:30:00'),
  (5, 1, NULL, 'J’ai testé <b>deux fois</b> : la deuxième était encore meilleure.', '2026-09-28 18:45:00'),
  (6, 1, NULL, 'Est-ce qu’on peut remplacer le chocolat noir par du chocolat au lait ?', '2026-09-29 13:00:00'),
  (7, 1, 'Merci', 'Une recette de famille partagée avec autant de soin, c’est rare.', '2026-09-30 21:10:00'),
  (2, 2, NULL, 'Très frais, parfait après un repas copieux.', '2026-09-21 18:10:00'),
  (6, 2, 'Un peu acide', 'Un peu trop acidulé à mon goût, j’ajouterai 10 g de sucre.', '2026-09-24 18:20:00'),
  (3, 3, NULL, 'Ma première praline trompe-l’œil et elle est réussie du premier coup !', '2026-09-23 11:10:00'),
  (4, 4, 'Magnifique', 'Le croustillant praliné avec la framboise, quelle association.', '2026-09-26 15:20:00'),
  (5, 5, NULL, 'Le glaçage croquant aux noisettes est addictif.', '2026-09-29 14:15:00');

INSERT INTO `contact_messages` (`name`, `email`, `subject`, `message`, `created_at`) VALUES
  ('Nadia Lemaire', 'nadia@exemple.be', 'Atelier enfants', 'Bonjour, organisez-vous des ateliers pour les enfants pendant les vacances de Toussaint ? Merci !', '2026-09-30 10:00:00');
