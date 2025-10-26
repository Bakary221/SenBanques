# Documentation API - Endpoint Lister les Comptes Bancaires

## Vue d'ensemble

Cette API permet de récupérer la liste des comptes bancaires avec pagination, filtres et format de réponse standardisé.

## Endpoints disponibles

### 1. GET /api/v1/comptes
**Alias :** GET /api/v1/comptes-bancaires

## Paramètres de requête

| Paramètre | Type | Obligatoire | Description | Valeur par défaut |
|-----------|------|-------------|-------------|-------------------|
| `page` | integer | Non | Numéro de la page | 1 |
| `limit` | integer | Non | Nombre d'éléments par page (1-100) | 10 |
| `numero` | string | Non | Filtrer par numéro de compte | null |
| `telephone` | string | Non | Filtrer par téléphone du client | null |
| `statut` | string | Non | Filtrer par statut (actif, inactif, bloque) | null |

## Format de réponse

### Réponse de succès

```json
{
  "success": true,
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "numeroCompte": "C00123456",
      "titulaire": "Amadou Diallo",
      "type": "epargne",
      "solde": 1250000,
      "devise": "FCFA",
      "dateCreation": "2023-03-15T00:00:00Z",
      "statut": "bloque",
      "motifBlocage": "Inactivité de 30+ jours",
      "metadata": {
        "derniereModification": "2023-06-10T14:30:00Z",
        "version": 1
      }
    }
  ],
  "pagination": {
    "currentPage": 1,
    "totalPages": 3,
    "totalItems": 25,
    "itemsPerPage": 10,
    "hasNext": true,
    "hasPrevious": false
  },
  "links": {
    "self": "/api/v1/comptes?page=1&limit=10",
    "next": "/api/v1/comptes?page=2&limit=10",
    "first": "/api/v1/comptes?page=1&limit=10",
    "last": "/api/v1/comptes?page=3&limit=10"
  }
}
```

### Réponse d'erreur

```json
{
  "success": false,
  "message": "Données de requête invalides",
  "errors": {
    "page": ["Le numéro de page doit être un entier positif"],
    "limit": ["La limite doit être comprise entre 1 et 100"]
  }
}
```

## Codes de statut HTTP

| Code | Description |
|------|-------------|
| 200 | Succès |
| 400 | Données de requête invalides |
| 422 | Erreur de validation |
| 500 | Erreur interne du serveur |

## Exemples d'utilisation avec Postman

### 1. Récupérer tous les comptes (pagination par défaut)

**Requête :**
```
GET http://localhost:8001/api/v1/comptes
```

### 2. Récupérer les comptes avec pagination personnalisée

**Requête :**
```
GET http://localhost:8001/api/v1/comptes?page=2&limit=5
```

### 3. Filtrer par numéro de compte

**Requête :**
```
GET http://localhost:8001/api/v1/comptes?numero=C000001
```

### 4. Filtrer par téléphone du client

**Requête :**
```
GET http://localhost:8001/api/v1/comptes?telephone=771234567
```

### 5. Filtrer par statut

**Requête :**
```
GET http://localhost:8001/api/v1/comptes?statut=actif
GET http://localhost:8001/api/v1/comptes?statut=bloque
GET http://localhost:8001/api/v1/comptes?statut=inactif
```

### 6. Combinaison de filtres et pagination

**Requête :**
```
GET http://localhost:8001/api/v1/comptes?page=1&limit=10&statut=actif&telephone=771234567
```

## Configuration Postman

### Headers requis :
```
Accept: application/json
Content-Type: application/json
```

### Configuration CORS :
L'API supporte les requêtes cross-origin. Assurez-vous que votre client gère correctement les headers CORS.

## Fonctionnalités implémentées

### 1. **Validations personnalisées**
- **Téléphone sénégalais** : Doit commencer par 77, 78, 70, 75 ou 76 et contenir exactement 9 chiffres
- **CNI sénégalais** : Doit contenir exactement 13 chiffres

### 2. **Scopes de modèle**
- **Scope global** : `active` - Prêt pour implémentation future de soft delete
- **Scope local** : `numero($numero)` - Filtrer par numéro de compte
- **Scope local** : `client($telephone)` - Filtrer par téléphone du client
- **Scope local** : `statut($statut)` - Filtrer par statut (actif, inactif, bloque)

### 3. **Génération automatique des numéros de compte**
- Format : `C` suivi de 6 chiffres (ex: C000001, C000002, etc.)
- Incrémentation automatique lors de la création

### 4. **Trait de réponse API standardisé**
- Format de réponse cohérent pour tous les endpoints
- Gestion automatique de la pagination
- Gestion des erreurs standardisée

### 5. **Resource API**
- Transformation des données selon le format demandé
- Calcul automatique du statut du compte
- Métadonnées incluses

### 6. **Middleware CORS personnalisé**
- Gestion des requêtes preflight (OPTIONS)
- Headers CORS configurés pour tous les endpoints API

### 7. **Gestion d'exceptions personnalisée**
- Classe `ApiException` pour erreurs métier
- Gestion centralisée des erreurs

### 8. **Index de base de données**
- Index sur les colonnes fréquemment utilisées pour optimiser les performances
- Index sur : numero, type_compte, user_id, statut, email, login, cni, created_at

### 9. **Gestion des statuts et blocages**
- **Statuts disponibles** : actif, inactif, bloque
- **Motifs de blocage** : Stockés en base de données
- **Méthodes métier** : `bloquer()`, `activer()`, `desactiver()`
- **Vérifications** : `estActif()`, `estBloque()`, `estInactif()`

## Architecture des fichiers

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   │           └── CompteBancaireController.php
│   ├── Middleware/
│   │   └── HandleCors.php
│   ├── Requests/
│   │   ├── ListComptesRequest.php
│   │   ├── StoreUserRequest.php
│   │   ├── StoreClientRequest.php
│   │   ├── StoreAdminRequest.php
│   │   ├── StoreCompteBancaireRequest.php
│   │   └── StoreTransactionRequest.php
│   └── Resources/
│       └── CompteBancaireResource.php
├── Rules/
│   ├── TelephoneSenegalais.php
│   └── CniSenegalais.php
├── Traits/
│   └── ApiResponseTrait.php
├── Exceptions/
│   └── ApiException.php
└── Models/
    └── CompteBancaire.php

routes/
└── api.php

config/
└── cors.php
```

## Tests recommandés

### Tests fonctionnels :
1. Pagination fonctionne correctement
2. Filtres appliquent correctement
3. Format de réponse respecté
4. Gestion d'erreurs appropriée

### Tests de validation :
1. Téléphone invalide rejeté
2. CNI invalide rejeté
3. Paramètres de pagination validés

### Tests de performance :
1. Temps de réponse acceptable
2. Utilisation des index vérifiée

## Sécurité

- Validation stricte des entrées
- Protection contre les injections SQL via Eloquent
- Limitation du taux de requêtes (throttle)
- Headers CORS configurés

## Évolutivité

- Structure versionnée (v1) prête pour évolution
- Traits réutilisables pour autres endpoints
- Middleware modulaire
- Scopes extensibles pour nouveaux filtres