<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListComptesRequest;
use App\Http\Requests\StoreCompteBancaireRequest;
use App\Http\Resources\CompteBancaireResource;
use App\Models\CompteBancaire;
use App\Traits\ApiResponseTrait;

/**
 * @OA\Info(
 *     title="API SenBanque",
 *     version="1.0.0",
 *     description="API pour la gestion des comptes bancaires"
 * )
 *
 * @OA\Server(
 *     url="https://senbanques-2.onrender.com/",
 *     description="Serveur de Production"
 * )
 * 
 * * @OA\Server(
 *     url="http://localhost:8001/api/v1",
 *     description="Serveur de développement"
 * )
 */
class CompteBancaireController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/comptes",
     *     summary="Lister les comptes bancaires",
     *     description="Récupère la liste paginée des comptes bancaires actifs (type Épargne ou Chèque). Les admins voient tous les comptes, les clients voient uniquement leurs comptes.",
     *     operationId="getComptesBancaires",
     *     tags={"Comptes Bancaires"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de la page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre d'éléments par page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=10)
     *     ),
     *     @OA\Parameter(
     *         name="numero",
     *         in="query",
     *         description="Filtrer par numéro de compte",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="telephone",
     *         in="query",
     *         description="Filtrer par téléphone du client",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des comptes bancaires récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Comptes bancaires récupérés avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                     @OA\Property(property="numeroCompte", type="string", example="C001234"),
     *                     @OA\Property(property="titulaire", type="string", example="Amadou Diallo"),
     *                     @OA\Property(property="type", type="string", enum={"Epargne", "Chéque"}),
     *                     @OA\Property(property="solde", type="number", format="float", example=1250000),
     *                     @OA\Property(property="devise", type="string", example="FCFA"),
     *                     @OA\Property(property="dateCreation", type="string", format="date-time"),
     *                     @OA\Property(property="statut", type="string", enum={"actif"}),
     *                     @OA\Property(property="motifBlocage", type="string", nullable=true),
     *                     @OA\Property(
     *                         property="metadata",
     *                         type="object",
     *                         @OA\Property(property="derniereModification", type="string", format="date-time"),
     *                         @OA\Property(property="version", type="integer", example=1)
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="currentPage", type="integer"),
     *                 @OA\Property(property="totalPages", type="integer"),
     *                 @OA\Property(property="totalItems", type="integer"),
     *                 @OA\Property(property="itemsPerPage", type="integer"),
     *                 @OA\Property(property="hasNext", type="boolean"),
     *                 @OA\Property(property="hasPrevious", type="boolean")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="self", type="string"),
     *                 @OA\Property(property="first", type="string"),
     *                 @OA\Property(property="last", type="string"),
     *                 @OA\Property(property="next", type="string", nullable=true),
     *                 @OA\Property(property="prev", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données de requête invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Données de requête invalides"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="page", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="limit", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Post(
      *     path="/comptes",
      *     summary="Créer un nouveau compte bancaire",
      *     description="Crée un nouveau compte bancaire avec possibilité de créer un nouveau client ou utiliser un client existant",
      *     operationId="createCompteBancaire",
      *     tags={"Comptes Bancaires"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"type_compte", "solde_initial", "nouveau_client"},
      *             @OA\Property(property="numero", type="string", description="Numéro du compte (optionnel, généré automatiquement si non fourni)", example="C001234"),
      *             @OA\Property(property="type_compte", type="string", enum={"Epargne", "Chéque"}, description="Type de compte bancaire", example="Epargne"),
      *             @OA\Property(property="solde_initial", type="number", format="float", description="Solde initial du compte (minimum 10 000)", example=10000),
      *             @OA\Property(property="devise", type="string", description="Devise du compte", example="FCFA"),
      *             @OA\Property(property="nouveau_client", type="boolean", description="Indique si un nouveau client doit être créé", example=true),
      *             @OA\Property(
      *                 property="client",
      *                 type="object",
      *                 description="Informations du client (requis si nouveau_client est true)",
      *                 @OA\Property(property="prenom", type="string", description="Prénom du client", example="Amadou"),
      *                 @OA\Property(property="nom", type="string", description="Nom du client", example="Diallo"),
      *                 @OA\Property(property="email", type="string", format="email", description="Email du client", example="amadou.diallo@example.com"),
      *                 @OA\Property(property="telephone", type="string", description="Téléphone du client (format sénégalais)", example="+221771234567"),
      *                 @OA\Property(property="adresse", type="string", description="Adresse du client", example="Dakar, Sénégal"),
      *                 @OA\Property(property="profession", type="string", description="Profession du client", example="Ingénieur"),
      *                 @OA\Property(property="cni", type="string", description="Numéro CNI (optionnel)", example="1234567890123")
      *             ),
      *             @OA\Property(property="user_id", type="string", description="ID de l'utilisateur existant (requis si nouveau_client est false)", example="550e8400-e29b-41d4-a716-446655440000")
      *         )
      *     ),
      *     @OA\Response(
      *         response=201,
      *         description="Compte bancaire créé avec succès",
      *         @OA\JsonContent(
      *             @OA\Property(property="success", type="boolean", example=true),
      *             @OA\Property(property="message", type="string", example="Compte bancaire créé avec succès"),
      *             @OA\Property(
      *                 property="data",
      *                 type="object",
      *                 @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
      *                 @OA\Property(property="numeroCompte", type="string", example="C001234"),
      *                 @OA\Property(property="titulaire", type="string", example="Amadou Diallo"),
      *                 @OA\Property(property="type", type="string", enum={"Epargne", "Chéque"}, example="Epargne"),
      *                 @OA\Property(property="solde", type="number", format="float", example=10000),
      *                 @OA\Property(property="devise", type="string", example="FCFA"),
      *                 @OA\Property(property="dateCreation", type="string", format="date-time", example="2023-10-26T12:00:00Z")
      *             )
      *         )
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Données de requête invalides",
      *         @OA\JsonContent(
      *             @OA\Property(property="success", type="boolean", example=false),
      *             @OA\Property(property="message", type="string", example="Données de requête invalides"),
      *             @OA\Property(
      *                 property="errors",
      *                 type="object",
      *                 @OA\Property(property="type_compte", type="array", @OA\Items(type="string")),
      *                 @OA\Property(property="solde_initial", type="array", @OA\Items(type="string")),
      *                 @OA\Property(property="client.email", type="array", @OA\Items(type="string"))
      *             )
      *         )
      *     )
      * )
     *
     * @OA\Get(
     *     path="/comptes/{id}",
     *     summary="Afficher un compte bancaire",
     *     description="Récupère les détails d'un compte bancaire spécifique",
     *     operationId="getCompteBancaire",
     *     tags={"Comptes Bancaires"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du compte bancaire",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du compte bancaire récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Compte bancaire récupéré avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="numeroCompte", type="string", example="C001234"),
     *                 @OA\Property(property="titulaire", type="string", example="Amadou Diallo"),
     *                 @OA\Property(property="type", type="string", enum={"Epargne", "Chéque"}),
     *                 @OA\Property(property="solde", type="number", format="float", example=1250000),
     *                 @OA\Property(property="dateCreation", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte bancaire non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Compte bancaire non trouvé")
     *         )
     *     )
     * )
     *
     * @OA\Put(
     *     path="/comptes/{id}",
     *     summary="Mettre à jour un compte bancaire",
     *     description="Met à jour les informations d'un compte bancaire",
     *     operationId="updateCompteBancaire",
     *     tags={"Comptes Bancaires"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du compte bancaire",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="numero", type="string", description="Numéro du compte", example="C001234"),
     *             @OA\Property(property="type_compte", type="string", enum={"Epargne", "Chéque"}, description="Type de compte")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte bancaire mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Compte bancaire mis à jour avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="numeroCompte", type="string", example="C001234"),
     *                 @OA\Property(property="type", type="string", enum={"Epargne", "Chéque"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte bancaire non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Compte bancaire non trouvé")
     *         )
     *     )
     * )
     *
     * @OA\Delete(
     *     path="/comptes/{id}",
     *     summary="Supprimer un compte bancaire",
     *     description="Supprime un compte bancaire (soft delete)",
     *     operationId="deleteCompteBancaire",
     *     tags={"Comptes Bancaires"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du compte bancaire",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte bancaire supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Compte bancaire supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte bancaire non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Compte bancaire non trouvé")
     *         )
     *     )
     * )
     */
    public function index(ListComptesRequest $request)
    {
        $user = auth()->user();

        $query = CompteBancaire::withoutGlobalScopes()->with('user')
            ->where('statut', 'actif')
            ->whereIn('type_compte', ['Epargne', 'Chéque']);

        // Si l'utilisateur n'est pas admin, filtrer par ses propres comptes
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Appliquer les filtres supplémentaires
        $query->when($request->numero, fn($q) => $q->numero($request->numero))
              ->when($request->telephone, fn($q) => $q->client($request->telephone));

        $comptes = $query->paginate($request->limit ?? 10);

        $pagination = $this->generatePaginationData($comptes);
        $links = $this->generatePaginationLinks($comptes, $request->url());

        $message = $user->role === 'admin'
            ? 'Comptes bancaires récupérés avec succès'
            : 'Vos comptes bancaires récupérés avec succès';

        return $this->successResponse(
            CompteBancaireResource::collection($comptes),
            $message,
            200,
            $pagination,
            $links
        );
    }

    public function store(StoreCompteBancaireRequest $request)
    {
        $validated = $request->validated();

        // Créer l'utilisateur si nouveau_client est true
        if ($validated['nouveau_client']) {
            $user = \App\Models\User::create([
                'prenom' => $validated['prenom'],
                'nom' => $validated['nom'],
                'login' => $validated['email'], // Utiliser l'email comme login
                'email' => $validated['email'],
                'statut' => 'actif',
                'telephone' => $validated['telephone'],
                'adresse' => $validated['adresse'],
                'profession' => $validated['profession'],
                'cni' => $validated['cni'] ?? 'TEMP-' . strtoupper(substr(md5(uniqid()), 0, 8)), // CNI temporaire si non fourni
                'code' => 'USR-' . strtoupper(substr(md5(uniqid()), 0, 8)), // Générer un code unique
                'password' => bcrypt('password123'), // Mot de passe temporaire
            ]);
            $validated['user_id'] = $user->id;
        }

        // Créer le compte bancaire
        $compte = CompteBancaire::create([
            'numero' => $validated['numero'] ?? null,
            'type_compte' => $validated['type_compte'],
            'statut' => 'actif',
            'user_id' => $validated['user_id'],
        ]);

        // Déclencher l'événement de création du compte
        \App\Events\CompteBancaireCree::dispatch($user, $compte, 'password123');

        return $this->successResponse(
            new CompteBancaireResource($compte->load('user')),
            'Compte bancaire créé avec succès',
            201
        );
    }

    public function show($id)
    {
        $compte = CompteBancaire::withoutGlobalScopes()->find($id);

        if (!$compte) {
            return $this->errorResponse('Compte bancaire non trouvé', 404);
        }

        // Vérifier les permissions d'accès
        $user = auth()->user();
        if ($user->role !== 'admin' && $compte->user_id !== $user->id) {
            return $this->errorResponse('Accès non autorisé à ce compte', 403);
        }

        return $this->successResponse(
            new CompteBancaireResource($compte->load('user')),
            'Compte bancaire récupéré avec succès',
            200
        );
    }

    public function update(StoreCompteBancaireRequest $request, $id)
    {
        $compte = CompteBancaire::withoutGlobalScopes()->find($id);

        if (!$compte) {
            return $this->errorResponse('Compte bancaire non trouvé', 404);
        }

        // Vérifier les permissions d'accès
        $user = auth()->user();
        if ($user->role !== 'admin' && $compte->user_id !== $user->id) {
            return $this->errorResponse('Accès non autorisé à ce compte', 403);
        }

        // Pour la mise à jour, on ne valide que les champs autorisés
        $validated = $request->validate([
            'numero' => 'nullable|string|unique:compte_bancaires,numero,' . $compte->id . '|regex:/^C\d{6}$/',
            'type_compte' => 'sometimes|in:Epargne,Chéque',
        ]);

        $compte->update($validated);

        return $this->successResponse(
            new CompteBancaireResource($compte->load('user')),
            'Compte bancaire mis à jour avec succès',
            200
        );
    }

    public function destroy($id)
    {
        $compte = CompteBancaire::withoutGlobalScopes()->find($id);

        if (!$compte) {
            return $this->errorResponse('Compte bancaire non trouvé', 404);
        }

        // Vérifier les permissions d'accès
        $user = auth()->user();
        if ($user->role !== 'admin' && $compte->user_id !== $user->id) {
            return $this->errorResponse('Accès non autorisé à ce compte', 403);
        }

        $compte->delete();

        return $this->successResponse(
            null,
            'Compte bancaire supprimé avec succès',
            200
        );
    }
}