<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanoSaudeRequest;
use App\Http\Requests\UpdatePlanoSaudeRequest;
use App\Models\PlanoSaude;
use OpenApi\Attributes as OA;

class PlanoSaudeController extends Controller
{
    #[OA\Get(
        path: '/api/planos-saude',
        tags: ['Planos de Saúde'],
        summary: 'Lista todos os planos de saúde',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Planos listados com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index()
    {
        $planos = PlanoSaude::orderBy('plano_descricao')->get();

        return response()->json([
            'message' => $planos->isEmpty() ? 'Nenhum plano de saúde cadastrado' : 'Planos de saúde listados com sucesso',
            'data'    => $planos,
        ]);
    }

    #[OA\Post(
        path: '/api/planos-saude',
        tags: ['Planos de Saúde'],
        summary: 'Cria um novo plano de saúde',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['plano_descricao'],
                properties: [
                    new OA\Property(property: 'plano_codigo', type: 'string', example: 'UNIMED'),
                    new OA\Property(property: 'plano_descricao', type: 'string', example: 'Unimed Nacional'),
                    new OA\Property(property: 'plano_telefone', type: 'string', example: '08000001234'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Plano criado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StorePlanoSaudeRequest $request)
    {
        $plano = PlanoSaude::create($request->validated());

        return response()->json([
            'message' => 'Plano de saúde criado com sucesso',
            'data'    => $plano,
        ], 201);
    }

    #[OA\Get(
        path: '/api/planos-saude/{id}',
        tags: ['Planos de Saúde'],
        summary: 'Busca um plano de saúde por ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Plano encontrado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Plano não encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $plano = PlanoSaude::findOrFail($id);

        return response()->json([
            'message' => 'Plano de saúde encontrado com sucesso',
            'data'    => $plano,
        ]);
    }

    #[OA\Put(
        path: '/api/planos-saude/{id}',
        tags: ['Planos de Saúde'],
        summary: 'Atualiza um plano de saúde',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['plano_descricao'],
                properties: [
                    new OA\Property(property: 'plano_codigo', type: 'string', example: 'UNIMED'),
                    new OA\Property(property: 'plano_descricao', type: 'string', example: 'Unimed Nacional'),
                    new OA\Property(property: 'plano_telefone', type: 'string', example: '08000001234'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Plano atualizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Plano não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdatePlanoSaudeRequest $request, string $id)
    {
        $plano = PlanoSaude::findOrFail($id);
        $plano->update($request->validated());

        return response()->json([
            'message' => 'Plano de saúde atualizado com sucesso',
            'data'    => $plano,
        ]);
    }

    #[OA\Delete(
        path: '/api/planos-saude/{id}',
        tags: ['Planos de Saúde'],
        summary: 'Remove um plano de saúde',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Plano removido com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Plano não encontrado'),
            new OA\Response(response: 409, description: 'Plano possui vínculos com pacientes'),
        ]
    )]
    public function destroy(string $id)
    {
        $plano = PlanoSaude::findOrFail($id);

        if ($plano->vinculos()->exists()) {
            return response()->json([
                'message' => 'Não é possível remover: existem pacientes vinculados a este plano.',
            ], 409);
        }

        $plano->delete();

        return response()->json([
            'message' => 'Plano de saúde removido com sucesso',
        ]);
    }
}
