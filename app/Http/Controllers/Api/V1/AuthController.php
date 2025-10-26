<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="API SenBanque - Authentification",
 *     version="1.0.0",
 *     description="API d'authentification pour SenBanque"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8001/api/v1",
 *     description="Serveur de développement"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Connexion utilisateur",
     *     description="Authentifie un utilisateur et retourne un token d'accès JWT",
     *     operationId="loginUser",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login", "password"},
     *             @OA\Property(property="login", type="string", description="Login ou email de l'utilisateur", example="admin"),
     *             @OA\Property(property="password", type="string", description="Mot de passe", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Connexion réussie."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                     @OA\Property(property="prenom", type="string", example="Admin"),
     *                     @OA\Property(property="nom", type="string", example="System"),
     *                     @OA\Property(property="email", type="string", example="admin@senbanque.com"),
     *                     @OA\Property(property="role", type="string", example="admin")
     *                 ),
     *                 @OA\Property(property="access_token", type="string", description="Token JWT"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=604800)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Informations d'identification incorrectes",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Les informations d'identification sont incorrectes."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="login", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Compte inactif",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Votre compte n'est pas actif."),
     *             @OA\Property(property="error", type="string", example="ACCOUNT_INACTIVE")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('login', $request->login)
                   ->orWhere('email', $request->login)
                   ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Les informations d\'identification sont incorrectes.'],
            ]);
        }

        // Vérifier si l'utilisateur est actif
        if ($user->statut !== 'actif') {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte n\'est pas actif.',
                'error' => 'ACCOUNT_INACTIVE'
            ], 403);
        }

        // Créer le token avec claims personnalisés
        $tokenResult = $user->createToken('Personal Access Token', ['*'], [
            'role' => $user->role ?? 'client', // Rôle par défaut
            'permissions' => $this->getUserPermissions($user),
        ]);

        $token = $tokenResult->accessToken;

        // Stocker le token dans un cookie sécurisé
        $cookie = Cookie::make(
            'access_token',
            $token,
            60 * 24 * 7, // 7 jours
            '/',
            null,
            true, // secure
            true, // httpOnly
            false,
            'Strict'
        );

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'prenom' => $user->prenom,
                    'nom' => $user->nom,
                    'email' => $user->email,
                    'role' => $user->role ?? 'client',
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 60 * 24 * 7 * 60, // secondes
            ]
        ])->withCookie($cookie);
    }

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     summary="Rafraîchir le token d'accès",
     *     description="Génère un nouveau token d'accès et révoque l'ancien",
     *     operationId="refreshToken",
     *     tags={"Authentification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token rafraîchi avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token rafraîchi avec succès."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string", description="Nouveau token JWT"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=604800)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        // Révoquer l'ancien token
        $request->user()->token()->revoke();

        // Créer un nouveau token
        $tokenResult = $user->createToken('Personal Access Token', ['*'], [
            'role' => $user->role ?? 'client',
            'permissions' => $this->getUserPermissions($user),
        ]);

        $token = $tokenResult->accessToken;

        // Nouveau cookie
        $cookie = Cookie::make(
            'access_token',
            $token,
            60 * 24 * 7,
            '/',
            null,
            true,
            true,
            false,
            'Strict'
        );

        return response()->json([
            'success' => true,
            'message' => 'Token rafraîchi avec succès.',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 60 * 24 * 7 * 60,
            ]
        ])->withCookie($cookie);
    }

    /**
     * Déconnexion et révocation du token
     */
    public function logout(Request $request)
    {
        // Révoquer le token actuel
        $request->user()->token()->revoke();

        // Supprimer le cookie
        $cookie = Cookie::forget('access_token');

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie.'
        ])->withCookie($cookie);
    }

    /**
     * @OA\Get(
     *     path="/auth/user",
     *     summary="Informations de l'utilisateur connecté",
     *     description="Récupère les informations détaillées de l'utilisateur actuellement authentifié",
     *     operationId="getCurrentUser",
     *     tags={"Authentification"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Informations utilisateur récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                     @OA\Property(property="prenom", type="string", example="Admin"),
     *                     @OA\Property(property="nom", type="string", example="System"),
     *                     @OA\Property(property="email", type="string", example="admin@senbanque.com"),
     *                     @OA\Property(property="login", type="string", example="admin"),
     *                     @OA\Property(property="role", type="string", example="admin"),
     *                     @OA\Property(property="statut", type="string", example="actif")
     *                 ),
     *                 @OA\Property(
     *                     property="token_info",
     *                     type="object",
     *                     @OA\Property(property="scopes", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="role", type="string", example="admin"),
     *                     @OA\Property(property="permissions", type="array", @OA\Items(type="string"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $token = $request->user()->token();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'prenom' => $user->prenom,
                    'nom' => $user->nom,
                    'email' => $user->email,
                    'login' => $user->login,
                    'role' => $user->role ?? 'client',
                    'statut' => $user->statut,
                ],
                'token_info' => [
                    'scopes' => $token->scopes,
                    'role' => $token->getClaim('role'),
                    'permissions' => $token->getClaim('permissions'),
                ]
            ]
        ]);
    }

    /**
     * Récupérer les permissions de l'utilisateur
     */
    private function getUserPermissions(User $user): array
    {
        $role = $user->role ?? 'client';

        $permissions = [
            'client' => [
                'view_own_accounts',
                'create_transaction',
                'view_own_transactions',
            ],
            'admin' => [
                'view_all_accounts',
                'manage_accounts',
                'view_all_transactions',
                'manage_transactions',
                'manage_users',
            ],
        ];

        return $permissions[$role] ?? $permissions['client'];
    }
}
