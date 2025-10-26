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
     *     description="Récupère la liste paginée des comptes bancaires avec possibilité de filtrage",
     *     operationId="getComptesBancaires",
     *     tags={"Comptes Bancaires"},
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
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         description="Filtrer par statut",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"actif", "inactif", "bloque"}
     *         )
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
     *                     @OA\Property(property="statut", type="string", enum={"actif", "inactif", "bloque"}),
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
     *     description="Crée un nouveau compte bancaire pour un utilisateur existant",
     *     operationId="createCompteBancaire",
     *     tags={"Comptes Bancaires"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "type_compte"},
     *             @OA\Property(property="numero", type="string", description="Numéro du compte (optionnel, généré automatiquement)", example="C001234"),
     *             @OA\Property(property="type_compte", type="string", enum={"Epargne", "Chéque"}, description="Type de compte"),
     *             @OA\Property(property="user_id", type="string", description="ID de l'utilisateur propriétaire", example="550e8400-e29b-41d4-a716-446655440000")
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
     *                 @OA\Property(property="type", type="string", enum={"Epargne", "Chéque"}),
     *                 @OA\Property(property="solde", type="number", format="float", example=0),
     *                 @OA\Property(property="dateCreation", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Données invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Données invalides"),
     *             @OA\Property(property="errors", type="object")
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
        $query = CompteBancaire::with('user')
            ->when($request->numero, fn($q) => $q->numero($request->numero))
            ->when($request->telephone, fn($q) => $q->client($request->telephone))
            ->when($request->statut, fn($q) => $q->statut($request->statut));

        $comptes = $query->paginate($request->limit ?? 10);

        $pagination = $this->generatePaginationData($comptes);
        $links = $this->generatePaginationLinks($comptes, $request->url());

        return $this->successResponse(
            CompteBancaireResource::collection($comptes),
            'Comptes bancaires récupérés avec succès',
            200,
            $pagination,
            $links
        );
    }

    public function store(StoreCompteBancaireRequest $request)
    {
        $compte = CompteBancaire::create($request->validated());

        return $this->successResponse(
            new CompteBancaireResource($compte->load('user')),
            'Compte bancaire créé avec succès',
            201
        );
    }

    public function show(CompteBancaire $compte)
    {
        return $this->successResponse(
            new CompteBancaireResource($compte->load('user')),
            'Compte bancaire récupéré avec succès',
            200
        );
    }

    public function update(StoreCompteBancaireRequest $request, CompteBancaire $compte)
    {
        $compte->update($request->validated());

        return $this->successResponse(
            new CompteBancaireResource($compte->load('user')),
            'Compte bancaire mis à jour avec succès',
            200
        );
    }

    public function destroy(CompteBancaire $compte)
    {
        $compte->delete();

        return $this->successResponse(
            null,
            'Compte bancaire supprimé avec succès',
            200
        );
    }
}