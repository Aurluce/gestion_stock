-- Seed de test pour le module Ventes
-- Catégories clients
INSERT INTO structure.categorie_client (nom_categorie, taux_remise, description) VALUES
('Particulier', 0, 'Client particulier sans remise'),
('Grossiste', 10, 'Client grossiste avec remise de 10%'),
('VIP', 15, 'Client VIP avec remise de 15%');

-- Clients
INSERT INTO structure.client (id_categorie_client, nom, prenom, adresse, ville, tel, email, type_client) VALUES
(1, 'Mballa', 'Jean', 'Quartier Tsinga', 'Yaoundé', '699112233', 'jean.mballa@example.com', 'particulier'),
(2, 'Société NDI & Fils', NULL, 'Avenue Kennedy', 'Douala', '677445566', 'contact@ndifils.cm', 'entreprise'),
(1, 'Fotso', 'Marie', 'Quartier Banengo', 'Bafoussam', '655998877', 'marie.fotso@example.com', 'particulier');

-- Familles de produits
INSERT INTO structure.famille (nom_famille, description) VALUES
('Boissons', 'Boissons gazeuses et jus'),
('Alimentation générale', 'Produits alimentaires de base'),
('Hygiène & Entretien', 'Produits d''hygiène et de nettoyage');

-- Produits
INSERT INTO structure.produit (id_famille, code_barre, nom_produit, description, prix_achat, prix_vente, stock_actuel, seuil_alerte, perissable, date_peremption, unite) VALUES
(1, '6001234560011', 'Coca-Cola 1.5L', 'Boisson gazeuse 1.5 litre', 600, 800, 100, 10, true, '2026-12-31', 'pce'),
(1, '6001234560028', 'Eau minérale 1.5L', 'Bouteille d''eau minérale', 300, 450, 200, 20, true, '2026-12-31', 'pce'),
(2, '6001234560035', 'Riz parfumé 25kg', 'Sac de riz parfumé 25kg', 15000, 18000, 50, 5, false, NULL, 'sac'),
(2, '6001234560042', 'Huile végétale 5L', 'Bidon d''huile végétale', 6000, 7500, 80, 10, true, '2027-06-30', 'bidon'),
(3, '6001234560059', 'Savon de Marseille', 'Savon traditionnel', 250, 400, 150, 15, false, NULL, 'pce');
