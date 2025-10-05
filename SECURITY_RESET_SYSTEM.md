# 🔐 Système de Réinitialisation de Mot de Passe Avancé

## Vue d'ensemble

Le système de réinitialisation de mot de passe de GestVentes-EGBC offre une sécurité renforcée avec des fonctionnalités avancées d'audit, de notification et de gestion d'urgence.

## ✨ Fonctionnalités Principales

### 1. **Indicateur de Force du Mot de Passe**
- Évaluation en temps réel de la sécurité du mot de passe
- Échelle de 5 niveaux : Très faible → Très fort
- Conseils visuels avec barre de progression colorée
- Validation côté client et serveur

### 2. **Notifications Email Automatiques**
- Notification immédiate lors du changement de mot de passe
- Informations de sécurité : IP, navigateur, timestamp
- Template email personnalisé avec branding
- Alerte en cas d'activité suspecte

### 3. **Audit Trail Complet**
- Traçabilité de toutes les tentatives de réinitialisation
- Stockage des métadonnées : IP, User-Agent, timestamp
- Statistiques de sécurité sur 30 jours
- Détection des patterns suspects

### 4. **Interface d'Administration d'Urgence**
- Réinitialisation directe par les administrateurs
- Envoi de liens de réinitialisation d'urgence
- Historique des actions administratives
- Statistiques de sécurité en temps réel

### 5. **UX Améliorée**
- Timer de compte à rebours (60 minutes)
- Conseils de sécurité rotatifs
- Expiration automatique des liens
- Interface moderne et intuitive

## 🏗️ Architecture Technique

### Modèles
- **PasswordResetLog** : Audit trail des tentatives
- **User** : Utilisateurs avec relations audit
- **PasswordChangedNotification** : Notifications email

### Contrôleurs
- **AuthController** : Gestion des resets standard
- **UserManagementController** : Interface d'urgence admin

### Middlewares
- **CheckUserStatus** : Vérification statut utilisateur
- **CheckPermission** : Contrôle d'accès granulaire

### Vues
- `auth/email_verif.blade.php` : Demande de lien
- `auth/password_reset.blade.php` : Formulaire de reset
- `users/emergency-reset.blade.php` : Interface admin

## 🔧 Configuration

### Variables d'Environnement
```bash
# Copier le fichier de configuration
cp .env.security.example .env.security

# Variables principales
PASSWORD_RESET_TOKEN_EXPIRE=60      # Durée validité (minutes)
PASSWORD_RESET_THROTTLE=60          # Délai entre demandes (secondes)
PASSWORD_RESET_MAX_ATTEMPTS=5       # Max tentatives/heure/IP
PASSWORD_MIN_STRENGTH=2             # Force minimum (0-4)
PASSWORD_MIN_LENGTH=8               # Longueur minimum
```

### Configuration Email
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

## 📊 Utilisation

### Pour les Utilisateurs
1. **Demande de réinitialisation**
   - Aller sur `/email/verify`
   - Saisir l'email du compte
   - Recevoir le lien par email

2. **Réinitialisation**
   - Cliquer sur le lien reçu
   - Saisir le nouveau mot de passe
   - Respecter les critères de sécurité
   - Confirmer le changement

### Pour les Administrateurs
1. **Interface d'urgence**
   - Aller sur `/users/emergency-reset`
   - Sélectionner l'utilisateur concerné
   - Choisir : reset direct ou envoi de lien
   - Justifier la raison de l'action

2. **Suivi des statistiques**
   - Consulter l'historique des actions
   - Analyser les patterns de sécurité
   - Identifier les tentatives suspectes

## 🛡️ Sécurité

### Niveaux de Protection
1. **Validation côté client** : Vérification JavaScript
2. **Validation serveur** : Contrôles PHP Laravel
3. **Throttling** : Limitation des tentatives
4. **Audit logging** : Traçabilité complète
5. **Notifications** : Alertes automatiques

### Critères de Mot de Passe
- **Longueur minimum** : 8 caractères
- **Complexité** : Lettres, chiffres, symboles
- **Éviter** : Informations personnelles
- **Recommandé** : 12+ caractères, unique

### Expiration et Sécurité
- **Liens valides** : 60 minutes maximum
- **Tokens uniques** : Générés aléatoirement
- **Usage unique** : Invalidation après utilisation
- **Protection CSRF** : Tokens de sécurité

## 📈 Monitoring

### Métriques Disponibles
- Nombre de demandes de reset (30j)
- Taux de réussite/échec
- IPs uniques impliquées
- Actions administratives d'urgence

### Alertes Automatiques
- Tentatives multiples depuis même IP
- Échecs répétés sur même compte
- Actions d'urgence administratives
- Changements de mot de passe

## 🚀 Déploiement

### Étapes d'Installation
```bash
# 1. Exécuter les migrations
php artisan migrate

# 2. Publier les assets
php artisan vendor:publish --tag=laravel-assets

# 3. Configurer les permissions
php artisan db:seed --class=RolePermissionSeeder

# 4. Tester la configuration email
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com'); });
```

### Vérifications Post-Déploiement
- [ ] Emails de notification fonctionnels
- [ ] Interface d'urgence accessible aux admins
- [ ] Audit trail opérationnel
- [ ] Timer et UX fonctionnels
- [ ] Permissions correctement configurées

## 🔍 Dépannage

### Problèmes Courants

**1. Emails non reçus**
```bash
# Vérifier la configuration MAIL_*
php artisan config:cache
php artisan queue:work
```

**2. Erreur de permissions**
```bash
# Re-seeder les permissions
php artisan db:seed --class=RolePermissionSeeder
```

**3. Timer ne fonctionne pas**
- Vérifier que JavaScript est activé
- Contrôler la console pour erreurs
- Valider la configuration des tokens

### Logs Utiles
```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs de sécurité
SELECT * FROM password_reset_logs ORDER BY created_at DESC LIMIT 10;
```

## 📞 Support

### Contacts
- **Développeur** : Arthur (arthur@gesteventes.com)
- **Administrateur** : admin@gesteventes.com

### Comptes de Test
- **Admin** : admin@gesteventes.com / password123
- **Stock** : stock@gesteventes.com / stock123
- **Vendeur** : vendeur@gesteventes.com / vendeur123

---

**Version** : 1.0  
**Dernière mise à jour** : Octobre 2025  
**Compatibilité** : Laravel 12, PHP 8.2+
