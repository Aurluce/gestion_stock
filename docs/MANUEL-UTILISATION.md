# Manuel d'utilisation de l'application Gestion Stock

Version du document : 1.0  
Date : 30 juin 2026  
Application : Gestion Stock  
Public cible : administrateurs, gestionnaires de stock, acheteurs, vendeurs, caissiers, responsables commerciaux

## Sommaire

- [1. Introduction](#1-introduction)
- [2. Principes généraux](#2-principes-généraux)
- [3. Connexion et session](#3-connexion-et-session)
- [4. Tableau de bord](#4-tableau-de-bord)
- [5. Module Structure](#5-module-structure)
- [6. Gestion des familles](#6-gestion-des-familles)
- [7. Gestion des produits](#7-gestion-des-produits)
- [8. Gestion des fournisseurs](#8-gestion-des-fournisseurs)
- [9. Gestion des clients](#9-gestion-des-clients)
- [10. Catégories clients](#10-catégories-clients)
- [11. Banques et mouvements bancaires](#11-banques-et-mouvements-bancaires)
- [12. Corbeille et restauration](#12-corbeille-et-restauration)
- [13. Module Approvisionnements](#13-module-approvisionnements)
- [14. Commandes fournisseurs](#14-commandes-fournisseurs)
- [15. Réceptions](#15-réceptions)
- [16. Bons d'entrée](#16-bons-dentrée)
- [17. Dons](#17-dons)
- [18. Factures fournisseurs](#18-factures-fournisseurs)
- [19. Paiements fournisseurs](#19-paiements-fournisseurs)
- [20. États achats](#20-états-achats)
- [21. Module Ventes](#21-module-ventes)
- [22. Commandes clients](#22-commandes-clients)
- [23. Bons de livraison](#23-bons-de-livraison)
- [24. Factures clients](#24-factures-clients)
- [25. Règlements clients](#25-règlements-clients)
- [26. Sorties de stock](#26-sorties-de-stock)
- [27. Vente au comptant](#27-vente-au-comptant)
- [28. États ventes](#28-états-ventes)
- [29. Tableau de bord des ventes](#29-tableau-de-bord-des-ventes)
- [30. Module Utilisateurs](#30-module-utilisateurs)
- [31. Groupes](#31-groupes)
- [32. Droits et affectation](#32-droits-et-affectation)
- [33. Utilisateurs](#33-utilisateurs)
- [34. Profil utilisateur](#34-profil-utilisateur)
- [35. Journal d'audit](#35-journal-daudit)
- [36. Impressions](#36-impressions)
- [37. Recherche et filtres](#37-recherche-et-filtres)
- [38. Gestion des erreurs fréquentes](#38-gestion-des-erreurs-fréquentes)
- [39. Bonnes pratiques d'exploitation](#39-bonnes-pratiques-dexploitation)
- [40. Profils utilisateurs recommandés](#40-profils-utilisateurs-recommandés)
- [41. Annexes](#41-annexes)

## 1. Introduction

Gestion Stock est une application web destinée au suivi complet des stocks, des achats, des ventes, des clients, des fournisseurs, des utilisateurs et des opérations financières associées.

Ce manuel explique comment utiliser l'application au quotidien. Il décrit les écrans, les principales actions, les procédures métier et les bonnes pratiques à respecter pour garantir la fiabilité des données.

## 2. Principes généraux

### 2.1 Organisation de l'application

Après connexion, l'application affiche une interface composée de trois zones :

- une barre latérale de navigation ;
- une barre supérieure avec le profil utilisateur ;
- une zone centrale contenant l'écran actif.

Les menus visibles dépendent des droits attribués à votre groupe utilisateur. Si un module ou un bouton n'apparaît pas, cela signifie généralement que votre compte ne dispose pas du droit correspondant.

### 2.2 Navigation

La barre latérale regroupe les modules :

- Tableau de bord ;
- Structure ;
- Approvisionnements ;
- Ventes ;
- Utilisateurs.

Sur mobile ou petit écran, utilisez le bouton menu pour ouvrir ou fermer la navigation.

### 2.3 Boutons et icônes courants

| Élément | Signification |
|---|---|
| `+` ou Nouveau | Créer un nouvel élément |
| Crayon | Modifier |
| Corbeille | Supprimer |
| Imprimante | Imprimer |
| Oeil ou détail | Consulter le détail |
| Coche | Valider |
| Interdiction | Annuler |
| Toggle | Activer ou désactiver |

### 2.4 Messages de confirmation

Certaines actions sensibles affichent une fenêtre de confirmation :

- suppression ;
- annulation ;
- validation ;
- restauration ;
- vidage de corbeille.

Lisez toujours le message avant de confirmer. Certaines actions peuvent modifier le stock ou les statuts de documents.

### 2.5 Messages flash

Après une action, l'application affiche un message :

- succès : action réalisée ;
- avertissement : action réalisée mais avec une attention particulière ;
- erreur : action non réalisée ou données incorrectes.

## 3. Connexion et session

### 3.1 Se connecter

1. Ouvrir l'application dans le navigateur.
2. Saisir le login.
3. Saisir le mot de passe.
4. Cliquer sur Connexion.

Si les identifiants sont corrects et que le compte est actif, l'application ouvre le tableau de bord.

### 3.2 Erreurs de connexion

Les causes fréquentes sont :

- login incorrect ;
- mot de passe incorrect ;
- compte désactivé ;
- problème de connexion à la base de données.

Si le compte est désactivé, contacter un administrateur.

### 3.3 Se déconnecter

1. Cliquer sur le profil utilisateur en haut à droite.
2. Cliquer sur Déconnexion.
3. Confirmer.

## 4. Tableau de bord

Le tableau de bord donne une vue synthétique de l'activité.

Selon vos droits, il peut afficher :

- nombre de produits actifs ;
- produits en alerte de stock ;
- commandes fournisseurs en cours ;
- commandes clients en cours ;
- chiffre d'affaires du mois ;
- encaissements du mois ;
- achats du mois ;
- paiements fournisseurs du mois ;
- graphiques de ventes et d'achats ;
- dernières activités ;
- mini-indicateurs clients, fournisseurs, banques et factures impayées.

### 4.1 Utilisation recommandée

Consulter le tableau de bord au début de chaque journée pour :

- identifier les produits sous seuil ;
- repérer les factures impayées ;
- suivre les commandes en cours ;
- vérifier les dernières actions sensibles.

## 5. Module Structure

Le module Structure contient les données de référence utilisées par les achats, ventes, stocks et rapports.

Il est recommandé de configurer ce module avant de saisir des opérations d'approvisionnement ou de vente.

## 6. Gestion des familles

Les familles permettent de classer les produits.

### 6.1 Accéder aux familles

Menu :

```text
Structure > Familles
```

### 6.2 Créer une famille

1. Cliquer sur Nouvelle famille.
2. Saisir le nom de la famille.
3. Saisir une description si nécessaire.
4. Enregistrer.

Le nom de la famille est obligatoire.

### 6.3 Modifier une famille

1. Cliquer sur l'icône de modification sur la ligne concernée.
2. Modifier les informations.
3. Enregistrer.

### 6.4 Supprimer une famille

1. Cliquer sur l'icône corbeille.
2. Confirmer la suppression.

Une famille liée à des produits peut être impossible à supprimer selon les règles de base de données.

## 7. Gestion des produits

Les produits constituent le coeur du stock. Chaque produit possède un stock courant, un prix d'achat, un prix de vente, une unité et un seuil d'alerte.

### 7.1 Accéder aux produits

Menu :

```text
Structure > Produits
```

### 7.2 Créer un produit

1. Cliquer sur Nouveau produit.
2. Sélectionner une famille.
3. Sélectionner éventuellement un produit père.
4. Saisir le nom du produit.
5. Saisir la description si nécessaire.
6. Saisir le prix d'achat.
7. Saisir le prix de vente.
8. Saisir le stock initial si nécessaire.
9. Saisir le seuil d'alerte.
10. Indiquer si le produit est périssable.
11. Saisir une date de péremption si le produit est périssable.
12. Choisir l'unité.
13. Indiquer si le produit est actif.
14. Enregistrer.

Le nom et la famille sont obligatoires.

### 7.3 Modifier un produit

1. Cliquer sur l'icône de modification.
2. Corriger les informations.
3. Enregistrer.

Attention : la modification manuelle du stock courant doit être utilisée avec prudence. Les entrées et sorties normales doivent passer par les modules d'approvisionnement, livraison ou sortie de stock.

### 7.4 Consulter le détail d'un produit

Cliquer sur l'action de détail lorsque disponible. Une fenêtre affiche les informations du produit.

### 7.5 Activer ou désactiver un produit

Un produit désactivé n'est plus proposé dans les listes de sélection opérationnelles.

Procédure :

1. Cliquer sur l'icône d'activation/désactivation.
2. Confirmer.

### 7.6 Supprimer un produit

1. Cliquer sur l'icône corbeille.
2. Confirmer.

La suppression peut alimenter la corbeille XML si le produit est pris en charge par les triggers de sauvegarde.

### 7.7 Bonnes pratiques produit

- Utiliser un nom clair et unique.
- Définir un seuil d'alerte réaliste.
- Vérifier les prix avant toute vente.
- Éviter de modifier le stock directement après le démarrage d'exploitation.
- Désactiver un produit plutôt que le supprimer si son historique doit rester lisible.

## 8. Gestion des fournisseurs

Les fournisseurs sont utilisés dans les commandes, factures et paiements fournisseurs.

### 8.1 Accéder aux fournisseurs

Menu :

```text
Structure > Fournisseurs
```

### 8.2 Créer un fournisseur

1. Cliquer sur Nouveau fournisseur.
2. Saisir le nom.
3. Compléter les coordonnées si disponibles.
4. Enregistrer.

Le nom est obligatoire.

### 8.3 Modifier un fournisseur

1. Cliquer sur l'icône de modification.
2. Modifier les informations.
3. Enregistrer.

### 8.4 Activer ou désactiver un fournisseur

Un fournisseur désactivé n'est plus proposé dans les nouvelles opérations.

### 8.5 Supprimer un fournisseur

1. Cliquer sur l'icône corbeille.
2. Confirmer.

Si le fournisseur est lié à des documents existants, la suppression peut être bloquée par les contraintes.

## 9. Gestion des clients

Les clients sont utilisés dans les commandes, livraisons, factures, règlements et ventes au comptant.

### 9.1 Accéder aux clients

Menu :

```text
Structure > Clients
```

### 9.2 Créer un client

1. Cliquer sur Nouveau client.
2. Saisir le nom.
3. Saisir le prénom si nécessaire.
4. Sélectionner une catégorie client si disponible.
5. Compléter les coordonnées.
6. Enregistrer.

Le nom est obligatoire.

### 9.3 Modifier un client

1. Cliquer sur l'icône de modification.
2. Modifier les informations.
3. Enregistrer.

### 9.4 Supprimer un client

1. Cliquer sur l'icône corbeille.
2. Confirmer.

### 9.5 Crédit client

Le solde crédit client est ajusté automatiquement par les factures et règlements. Une facture impayée ou partielle augmente le crédit dû. Un règlement le diminue.

## 10. Catégories clients

Les catégories permettent d'organiser les clients et d'associer éventuellement des remises.

### 10.1 Accéder aux catégories

Menu :

```text
Structure > Catégories clients
```

### 10.2 Créer une catégorie

1. Cliquer sur Nouvelle catégorie client.
2. Saisir le nom.
3. Saisir les paramètres complémentaires si affichés.
4. Enregistrer.

### 10.3 Supprimer une catégorie

La suppression est refusée si des clients sont liés à la catégorie.

## 11. Banques et mouvements bancaires

Le module Banque permet de gérer les banques et de suivre les versements/retraits.

### 11.1 Accéder aux banques

Menu :

```text
Structure > Banques
```

### 11.2 Créer une banque

1. Cliquer sur Nouvelle banque.
2. Saisir le nom de la banque.
3. Saisir le sigle, responsable, adresse, téléphone, email si nécessaire.
4. Enregistrer.

### 11.3 Modifier ou supprimer une banque

Utiliser les icônes de modification ou suppression sur la ligne concernée.

### 11.4 Consulter l'état des versements

Menu :

```text
Structure > État banque
```

Procédure :

1. Sélectionner une banque.
2. Choisir la date de début.
3. Choisir la date de fin.
4. Consulter les mouvements et le solde.

L'écran affiche :

- solde initial ;
- total entrées ;
- total sorties ;
- solde final ;
- liste des mouvements.

### 11.5 Ajouter un mouvement bancaire

1. Depuis l'état bancaire, cliquer sur Ajouter un mouvement.
2. Sélectionner le type : versement ou retrait.
3. Saisir le montant.
4. Saisir la date.
5. Ajouter une référence et une description si nécessaire.
6. Enregistrer.

Le montant doit être supérieur à zéro.

## 12. Corbeille et restauration

La corbeille contient certains éléments supprimés et sauvegardés en XML.

### 12.1 Accéder à la corbeille

Menu :

```text
Structure > Corbeille
```

### 12.2 Filtrer la corbeille

Vous pouvez filtrer par :

- type d'objet ;
- texte de recherche.

### 12.3 Consulter un élément

1. Cliquer sur l'action de consultation.
2. Vérifier les données sauvegardées.

### 12.4 Restaurer un élément

1. Cliquer sur Restaurer.
2. Confirmer.

La restauration dépend du type d'objet et des contraintes existantes. Par exemple, un produit peut nécessiter que sa famille existe encore.

### 12.5 Supprimer définitivement

1. Cliquer sur Supprimer définitivement.
2. Confirmer.

Cette action retire l'élément de la corbeille.

### 12.6 Vider la corbeille

1. Cliquer sur Vider la corbeille.
2. Confirmer.

Cette action est sensible et doit être réservée aux administrateurs.

## 13. Module Approvisionnements

Le module Approvisionnements couvre les opérations d'achat et d'entrée en stock.

## 14. Commandes fournisseurs

Une commande fournisseur permet de demander des produits à un fournisseur.

### 14.1 Accéder aux commandes fournisseurs

Menu :

```text
Approvisionnements > Commandes fourn.
```

### 14.2 Créer une commande fournisseur

1. Cliquer sur Nouvelle commande.
2. Sélectionner le fournisseur.
3. Ajouter une ou plusieurs lignes produit.
4. Pour chaque ligne, saisir :
   - produit ;
   - quantité commandée ;
   - prix unitaire ;
   - remise si applicable ;
   - observation si nécessaire.
5. Ajouter les observations générales.
6. Enregistrer.

La commande est créée en brouillon ou en état initial selon la configuration du modèle.

### 14.3 Modifier une commande fournisseur

1. Cliquer sur l'icône de modification.
2. Modifier les informations.
3. Enregistrer.

La modification est destinée aux commandes non finalisées.

### 14.4 Valider une commande fournisseur

1. Cliquer sur l'action de validation.
2. Confirmer.

La commande passe au statut envoyé. Elle devient disponible pour les réceptions.

### 14.5 Annuler une commande fournisseur

1. Cliquer sur l'action d'annulation.
2. Confirmer.

### 14.6 Supprimer une commande fournisseur

1. Cliquer sur l'icône corbeille.
2. Confirmer.

### 14.7 Imprimer une commande fournisseur

Cliquer sur l'icône imprimante. La page d'impression s'ouvre avec le bon de commande.

## 15. Réceptions

Une réception enregistre les quantités effectivement reçues.

### 15.1 Accéder aux réceptions

Menu :

```text
Approvisionnements > Réceptions
```

### 15.2 Créer une réception liée à une commande

1. Cliquer sur Nouvelle réception.
2. Sélectionner une commande fournisseur disponible.
3. Vérifier les lignes proposées.
4. Saisir les quantités reçues.
5. Saisir le prix unitaire si nécessaire.
6. Indiquer l'état du produit.
7. Ajouter des observations.
8. Enregistrer.

L'application empêche de recevoir une quantité supérieure à la quantité commandée.

### 15.3 Créer une réception non liée

Si l'écran le permet :

1. Laisser la commande vide.
2. Ajouter manuellement les produits reçus.
3. Enregistrer.

### 15.4 Valider une réception

1. Cliquer sur Valider.
2. Confirmer.

Effets de la validation :

- création automatique d'un bon d'entrée ;
- création des lignes de bon d'entrée ;
- augmentation automatique du stock ;
- création de mouvements de stock ;
- mise à jour du statut de réception ;
- mise à jour éventuelle du statut de commande fournisseur.

### 15.5 Imprimer une réception

Cliquer sur l'icône imprimante de la réception.

## 16. Bons d'entrée

Les bons d'entrée matérialisent l'entrée effective de produits en stock.

### 16.1 Accéder aux bons d'entrée

Menu :

```text
Approvisionnements > Bons d'entrée
```

### 16.2 Consulter les bons d'entrée

L'écran liste les bons d'entrée générés depuis :

- réception fournisseur ;
- don ;
- autre source prévue.

### 16.3 Imprimer un bon d'entrée

Cliquer sur l'icône imprimante.

### 16.4 Impact sur le stock

Le stock augmente lors de l'insertion des lignes de bon d'entrée. Ce traitement est automatisé par la base de données.

## 17. Dons

Le module Dons permet d'enregistrer une entrée gratuite de produits.

### 17.1 Accéder aux dons

Menu :

```text
Approvisionnements > Dons
```

### 17.2 Enregistrer un don

1. Cliquer sur Nouveau don.
2. Saisir le donateur.
3. Saisir le contact du donateur si disponible.
4. Saisir la date du don.
5. Ajouter une description.
6. Ajouter les produits donnés.
7. Saisir les quantités.
8. Enregistrer.

Effets :

- création du don ;
- création d'un bon d'entrée ;
- augmentation automatique du stock.

### 17.3 Modifier un don

1. Cliquer sur l'icône de modification.
2. Modifier les informations générales.
3. Enregistrer.

### 17.4 Supprimer un don

1. Cliquer sur l'icône corbeille.
2. Confirmer.

## 18. Factures fournisseurs

Les factures fournisseurs permettent d'enregistrer les montants dus aux fournisseurs.

### 18.1 Accéder aux factures fournisseurs

Menu :

```text
Approvisionnements > Factures fourn.
```

### 18.2 Créer une facture fournisseur

1. Cliquer sur Nouvelle facture.
2. Sélectionner le fournisseur.
3. Sélectionner éventuellement une commande fournisseur.
4. Saisir le numéro de facture.
5. Saisir la date de facture.
6. Saisir le montant HT.
7. Activer la TVA si applicable.
8. Saisir le taux de TVA.
9. Saisir la date d'échéance si connue.
10. Ajouter les lignes produit.
11. Enregistrer.

### 18.3 Modifier une facture fournisseur

1. Cliquer sur l'icône de modification.
2. Modifier les champs nécessaires.
3. Enregistrer.

### 18.4 Supprimer une facture fournisseur

1. Cliquer sur l'icône corbeille.
2. Confirmer.

La suppression peut être refusée si des paiements sont liés.

### 18.5 Imprimer une facture fournisseur

Cliquer sur l'icône imprimante.

## 19. Paiements fournisseurs

Les paiements fournisseurs enregistrent les sommes versées aux fournisseurs.

### 19.1 Accéder aux paiements fournisseurs

Menu :

```text
Approvisionnements > Paiements fourn.
```

### 19.2 Enregistrer un paiement

1. Cliquer sur Nouveau paiement.
2. Sélectionner le fournisseur.
3. Sélectionner la facture à payer.
4. Saisir le montant.
5. Saisir la date de paiement.
6. Sélectionner le mode de paiement.
7. Ajouter des observations.
8. Enregistrer.

### 19.3 Supprimer un paiement

1. Cliquer sur l'icône corbeille.
2. Confirmer.

### 19.4 Imprimer un reçu fournisseur

Cliquer sur l'icône imprimante.

## 20. États achats

Les états achats permettent de consulter les entrées de stock valorisées.

### 20.1 État journalier

1. Aller dans `Approvisionnements > États achats`.
2. Choisir la période jour.
3. Sélectionner la date.
4. Consulter les lignes.
5. Cliquer sur Imprimer si nécessaire.

### 20.2 État annuel

1. Choisir la période annuelle.
2. Sélectionner l'année.
3. Consulter les totaux mensuels.
4. Imprimer si nécessaire.

## 21. Module Ventes

Le module Ventes couvre les commandes clients, livraisons, factures, règlements, ventes au comptant, sorties de stock et états.

## 22. Commandes clients

Une commande client représente une demande de produits par un client.

### 22.1 Accéder aux commandes clients

Menu :

```text
Ventes > Commandes clients
```

### 22.2 Créer une commande client

1. Cliquer sur Nouvelle commande.
2. Sélectionner le client.
3. Choisir le type de vente.
4. Ajouter les produits.
5. Pour chaque ligne, saisir :
   - quantité ;
   - prix unitaire ;
   - remise si applicable.
6. Ajouter une observation si nécessaire.
7. Enregistrer.

### 22.3 Modifier une commande client

1. Cliquer sur l'icône de modification.
2. Modifier les lignes ou informations générales.
3. Enregistrer.

### 22.4 Consulter le détail d'une commande

Cliquer sur l'action de détail. Une fenêtre affiche la commande et ses lignes.

### 22.5 Imprimer une commande client

Cliquer sur l'icône imprimante.

### 22.6 Annuler une commande client

1. Cliquer sur l'action d'annulation.
2. Confirmer.

### 22.7 Supprimer une commande client

1. Cliquer sur l'icône corbeille.
2. Confirmer.

## 23. Bons de livraison

Un bon de livraison constate la sortie physique des produits vers le client.

### 23.1 Accéder aux bons de livraison

Menu :

```text
Ventes > Bons de livraison
```

### 23.2 Créer une livraison

1. Cliquer sur Nouvelle livraison.
2. Sélectionner une commande livrable.
3. Vérifier les produits commandés.
4. Saisir les quantités livrées.
5. Cocher livraison complète si toute la commande est livrée.
6. Ajouter des observations.
7. Enregistrer.

Effets :

- création du bon de livraison ;
- sortie automatique du stock ;
- création des mouvements de stock ;
- mise à jour éventuelle du statut de commande.

Si le stock est insuffisant, l'application bloque l'opération.

### 23.3 Imprimer un bon de livraison

Cliquer sur l'icône imprimante.

### 23.4 Annuler une livraison

1. Cliquer sur l'action d'annulation.
2. Confirmer.

Attention : vérifier les règles internes concernant le retour physique ou la correction de stock.

## 24. Factures clients

Une facture client est créée à partir d'une commande livrée.

### 24.1 Accéder aux factures clients

Menu :

```text
Ventes > Factures clients
```

### 24.2 Créer une facture client

1. Cliquer sur Nouvelle facture.
2. Sélectionner une commande livrée non encore facturée.
3. Saisir le taux de TVA si affiché.
4. Saisir la date d'échéance si nécessaire.
5. Enregistrer.

La facture est créée avec le statut impayée.

### 24.3 Imprimer une facture client

Cliquer sur l'icône imprimante.

### 24.4 Annuler une facture client

1. Cliquer sur l'action d'annulation.
2. Confirmer.

L'annulation exclut la facture des états de vente.

## 25. Règlements clients

Les règlements clients enregistrent les encaissements liés aux factures.

### 25.1 Accéder aux règlements

Menu :

```text
Ventes > Règlements clients
```

### 25.2 Enregistrer un règlement

1. Cliquer sur Nouveau règlement.
2. Sélectionner une facture impayée ou partielle.
3. Saisir le montant.
4. Sélectionner le mode de paiement.
5. Saisir une référence si disponible.
6. Ajouter des observations.
7. Enregistrer.

Le montant doit être supérieur à zéro.

### 25.3 Effet sur la facture

Après enregistrement :

- si le règlement couvre toute la facture, elle passe à payée ;
- si le règlement couvre une partie, elle passe à partielle ;
- si le règlement dépasse le reste dû, un avertissement est affiché.

### 25.4 Supprimer un règlement

1. Cliquer sur l'icône corbeille.
2. Confirmer.

Après suppression, le statut de la facture est recalculé.

### 25.5 Imprimer un reçu client

Cliquer sur l'icône imprimante.

## 26. Sorties de stock

Les sorties de stock servent à retirer des produits du stock hors livraison client.

### 26.1 Accéder aux sorties de stock

Menu :

```text
Ventes > Sorties de stock
```

### 26.2 Enregistrer une sortie

1. Cliquer sur Nouvelle sortie.
2. Sélectionner le produit.
3. Sélectionner éventuellement le client concerné.
4. Saisir la quantité.
5. Choisir le motif.
6. Ajouter une observation.
7. Enregistrer.

Le stock est diminué automatiquement. Si le stock est insuffisant, l'opération est refusée.

### 26.3 Imprimer un bon de sortie

Cliquer sur l'icône imprimante.

## 27. Vente au comptant

La vente au comptant permet d'effectuer une vente complète en une seule opération.

### 27.1 Accéder à la vente au comptant

Menu :

```text
Ventes > Vente au comptant
```

### 27.2 Enregistrer une vente au comptant

1. Sélectionner le client.
2. Ajouter les produits vendus.
3. Saisir les quantités.
4. Vérifier les prix.
5. Sélectionner le mode de paiement.
6. Ajouter une observation si nécessaire.
7. Valider la vente.

Effets automatiques :

- création d'une commande client type comptant ;
- création d'un bon de livraison complet ;
- sortie de stock ;
- création d'une facture ;
- création d'un règlement intégral ;
- facture marquée payée ;
- commande marquée réglée ;
- redirection vers le ticket de vente.

### 27.3 Imprimer le ticket

Après validation, la page du ticket s'affiche. Cliquer sur Imprimer.

### 27.4 Bonnes pratiques

- Vérifier le client avant validation.
- Vérifier le stock disponible.
- Vérifier les quantités.
- Vérifier le mode de paiement.
- Imprimer immédiatement le ticket si le client le demande.

## 28. États ventes

Les états ventes permettent de suivre le chiffre d'affaires et les factures.

### 28.1 État journalier

1. Aller dans `Ventes > États ventes`.
2. Choisir l'état du jour.
3. Consulter les factures du jour et les totaux.
4. Cliquer sur Imprimer si nécessaire.

### 28.2 État annuel

1. Choisir l'état annuel.
2. Sélectionner l'année.
3. Consulter les totaux par mois.
4. Imprimer si nécessaire.

## 29. Tableau de bord des ventes

Le tableau de bord des ventes affiche :

- ventes des 7 derniers jours ;
- top 5 produits vendus ;
- top 5 clients ;
- produits en alerte de stock ;
- factures en attente de règlement.

Utiliser cet écran pour piloter l'activité commerciale et anticiper les réapprovisionnements.

## 30. Module Utilisateurs

Le module Utilisateurs est réservé aux administrateurs ou responsables habilités.

## 31. Groupes

Les groupes servent à regrouper des utilisateurs ayant les mêmes droits.

### 31.1 Accéder aux groupes

Menu :

```text
Utilisateurs > Groupes
```

### 31.2 Créer un groupe

1. Cliquer sur Ajouter un groupe.
2. Saisir le nom.
3. Saisir la description.
4. Enregistrer.

### 31.3 Modifier un groupe

1. Cliquer sur l'icône de modification.
2. Modifier les informations.
3. Enregistrer.

### 31.4 Supprimer un groupe

1. Cliquer sur l'icône corbeille.
2. Confirmer.

Un groupe contenant des utilisateurs ne peut pas être supprimé.

## 32. Droits et affectation

### 32.1 Consulter les droits

Menu :

```text
Utilisateurs > Groupes > Droits
```

Les droits sont regroupés par module.

### 32.2 Affecter les droits à un groupe

1. Ouvrir la gestion des droits du groupe.
2. Cocher les droits nécessaires.
3. Décocher les droits à retirer.
4. Enregistrer.

Les utilisateurs du groupe héritent immédiatement des droits mis à jour.

### 32.3 Recommandations

- Créer un groupe par profil métier.
- Donner uniquement les droits nécessaires.
- Réserver la corbeille et le journal d'audit aux administrateurs.
- Réserver les suppressions définitives aux profils de confiance.

## 33. Utilisateurs

### 33.1 Accéder aux utilisateurs

Menu :

```text
Utilisateurs > Utilisateurs
```

### 33.2 Créer un utilisateur

1. Cliquer sur Ajouter un utilisateur.
2. Saisir le nom complet.
3. Saisir le login.
4. Saisir le mot de passe.
5. Sélectionner un groupe.
6. Cocher Actif si le compte doit être utilisable.
7. Définir une date d'expiration du mot de passe si nécessaire.
8. Enregistrer.

### 33.3 Modifier un utilisateur

1. Cliquer sur l'icône de modification.
2. Modifier les informations.
3. Saisir un nouveau mot de passe uniquement si vous souhaitez le changer.
4. Enregistrer.

### 33.4 Désactiver un utilisateur

Décochez Actif lors de la modification du compte. Un compte inactif ne peut plus se connecter.

### 33.5 Supprimer un utilisateur

1. Cliquer sur l'icône corbeille.
2. Confirmer.

L'application empêche un utilisateur de supprimer son propre compte.

## 34. Profil utilisateur

### 34.1 Accéder au profil

1. Cliquer sur le menu utilisateur en haut à droite.
2. Cliquer sur Profil.

### 34.2 Modifier son mot de passe

1. Saisir l'ancien mot de passe.
2. Saisir le nouveau mot de passe.
3. Confirmer le nouveau mot de passe.
4. Enregistrer.

Règles actuelles :

- l'ancien mot de passe doit être correct ;
- les deux nouveaux mots de passe doivent correspondre ;
- le nouveau mot de passe doit contenir au moins 4 caractères.

## 35. Journal d'audit

Le journal d'audit trace les actions importantes.

### 35.1 Accéder au journal

Menu :

```text
Utilisateurs > Journal audit
```

### 35.2 Informations affichées

Selon l'écran, le journal peut afficher :

- date et heure ;
- utilisateur ;
- action ;
- table cible ;
- identifiant ;
- anciennes valeurs ;
- nouvelles valeurs ;
- adresse IP ;
- navigateur.

### 35.3 Utilisation recommandée

Consulter le journal pour :

- contrôler les suppressions ;
- vérifier les modifications sensibles ;
- suivre les connexions ;
- investiguer une anomalie de stock ou de facturation.

## 36. Impressions

L'application propose des impressions dans plusieurs modules.

Documents imprimables :

- bon de commande fournisseur ;
- bon de réception ;
- bon d'entrée ;
- facture fournisseur ;
- reçu de paiement fournisseur ;
- commande client ;
- bon de livraison ;
- facture client ;
- reçu client ;
- bon de sortie ;
- ticket de vente ;
- état achats journalier ;
- état achats annuel ;
- état ventes journalier ;
- état ventes annuel.

### 36.1 Imprimer un document

1. Cliquer sur l'icône imprimante.
2. Vérifier l'aperçu.
3. Cliquer sur Imprimer.
4. Choisir l'imprimante ou l'impression PDF.

## 37. Recherche et filtres

Plusieurs écrans proposent :

- champ de recherche ;
- filtre par statut ;
- filtre par période ;
- filtre par motif ;
- filtre par banque ou année.

Utiliser les filtres pour réduire les listes et retrouver rapidement un document.

## 38. Gestion des erreurs fréquentes

### 38.1 "Droit requis"

Cause : votre groupe ne possède pas le droit demandé.  
Solution : demander à un administrateur de vérifier vos droits.

### 38.2 "Stock insuffisant"

Cause : la quantité demandée dépasse le stock disponible.  
Solution :

1. vérifier le stock du produit ;
2. enregistrer une entrée de stock si nécessaire ;
3. réduire la quantité à livrer ou à sortir.

### 38.3 "Ajoutez au moins un produit"

Cause : le formulaire a été soumis sans ligne produit.  
Solution : ajouter au moins une ligne valide.

### 38.4 "Le montant doit être supérieur à 0"

Cause : règlement, paiement ou mouvement bancaire avec montant nul ou négatif.  
Solution : saisir un montant positif.

### 38.5 "Impossible de supprimer"

Causes possibles :

- l'objet est lié à d'autres documents ;
- des utilisateurs sont rattachés au groupe ;
- des paiements sont liés à une facture ;
- une contrainte de base de données bloque l'opération.

Solution : vérifier les éléments liés ou désactiver l'objet au lieu de le supprimer.

## 39. Bonnes pratiques d'exploitation

### 39.1 Avant de démarrer l'exploitation

1. Créer les familles.
2. Créer les produits.
3. Créer les fournisseurs.
4. Créer les clients.
5. Créer les banques.
6. Créer les groupes utilisateurs.
7. Affecter les droits.
8. Créer les utilisateurs.
9. Vérifier le stock initial.

### 39.2 Pendant l'exploitation

- Enregistrer les entrées via réceptions, dons ou bons d'entrée.
- Enregistrer les sorties via livraisons, ventes comptant ou sorties de stock.
- Ne pas modifier manuellement le stock sans justification.
- Imprimer les documents importants.
- Contrôler régulièrement les stocks bas.
- Suivre les factures impayées.
- Vérifier les paiements fournisseurs.

### 39.3 En fin de journée

1. Consulter l'état des ventes du jour.
2. Consulter les encaissements.
3. Vérifier les sorties de stock.
4. Vérifier les produits sous seuil.
5. Imprimer ou archiver les états nécessaires.

### 39.4 En fin de mois

1. Contrôler les ventes mensuelles.
2. Contrôler les achats.
3. Contrôler les factures impayées.
4. Contrôler les paiements fournisseurs.
5. Exporter ou imprimer les états si nécessaire.
6. Effectuer une sauvegarde de la base.

## 40. Profils utilisateurs recommandés

### 40.1 Administrateur

Accès complet :

- utilisateurs ;
- droits ;
- structure ;
- corbeille ;
- audit ;
- achats ;
- ventes ;
- états.

### 40.2 Gestionnaire de stock

Droits recommandés :

- produits ;
- familles ;
- réceptions ;
- bons d'entrée ;
- sorties de stock ;
- mouvements de stock ;
- états achats ;
- consultation des stocks.

### 40.3 Acheteur

Droits recommandés :

- fournisseurs ;
- commandes fournisseurs ;
- réceptions ;
- factures fournisseurs ;
- paiements fournisseurs ;
- états achats.

### 40.4 Vendeur

Droits recommandés :

- clients ;
- commandes clients ;
- bons de livraison ;
- factures clients ;
- règlements clients ;
- vente au comptant ;
- états ventes.

### 40.5 Caissier

Droits recommandés :

- vente au comptant ;
- règlements clients ;
- impression tickets ;
- impression reçus.

### 40.6 Auditeur

Droits recommandés :

- consultation uniquement ;
- journal d'audit ;
- états ;
- tableaux de bord.

## 41. Annexes

### 41.1 Cycle achat résumé

```text
Commande fournisseur
    → Validation
    → Réception
    → Validation réception
    → Bon d'entrée
    → Stock augmenté
    → Facture fournisseur
    → Paiement fournisseur
```

### 41.2 Cycle vente à crédit résumé

```text
Commande client
    → Livraison
    → Stock diminué
    → Facture client
    → Règlement client
    → Facture payée
```

### 41.3 Cycle vente comptant résumé

```text
Vente comptant
    → Commande automatique
    → Livraison automatique
    → Facture automatique
    → Règlement automatique
    → Ticket
```

### 41.4 Règles critiques à retenir

- Une entrée de stock réelle passe par un bon d'entrée.
- Une sortie de stock réelle passe par une livraison ou une sortie de stock.
- Une vente comptant crée automatiquement tous les documents nécessaires.
- Une facture client doit être réglée pour passer à payée.
- Les droits utilisateur conditionnent l'accès aux écrans et aux actions.
- Les suppressions sensibles peuvent être consultées dans la corbeille ou l'audit selon le cas.
