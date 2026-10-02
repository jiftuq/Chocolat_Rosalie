-- Compte MySQL/MariaDB dédié au site (SEC-09) : droits limités aux données,
-- aucun droit sur la structure. À exécuter une fois en tant qu'administrateur
-- après data/maison_rosalie.sql, en remplaçant le mot de passe :
--
--   mariadb -u root -p < data/create_app_user.sql

CREATE USER IF NOT EXISTS 'rosalie_app'@'localhost' IDENTIFIED BY 'mot-de-passe-a-changer';
GRANT SELECT, INSERT, UPDATE, DELETE ON `maison_rosalie`.* TO 'rosalie_app'@'localhost';
FLUSH PRIVILEGES;
