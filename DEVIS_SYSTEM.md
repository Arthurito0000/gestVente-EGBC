# Système de Gestion des Devis - Documentation Complète

## Vue d'ensemble

Le système de devis permet aux **Administrateurs** et **Vendeurs** de créer, gérer et convertir des devis estimatifs en factures. Les devis ne touchent pas le stock tant qu'ils ne sont pas convertis en factures.

---

## Fonctionnalités Principales

### 1. Gestion Complète des Devis (CRUD)
- ✅ **Création** : Formulaire dynamique avec ajout d'articles illimités
- ✅ **Consultation** : Vue détaillée avec tous les articles et totaux
- ✅ **Modification** : Édition possible pour les devis en brouillon ou envoyés
- ✅ **Suppression** : Possible sauf pour les devis déjà convertis en factures

### 2. Statuts des Devis
- **Brouillon** : Devis en cours de préparation (modifiable)
- **Envoyé** : Devis transmis au client (modifiable, convertible)
- **Accepté** : Devis validé par le client (convertible)
- **Refusé** : Devis rejeté par le client
- **Converti** : Devis transformé en facture (non modifiable, non supprimable)

### 3. Types d'Articles
- **Matériel** : Produits physiques (affecte le stock lors de la conversion)
- **Main d'Œuvre** : Services (n'affecte pas le stock)

### 4. Actions Disponibles
- 📄 **Imprimer** : Impression directe du devis formaté
- 📥 **Télécharger PDF** : Export PDF avec logo et en-tête professionnelle
- 🔄 **Convertir en Facture** : Transformation automatique avec mise à jour des stocks
- 📋 **Dupliquer** : Création d'un nouveau devis basé sur un existant
- ✏️ **Modifier** : Édition des informations et articles
- 🗑️ **Supprimer** : Suppression (sauf si converti)

---

## Architecture Technique

### Modèles
- **Quote** : Devis principal avec informations client et totaux
- **QuoteItem** : Articles du devis (matériel ou main d'œuvre)

### Relations
```
Quote
├── belongsTo User (vendeur)
├── hasMany QuoteItem (articles)
└── belongsTo Sale (facture convertie)

QuoteItem
├── belongsTo Quote
└── belongsTo Product
```

### Tables de Base de Données

#### Table `quotes`
- `id` : Identifiant unique
- `numero_devis` : Numéro unique (format: DEV-YYYYMMDD-XXX)
- `user_id` : Vendeur/Admin créateur
- `client_nom` : Nom du client
- `objet` : Description du projet
- `total_materiel` : Total des articles matériels
- `total_main_oeuvre` : Total de la main d'œuvre
- `total_general` : Total global
- `statut` : État du devis
- `date_devis` : Date de création
- `date_validite` : Date d'expiration
- `converted_sale_id` : ID de la facture si converti

#### Table `quote_items`
- `id` : Identifiant unique
- `quote_id` : Référence au devis
- `product_id` : Référence au produit
- `designation` : Nom de l'article
- `quantite` : Quantité
- `prix_unitaire` : Prix unitaire
- `prix_total` : Prix total (quantite × prix_unitaire)
- `type` : 'materiel' ou 'main_oeuvre'

---

## Permissions et Accès

### Rôles Autorisés
- ✅ **Administrateur** : Accès complet (création, modification, conversion, suppression)
- ✅ **Vendeur** : Accès complet (création, modification, conversion, suppression)
- ❌ **Gérant de Stock** : Aucun accès aux devis

### Permission Requise
- `create-sales` : Nécessaire pour accéder au module devis

---

## Processus de Conversion en Facture

### Étapes Automatiques
1. **Vérification du stock** : Pour tous les articles de type "matériel"
2. **Création de la vente** : Génération d'une nouvelle facture
3. **Mise à jour des stocks** : Déduction des quantités vendues
4. **Création des mouvements** : Traçabilité des sorties de stock
5. **Marquage du devis** : Statut passé à "converti"
6. **Liaison** : Lien entre le devis et la facture créée

### Sécurité de la Conversion
- ❌ Impossible si stock insuffisant
- ❌ Impossible si devis déjà converti
- ❌ Impossible si statut = "brouillon" ou "refusé"
- ✅ Possible uniquement pour statuts "envoyé" ou "accepté"

---

## Format du Document Devis

### En-tête
- Logo de l'entreprise (centré, petite taille)
- **ETS GLASS LE BIEN**
- VITRERIE-MENUISERIE-ALUMINIUM
- Vente des Vitres - aluminium - accessoires
- Adresse et téléphones

### Corps du Document
- Numéro de devis unique
- Date et lieu (Douala)
- Nom du client
- Objet du devis
- Tableau des articles avec :
  - N° d'ordre
  - Désignations
  - Quantités
  - Prix unitaires
  - Prix totaux
- **TOTAL MATERIEL**
- **MAIN D'ŒUVRE**
- **TOTAL GENERAL**

### Pied de Page
- Montant en lettres (français)
- Signatures : Mme CHRISTINE (Lendi) et Mr JOEL

---

## Routes Disponibles

```php
GET    /quotes                    - Liste des devis
GET    /quotes/create             - Formulaire de création
POST   /quotes                    - Enregistrer un nouveau devis
GET    /quotes/{quote}            - Détails d'un devis
GET    /quotes/{quote}/edit       - Formulaire de modification
PUT    /quotes/{quote}            - Mettre à jour un devis
DELETE /quotes/{quote}            - Supprimer un devis
GET    /quotes/{quote}/print      - Imprimer le devis
GET    /quotes/{quote}/pdf        - Télécharger en PDF
POST   /quotes/{quote}/convert    - Convertir en facture
POST   /quotes/{quote}/duplicate  - Dupliquer le devis
```

---

## Utilisation

### Créer un Nouveau Devis

1. Accéder au menu **Ventes > Devis**
2. Cliquer sur **"Nouveau Devis"**
3. Remplir les informations client :
   - Nom du client (obligatoire)
   - Date du devis (obligatoire)
   - Date de validité (optionnel)
   - Objet du devis (optionnel)
4. Ajouter des articles :
   - Cliquer sur **"+ Ajouter un article"**
   - Sélectionner un produit
   - Modifier la désignation si nécessaire
   - Choisir le type (Matériel ou Main d'Œuvre)
   - Indiquer la quantité
   - Ajuster le prix unitaire
5. Vérifier les totaux dans le résumé
6. Cliquer sur **"Créer le Devis"**

### Modifier un Devis

1. Ouvrir le devis depuis la liste
2. Cliquer sur **"Modifier"**
3. Modifier les informations souhaitées
4. Changer le statut si nécessaire
5. Cliquer sur **"Enregistrer les Modifications"**

### Convertir un Devis en Facture

1. Ouvrir le devis (statut "envoyé" ou "accepté")
2. Cliquer sur **"Convertir en Facture"**
3. Confirmer l'action
4. Le système vérifie automatiquement :
   - Disponibilité des stocks
   - Validité du devis
5. Si succès : redirection vers la facture créée
6. Si échec : message d'erreur avec détails

### Imprimer ou Télécharger

- **Imprimer** : Ouvre une fenêtre d'impression avec le devis formaté
- **Télécharger PDF** : Génère et télécharge un PDF professionnel

---

## Données de Test

Le seeder `QuoteSeeder` crée 4 devis de démonstration :

1. **DEV-20250104-001** : Brouillon (vendeur)
   - Client : Mme CHRISTINE (Lendi)
   - 16 articles (15 matériels + 1 main d'œuvre)
   - Total : ~981 500 Fcfa

2. **DEV-20250103-001** : Envoyé (admin)
   - Client : Mr JOEL
   - 5 articles (4 matériels + 1 main d'œuvre)
   - Total : ~213 000 Fcfa

3. **DEV-20250102-001** : Accepté (vendeur)
   - Client : Société BATITECH
   - 4 articles (3 matériels + 1 main d'œuvre)
   - Total : ~490 000 Fcfa

4. **DEV-20250101-001** : Refusé (admin)
   - Client : M. DUPONT
   - 3 articles (2 matériels + 1 main d'œuvre)
   - Total : ~380 000 Fcfa

### Exécuter le Seeder

```bash
php artisan db:seed --class=QuoteSeeder
```

---

## Règles Métier

### Modification
- ✅ Possible si statut = "brouillon" ou "envoyé"
- ❌ Impossible si statut = "accepté", "refusé" ou "converti"

### Suppression
- ✅ Possible si statut ≠ "converti"
- ❌ Impossible si le devis a été transformé en facture

### Conversion
- ✅ Possible si statut = "envoyé" ou "accepté"
- ✅ Possible si stock suffisant pour tous les articles matériels
- ❌ Impossible si déjà converti
- ❌ Impossible si statut = "brouillon" ou "refusé"

### Numérotation
- Format : **DEV-YYYYMMDD-XXX**
- Exemple : DEV-20250104-001
- Incrémentation automatique par jour

---

## Calculs Automatiques

### Totaux
- **Total Matériel** = Σ (prix_total des articles de type "materiel")
- **Total Main d'Œuvre** = Σ (prix_total des articles de type "main_oeuvre")
- **Total Général** = Total Matériel + Total Main d'Œuvre

### Prix Total par Article
- **Prix Total** = Quantité × Prix Unitaire

---

## Fichiers Créés

### Backend
- `app/Models/Quote.php` : Modèle principal
- `app/Models/QuoteItem.php` : Modèle des articles
- `app/Http/Controllers/QuoteController.php` : Contrôleur complet
- `database/migrations/*_create_quotes_table.php` : Migration
- `database/seeders/QuoteSeeder.php` : Données de test

### Frontend
- `resources/views/quotes/index.blade.php` : Liste avec recherche
- `resources/views/quotes/create.blade.php` : Formulaire de création
- `resources/views/quotes/edit.blade.php` : Formulaire de modification
- `resources/views/quotes/show.blade.php` : Détails du devis
- `resources/views/quotes/print.blade.php` : Template d'impression
- `resources/views/quotes/pdf.blade.php` : Template PDF
- `resources/views/quotes/partials/table.blade.php` : Table réutilisable

### Configuration
- `routes/web.php` : Routes ajoutées
- `resources/views/layouts/app.blade.php` : Menu mis à jour

---

## Commandes Utiles

### Exécuter la Migration
```bash
php artisan migrate
```

### Générer des Données de Test
```bash
php artisan db:seed --class=QuoteSeeder
```

### Réinitialiser et Recréer
```bash
php artisan migrate:fresh --seed
```

---

## Support et Maintenance

### Logs
Les erreurs de conversion sont loguées avec détails :
- Stock insuffisant
- Produit introuvable
- Erreurs de transaction

### Sécurité
- Toutes les routes protégées par authentification
- Permission `create-sales` requise
- Validation stricte des données
- Transactions DB pour la conversion
- Verrous de base de données pour éviter les race conditions

---

## Évolutions Futures Possibles

- [ ] Envoi automatique par email au client
- [ ] Signature électronique
- [ ] Historique des modifications
- [ ] Notifications de rappel avant expiration
- [ ] Export Excel des devis
- [ ] Statistiques et rapports sur les devis
- [ ] Templates de devis prédéfinis
- [ ] Multi-devises
- [ ] Remises et promotions
- [ ] Conditions générales de vente personnalisables

---

**Date de création** : 04 Janvier 2025  
**Version** : 1.0.0  
**Auteur** : Système GestVentes EGBC
