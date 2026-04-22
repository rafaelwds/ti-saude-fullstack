<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEspecialidadeRequest;
use App\Http\Requests\UpdateEspecialidadeRequest;
use App\Models\Especialidade;
use OpenApi\Attributes as OA;

class EspecialidadeController extends Controller
{
    #[OA\Get(
        path: '/api/especialidades',
        tags: ['Especialidades'],
        summary: 'Lista todas as especialidades',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Especialidades listadas com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index()
    {
        $especialidades = Especialidade::orderBy('espec_nome')->get();

        return response()->json([
            'message' => $especialidades->isEmpty() ? 'Nenhuma especialidade cadastrada' : 'Especialidades listadas com sucesso',
            'data'    => $especialidades,
        ]);
    }

    #[OA\Post(
        path: '/api/especialidades',
        tags: ['Especialidades'],
        summary: 'Cria uma nova especialidade',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['espec_nome'],
                properties: [
                    new OA\Property(property: 'espec_codigo', type: 'string', example: 'CARD'),
                    new OA\Property(property: 'espec_nome', type: 'string', example: 'Cardiologia'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Especialidade criada com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StoreEspecialidadeRequest $request)
    {
        $especialidade = Especialidade::create($request->validated());

        return response()->json([
            'message' => 'Especialidade criada com sucesso',
            'data'    => $especialidade,
        ], 201);
    }

    #[OA\Get(
        path: '/api/especialidades/{id}',
        tags: ['Especialidades'],
        summary: 'Busca uma especialidade por ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Especialidade encontrada com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Especialidade não encontrada'),
        ]
    )]
    public function show(string $id)
    {
        $especialidade = Especialidade::with('medicos')->findOrFail($id);

        return response()->json([
            'message' => 'Especialidade encontrada com sucesso',
            'data'    => $especialidade,
        ]);
    }

    #[OA\Put(
        path: '/api/especialidades/{id}',
        tags: ['Especialidades'],
        summary: 'Atualiza uma especialidade',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['espec_nome'],
                properties: [
                    new OA\Property(property: 'espec_codigo', type: 'string', example: 'CARD'),
                    new OA\Property(property: 'espec_nome', type: 'string', example: 'Cardiologia Intervencionista'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Especialidade atualizada com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Especialidade não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdateEspecialidadeRequest $request, string $id)
    {
        $especialidade = Especialidade::findOrFail($id);
        $especialidade->update($request->validated());

        return response()->json([
            'message' => 'Especialidade atualizada com sucesso',
            'data'    => $especialidade,
        ]);
    }

    #[OA\Delete(
        path: '/api/especialidades/{id}',
        tags: ['Especialidades'],
        summary: 'Remove uma especialidade',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Especialidade removida com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Especialidade não encontrada'),
            new OA\Response(response: 409, description: 'Especialidade possui médicos vinculados'),
        ]
    )]
    public function destroy(string $id)
    {
        $especialidade = Especialidade::findOrFail($id);

        if ($especialidade->medicos()->exists()) {
            return response()->json([
                'message' => 'Não é possível remover: existem médicos vinculados a esta especialidade.',
            ], 409);
        }

        $especialidade->delete();

        return response()->json([
            'message' => 'Especialidade removida com sucesso',
        ]);
    }
}
