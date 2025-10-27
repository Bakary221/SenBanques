# Logique d'Authentification - SenBanques Production

## Vue d'ensemble

Le système d'authentification de SenBanques utilise Laravel Passport pour l'authentification API basée sur des tokens JWT (JSON Web Tokens). Il prend en charge deux rôles principaux : `client` et `admin`, avec des permissions différenciées.

## Composants principaux

### 1. Modèle User (`app/Models/User.php`)
- Utilise `HasApiTokens` de Laravel Passport pour la gestion des tokens
- Clés primaires UUID au lieu d'auto-incréments
- Champs importants :
  - `login` et `email` pour l'authentification
  - `role` pour la gestion des permissions
  - `statut` pour activer/désactiver les comptes

### 2. AuthController (`app/Http/Controllers/Api/V1/AuthController.php`)

#### Méthode `login(Request $request)`
1. **Validation** : Vérifie que `login` et `password` sont fournis
2. **Recherche utilisateur** : Cherche par `login` ou `email`
3. **Vérification mot de passe** : Utilise `Hash::check()`
4. **Vérification statut** : S'assure que le compte est actif (`statut === 'actif'`)
5. **Création token** : Génère un token JWT avec claims personnalisés :
   - `role` : Rôle de l'utilisateur
   - `permissions` : Permissions basées sur le rôle
6. **Cookie sécurisé** : Stocke le token dans un cookie HTTP-only

#### Méthode `refresh(Request $request)`
- Révoque l'ancien token
- Crée un nouveau token avec les mêmes claims
- Met à jour le cookie

#### Méthode `logout(Request $request)`
- Révoque le token actuel
- Supprime le cookie d'authentification

#### Méthode `user(Request $request)`
- Retourne les informations de l'utilisateur connecté
- Inclut les détails du token (scopes, rôle, permissions)

#### Méthode `getUserPermissions(User $user)`
- Définit les permissions par rôle :
  - **Client** : `view_own_accounts`, `create_transaction`, `view_own_transactions`
  - **Admin** : Toutes les permissions client + `view_all_accounts`, `manage_accounts`, `view_all_transactions`, `manage_transactions`, `manage_users`

## Middleware d'authentification

### AuthMiddleware (`app/Http/Middleware/AuthMiddleware.php`)
- Vérifie l'authentification via le guard `api` (Passport)
- Retourne une erreur 401 si le token est manquant ou invalide

### RoleMiddleware (`app/Http/Middleware/RoleMiddleware.php`)
- Vérifie le rôle de l'utilisateur via les claims du token JWT
- Accepte plusieurs rôles en paramètre
- Retourne une erreur 403 si le rôle ne correspond pas

## Configuration

### AuthServiceProvider (`app/Providers/AuthServiceProvider.php`)
- Configure Passport :
  - Clés stockées dans `storage_path('oauth')`
  - Tokens expirent après 7 jours
  - Refresh tokens expirent après 14 jours
- Définit les scopes disponibles
- Configure les Gates pour les permissions

### Configuration Auth (`config/auth.php`)
- Guard `api` utilise le driver `passport`
- Provider `users` utilise le modèle `App\Models\User`

## Routes API (`routes/api.php`)

### Routes publiques
- `POST /api/v1/auth/login` : Connexion
- `POST /api/v1/auth/refresh` : Rafraîchissement token
- `POST /api/v1/auth/logout` : Déconnexion
- `GET /api/v1/auth/user` : Infos utilisateur

### Routes protégées
- Utilisent le middleware `auth:api`
- Routes admin utilisent `auth:api`, `auth.middleware`, `role:admin`

## Flux d'authentification

1. **Connexion** :
   - Client envoie `login`/`password`
   - Serveur valide les credentials
   - Génère token JWT avec claims
   - Retourne token + infos utilisateur
   - Stocke token dans cookie sécurisé

2. **Requêtes authentifiées** :
   - Client envoie token dans header `Authorization: Bearer {token}` ou cookie
   - `AuthMiddleware` vérifie la validité du token
   - `RoleMiddleware` vérifie les permissions si nécessaire
   - Requête traitée si authentifiée

3. **Rafraîchissement** :
   - Client peut rafraîchir le token avant expiration
   - Ancien token révoqué, nouveau généré

4. **Déconnexion** :
   - Token actuel révoqué
   - Cookie supprimé

## Sécurité

- **Tokens JWT** avec expiration (7 jours)
- **Cookies HTTP-only** pour éviter accès JavaScript
- **Claims personnalisés** pour rôle et permissions
- **Vérification statut compte** avant connexion
- **Hachage mots de passe** avec bcrypt
- **UUID** pour les IDs utilisateurs

## Gestion des rôles et permissions

- **Client** : Accès limité à ses propres comptes et transactions
- **Admin** : Accès complet à tous les comptes, transactions et utilisateurs
- Permissions définies dans `AuthController::getUserPermissions()`
- Gates définis dans `AuthServiceProvider` pour contrôle granulaire