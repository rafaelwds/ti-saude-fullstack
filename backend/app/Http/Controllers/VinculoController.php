<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVinculoRequest;
use App\Http\Requests\UpdateVinculoRequest;
use App\Models\Vinculo;
use OpenApi\Attributes as OA;

class VinculoController extends Controller
{
    #[OA\Get(
        path: '/api/vinculos',
        tags: ['Vínculos'],
        summary: 'Lista todos os vínculos paciente-plano',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Vínculos listados com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index()
    {
        $vinculos = Vinculo::with(['paciente', 'planoSaude'])->latest()->get();

        return response()->json([
            'message' => $vinculos->isEmpty() ? 'Nenhum vínculo cadastrado' : 'Vínculos listados com sucesso',
            'data'    => $vinculos,
        ]);
    }

    #[OA\Post(
        path: '/api/vinculos',
        tags: ['Vínculos'],
        summary: 'Cria um novo vínculo entre paciente e plano de saúde',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nr_contrato', 'paciente_id', 'plano_saude_id'],
                properties: [
                    new OA\Property(property: 'nr_contrato', type: 'string', example: 'CONT-2026-001'),
                    new OA\Property(property: 'paciente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'plano_saude_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Vínculo criado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StoreVinculoRequest $request)
    {
        $vinculo = Vinculo::create($request->validated());
        $vinculo->load(['paciente', 'planoSaude']);

        return response()->json([
            'message' => 'Vínculo criado com sucesso',
            'data'    => $vinculo,
        ], 201);
    }

    #[OA\Get(
        path: '/api/vinculos/{id}',
        tags: ['Vínculos'],
        summary: 'Busca um vínculo por ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Vínculo encontrado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Vínculo não encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $vinculo = Vinculo::with(['paciente', 'planoSaude'])->findOrFail($id);

        return response()->json([
            'message' => 'Vínculo encontrado com sucesso',
            'data'    => $vinculo,
        ]);
    }

    #[OA\Put(
        path: '/api/vinculos/{id}',
        tags: ['Vínculos'],
        summary: 'Atualiza um vínculo',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nr_contrato', 'paciente_id', 'plano_saude_id'],
                properties: [
                    new OA\Property(property: 'nr_contrato', type: 'string', example: 'CONT-2026-002'),
                    new OA\Property(property: 'paciente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'plano_saude_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Vínculo atualizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Vínculo não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdateVinculoRequest $request, string $id)
    {
        $vinculo = Vinculo::findOrFail($id);
        $vinculo->update($request->validated());
        $vinculo->load(['paciente', 'planoSaude']);

        return response()->json([
            'message' => 'Vínculo atualizado com sucesso',
            'data'    => $vinculo,
        ]);
    }

    #[OA\Delete(
        path: '/api/vinculos/{id}',
        tags: ['Vínculos'],
        summary: 'Remove um vínculo',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Vínculo removido com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Vínculo não encontrado'),
        ]
    )]
    public function destroy(string $id)
    {
        $vinculo = Vinculo::findOrFail($id);
        $vinculo->delete();

        return response()->json([
            'message' => 'Vínculo removido com sucesso',
        ]);
    }
}
