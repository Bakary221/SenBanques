<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SwaggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app['router']->get('/api/v1/documentation', function () {
            $content = file_get_contents(resource_path('views/swagger/index.blade.php'));
            return response()->make($content, 200, ['Content-Type' => 'text/html']);
        });

        $this->app['router']->get('/api/v1/docs', function () {
            // Documentation JSON statique pour l'exemple
            $docs = [
                "openapi" => "3.0.0",
                "info" => [
                    "title" => "API SenBanque",
                    "version" => "1.0.0",
                    "description" => "API pour la gestion des comptes bancaires"
                ],
                "servers" => [
                    [
                        "url" => "https://senbanques-2.onrender.com/api/v1",
                        "description" => "Serveur de Production"
                    ],
                    [
                        "url" => "http://localhost:8001/api/v1",
                        "description" => "Serveur de développement"
                    ]
                ],
                "security" => [
                    [
                        "bearerAuth" => []
                    ]
                ],
                "paths" => [
                    "/auth/login" => [
                        "post" => [
                            "summary" => "Connexion utilisateur",
                            "description" => "Authentifie un utilisateur et retourne un token d'accès JWT",
                            "operationId" => "loginUser",
                            "tags" => ["Authentification"],
                            "requestBody" => [
                                "required" => true,
                                "content" => [
                                    "application/json" => [
                                        "schema" => [
                                            "type" => "object",
                                            "required" => ["login", "password"],
                                            "properties" => [
                                                "login" => ["type" => "string", "description" => "Login ou email de l'utilisateur", "example" => "admin"],
                                                "password" => ["type" => "string", "description" => "Mot de passe", "example" => "password123"]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            "responses" => [
                                "200" => [
                                    "description" => "Connexion réussie",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "message" => ["type" => "string", "example" => "Connexion réussie."],
                                                    "data" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "user" => [
                                                                "type" => "object",
                                                                "properties" => [
                                                                    "id" => ["type" => "string", "example" => "550e8400-e29b-41d4-a716-446655440000"],
                                                                    "prenom" => ["type" => "string", "example" => "Admin"],
                                                                    "nom" => ["type" => "string", "example" => "System"],
                                                                    "email" => ["type" => "string", "example" => "admin@senbanque.com"],
                                                                    "role" => ["type" => "string", "example" => "admin"]
                                                                ]
                                                            ],
                                                            "access_token" => ["type" => "string", "description" => "Token JWT"],
                                                            "token_type" => ["type" => "string", "example" => "Bearer"],
                                                            "expires_in" => ["type" => "integer", "example" => 604800]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "422" => [
                                    "description" => "Informations d'identification incorrectes",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "message" => ["type" => "string", "example" => "Les informations d'identification sont incorrectes."],
                                                    "errors" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "login" => ["type" => "array", "items" => ["type" => "string"]]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "403" => [
                                    "description" => "Compte inactif",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => false],
                                                    "message" => ["type" => "string", "example" => "Votre compte n'est pas actif."],
                                                    "error" => ["type" => "string", "example" => "ACCOUNT_INACTIVE"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "/auth/refresh" => [
                        "post" => [
                            "summary" => "Rafraîchir le token d'accès",
                            "description" => "Génère un nouveau token d'accès et révoque l'ancien",
                            "operationId" => "refreshToken",
                            "tags" => ["Authentification"],
                            "security" => [["bearerAuth" => []]],
                            "responses" => [
                                "200" => [
                                    "description" => "Token rafraîchi avec succès",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "message" => ["type" => "string", "example" => "Token rafraîchi avec succès."],
                                                    "data" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "access_token" => ["type" => "string", "description" => "Nouveau token JWT"],
                                                            "token_type" => ["type" => "string", "example" => "Bearer"],
                                                            "expires_in" => ["type" => "integer", "example" => 604800]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "401" => [
                                    "description" => "Non authentifié",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "message" => ["type" => "string", "example" => "Unauthenticated."]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "/auth/logout" => [
                        "post" => [
                            "summary" => "Déconnexion utilisateur",
                            "description" => "Révoque le token d'accès actuel et déconnecte l'utilisateur",
                            "operationId" => "logoutUser",
                            "tags" => ["Authentification"],
                            "security" => [["bearerAuth" => []]],
                            "responses" => [
                                "200" => [
                                    "description" => "Déconnexion réussie",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "message" => ["type" => "string", "example" => "Déconnexion réussie."]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "401" => [
                                    "description" => "Non authentifié",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "message" => ["type" => "string", "example" => "Unauthenticated."]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "/auth/user" => [
                        "get" => [
                            "summary" => "Informations de l'utilisateur connecté",
                            "description" => "Récupère les informations détaillées de l'utilisateur actuellement authentifié",
                            "operationId" => "getCurrentUser",
                            "tags" => ["Authentification"],
                            "security" => [["bearerAuth" => []]],
                            "responses" => [
                                "200" => [
                                    "description" => "Informations utilisateur récupérées avec succès",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "data" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "user" => [
                                                                "type" => "object",
                                                                "properties" => [
                                                                    "id" => ["type" => "string", "example" => "550e8400-e29b-41d4-a716-446655440000"],
                                                                    "prenom" => ["type" => "string", "example" => "Admin"],
                                                                    "nom" => ["type" => "string", "example" => "System"],
                                                                    "email" => ["type" => "string", "example" => "admin@senbanque.com"],
                                                                    "login" => ["type" => "string", "example" => "admin"],
                                                                    "role" => ["type" => "string", "example" => "admin"],
                                                                    "statut" => ["type" => "string", "example" => "actif"]
                                                                ]
                                                            ],
                                                            "token_info" => [
                                                                "type" => "object",
                                                                "properties" => [
                                                                    "scopes" => ["type" => "array", "items" => ["type" => "string"]],
                                                                    "role" => ["type" => "string", "example" => "admin"],
                                                                    "permissions" => ["type" => "array", "items" => ["type" => "string"]]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "401" => [
                                    "description" => "Non authentifié",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "message" => ["type" => "string", "example" => "Unauthenticated."]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "/comptes" => [
                        "get" => [
                            "summary" => "Lister les comptes bancaires",
                            "description" => "Récupère la liste paginée des comptes bancaires avec possibilité de filtrage",
                            "operationId" => "getComptesBancaires",
                            "tags" => ["Comptes Bancaires"],
                            "parameters" => [
                                [
                                    "name" => "page",
                                    "in" => "query",
                                    "description" => "Numéro de la page",
                                    "required" => false,
                                    "schema" => [
                                        "type" => "integer",
                                        "minimum" => 1,
                                        "default" => 1
                                    ]
                                ],
                                [
                                    "name" => "limit",
                                    "in" => "query",
                                    "description" => "Nombre d'éléments par page",
                                    "required" => false,
                                    "schema" => [
                                        "type" => "integer",
                                        "minimum" => 1,
                                        "maximum" => 100,
                                        "default" => 10
                                    ]
                                ],
                                [
                                    "name" => "numero",
                                    "in" => "query",
                                    "description" => "Filtrer par numéro de compte",
                                    "required" => false,
                                    "schema" => [
                                        "type" => "string"
                                    ]
                                ],
                                [
                                    "name" => "telephone",
                                    "in" => "query",
                                    "description" => "Filtrer par téléphone du client",
                                    "required" => false,
                                    "schema" => [
                                        "type" => "string"
                                    ]
                                ],
                                [
                                    "name" => "statut",
                                    "in" => "query",
                                    "description" => "Filtrer par statut",
                                    "required" => false,
                                    "schema" => [
                                        "type" => "string",
                                        "enum" => ["actif", "inactif", "bloque"]
                                    ]
                                ]
                            ],
                            "responses" => [
                                "200" => [
                                    "description" => "Liste des comptes bancaires récupérée avec succès",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "message" => ["type" => "string", "example" => "Comptes bancaires récupérés avec succès"],
                                                    "data" => [
                                                        "type" => "array",
                                                        "items" => [
                                                            "type" => "object",
                                                            "properties" => [
                                                                "id" => ["type" => "string", "example" => "550e8400-e29b-41d4-a716-446655440000"],
                                                                "numeroCompte" => ["type" => "string", "example" => "C001234"],
                                                                "titulaire" => ["type" => "string", "example" => "Amadou Diallo"],
                                                                "type" => ["type" => "string", "enum" => ["Epargne", "Chéque"]],
                                                                "solde" => ["type" => "number", "format" => "float", "example" => 1250000],
                                                                "devise" => ["type" => "string", "example" => "FCFA"],
                                                                "dateCreation" => ["type" => "string", "format" => "date-time"],
                                                                "statut" => ["type" => "string", "enum" => ["actif", "inactif", "bloque"]],
                                                                "motifBlocage" => ["type" => "string", "nullable" => true],
                                                                "metadata" => [
                                                                    "type" => "object",
                                                                    "properties" => [
                                                                        "derniereModification" => ["type" => "string", "format" => "date-time"],
                                                                        "version" => ["type" => "integer", "example" => 1]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ],
                                                    "pagination" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "currentPage" => ["type" => "integer"],
                                                            "totalPages" => ["type" => "integer"],
                                                            "totalItems" => ["type" => "integer"],
                                                            "itemsPerPage" => ["type" => "integer"],
                                                            "hasNext" => ["type" => "boolean"],
                                                            "hasPrevious" => ["type" => "boolean"]
                                                        ]
                                                    ],
                                                    "links" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "self" => ["type" => "string"],
                                                            "first" => ["type" => "string"],
                                                            "last" => ["type" => "string"],
                                                            "next" => ["type" => "string", "nullable" => true],
                                                            "prev" => ["type" => "string", "nullable" => true]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "422" => [
                                    "description" => "Données de requête invalides",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => false],
                                                    "message" => ["type" => "string", "example" => "Données de requête invalides"],
                                                    "errors" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "page" => ["type" => "array", "items" => ["type" => "string"]],
                                                            "limit" => ["type" => "array", "items" => ["type" => "string"]]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "post" => [
                            "summary" => "Créer un nouveau compte bancaire",
                            "description" => "Crée un nouveau compte bancaire avec possibilité de créer un nouveau client ou utiliser un client existant",
                            "operationId" => "createCompteBancaire",
                            "tags" => ["Comptes Bancaires"],
                            "requestBody" => [
                                "required" => true,
                                "content" => [
                                    "application/json" => [
                                        "schema" => [
                                            "type" => "object",
                                            "required" => ["type_compte", "solde_initial", "nouveau_client"],
                                            "properties" => [
                                                "numero" => ["type" => "string", "description" => "Numéro du compte (optionnel, généré automatiquement si non fourni)", "example" => "C001234"],
                                                "type_compte" => ["type" => "string", "enum" => ["Epargne", "Chéque"], "description" => "Type de compte bancaire", "example" => "Epargne"],
                                                "solde_initial" => ["type" => "number", "format" => "float", "description" => "Solde initial du compte (minimum 10 000)", "example" => 10000],
                                                "devise" => ["type" => "string", "description" => "Devise du compte", "example" => "FCFA"],
                                                "nouveau_client" => ["type" => "boolean", "description" => "Indique si un nouveau client doit être créé", "example" => true],
                                                "client" => [
                                                    "type" => "object",
                                                    "description" => "Informations du client (requis si nouveau_client est true)",
                                                    "properties" => [
                                                        "prenom" => ["type" => "string", "description" => "Prénom du client", "example" => "Amadou"],
                                                        "nom" => ["type" => "string", "description" => "Nom du client", "example" => "Diallo"],
                                                        "email" => ["type" => "string", "format" => "email", "description" => "Email du client", "example" => "amadou.diallo@example.com"],
                                                        "telephone" => ["type" => "string", "description" => "Téléphone du client (format sénégalais)", "example" => "+221771234567"],
                                                        "adresse" => ["type" => "string", "description" => "Adresse du client", "example" => "Dakar, Sénégal"],
                                                        "profession" => ["type" => "string", "description" => "Profession du client", "example" => "Ingénieur"],
                                                        "cni" => ["type" => "string", "description" => "Numéro CNI (optionnel)", "example" => "1234567890123"]
                                                    ]
                                                ],
                                                "user_id" => ["type" => "string", "description" => "ID de l'utilisateur existant (requis si nouveau_client est false)", "example" => "550e8400-e29b-41d4-a716-446655440000"]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            "responses" => [
                                "201" => [
                                    "description" => "Compte bancaire créé avec succès",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "message" => ["type" => "string", "example" => "Compte bancaire créé avec succès"],
                                                    "data" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "id" => ["type" => "string", "example" => "550e8400-e29b-41d4-a716-446655440000"],
                                                            "numeroCompte" => ["type" => "string", "example" => "C001234"],
                                                            "titulaire" => ["type" => "string", "example" => "Amadou Diallo"],
                                                            "type" => ["type" => "string", "enum" => ["Epargne", "Chéque"], "example" => "Epargne"],
                                                            "solde" => ["type" => "number", "format" => "float", "example" => 10000],
                                                            "devise" => ["type" => "string", "example" => "FCFA"],
                                                            "dateCreation" => ["type" => "string", "format" => "date-time", "example" => "2023-10-26T12:00:00Z"]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "400" => [
                                    "description" => "Données de requête invalides",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => false],
                                                    "message" => ["type" => "string", "example" => "Données de requête invalides"],
                                                    "errors" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "type_compte" => ["type" => "array", "items" => ["type" => "string"]],
                                                            "solde_initial" => ["type" => "array", "items" => ["type" => "string"]],
                                                            "client.email" => ["type" => "array", "items" => ["type" => "string"]]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "/comptes/{id}" => [
                        "get" => [
                            "summary" => "Afficher un compte bancaire",
                            "description" => "Récupère les détails d'un compte bancaire spécifique",
                            "operationId" => "getCompteBancaire",
                            "tags" => ["Comptes Bancaires"],
                            "parameters" => [
                                [
                                    "name" => "id",
                                    "in" => "path",
                                    "required" => true,
                                    "description" => "ID du compte bancaire",
                                    "schema" => ["type" => "string"]
                                ]
                            ],
                            "responses" => [
                                "200" => [
                                    "description" => "Détails du compte bancaire récupérés avec succès",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "message" => ["type" => "string", "example" => "Compte bancaire récupéré avec succès"],
                                                    "data" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "id" => ["type" => "string", "example" => "550e8400-e29b-41d4-a716-446655440000"],
                                                            "numeroCompte" => ["type" => "string", "example" => "C001234"],
                                                            "titulaire" => ["type" => "string", "example" => "Amadou Diallo"],
                                                            "type" => ["type" => "string", "enum" => ["Epargne", "Chéque"]],
                                                            "solde" => ["type" => "number", "format" => "float", "example" => 1250000],
                                                            "dateCreation" => ["type" => "string", "format" => "date-time"]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "404" => [
                                    "description" => "Compte bancaire non trouvé",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => false],
                                                    "message" => ["type" => "string", "example" => "Compte bancaire non trouvé"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "put" => [
                            "summary" => "Mettre à jour un compte bancaire",
                            "description" => "Met à jour les informations d'un compte bancaire",
                            "operationId" => "updateCompteBancaire",
                            "tags" => ["Comptes Bancaires"],
                            "parameters" => [
                                [
                                    "name" => "id",
                                    "in" => "path",
                                    "required" => true,
                                    "description" => "ID du compte bancaire",
                                    "schema" => ["type" => "string"]
                                ]
                            ],
                            "requestBody" => [
                                "required" => true,
                                "content" => [
                                    "application/json" => [
                                        "schema" => [
                                            "type" => "object",
                                            "properties" => [
                                                "numero" => ["type" => "string", "description" => "Numéro du compte", "example" => "C001234"],
                                                "type_compte" => ["type" => "string", "enum" => ["Epargne", "Chéque"], "description" => "Type de compte"]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            "responses" => [
                                "200" => [
                                    "description" => "Compte bancaire mis à jour avec succès",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "message" => ["type" => "string", "example" => "Compte bancaire mis à jour avec succès"],
                                                    "data" => [
                                                        "type" => "object",
                                                        "properties" => [
                                                            "id" => ["type" => "string", "example" => "550e8400-e29b-41d4-a716-446655440000"],
                                                            "numeroCompte" => ["type" => "string", "example" => "C001234"],
                                                            "type" => ["type" => "string", "enum" => ["Epargne", "Chéque"]]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "404" => [
                                    "description" => "Compte bancaire non trouvé",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => false],
                                                    "message" => ["type" => "string", "example" => "Compte bancaire non trouvé"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "delete" => [
                            "summary" => "Supprimer un compte bancaire",
                            "description" => "Supprime un compte bancaire (soft delete)",
                            "operationId" => "deleteCompteBancaire",
                            "tags" => ["Comptes Bancaires"],
                            "parameters" => [
                                [
                                    "name" => "id",
                                    "in" => "path",
                                    "required" => true,
                                    "description" => "ID du compte bancaire",
                                    "schema" => ["type" => "string"]
                                ]
                            ],
                            "responses" => [
                                "200" => [
                                    "description" => "Compte bancaire supprimé avec succès",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => true],
                                                    "message" => ["type" => "string", "example" => "Compte bancaire supprimé avec succès"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                "404" => [
                                    "description" => "Compte bancaire non trouvé",
                                    "content" => [
                                        "application/json" => [
                                            "schema" => [
                                                "type" => "object",
                                                "properties" => [
                                                    "success" => ["type" => "boolean", "example" => false],
                                                    "message" => ["type" => "string", "example" => "Compte bancaire non trouvé"]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            return response()->json($docs, 200, [], JSON_PRETTY_PRINT);
        });
    }
}
