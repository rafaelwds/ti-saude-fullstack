<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        tags: ['Autenticação'],
        summary: 'Registra um novo usuário',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Rafael'),
                    new OA\Property(property: 'email', type: 'string', example: 'rafael@email.com'),
                    new OA\Property(property: 'password', type: 'string', example: '123456'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuário criado com sucesso'
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação'
            ),
        ]
    )]
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => $user,
        ], 201);
    }

    #[OA\Post(
        path: '/api/login',
        tags: ['Autenticação'],
        summary: 'Realiza login do usuário',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'rafael@email.com'),
                    new OA\Property(property: 'password', type: 'string', example: '123456'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login realizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'access_token', type: 'string', example: 'TOKEN_JWT'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Credenciais inválidas'
            ),
        ]
    )]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    #[OA\Get(
        path: '/api/me',
        tags: ['Autenticação'],
        summary: 'Retorna o usuário autenticado',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário autenticado retornado com sucesso'
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado'
            ),
        ]
    )]
    public function me()
    {
        return response()->json(auth('api')->user());
    }

     #[OA\Post(
        path: '/api/logout',
        tags: ['Autenticação'],
        summary: 'Realiza logout do usuário',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout realizado com sucesso'
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado'
            ),
        ]
    )]
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }
}
