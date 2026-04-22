<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcedimentoRequest;
use App\Http\Requests\UpdateProcedimentoRequest;
use App\Models\Procedimento;
use OpenApi\Attributes as OA;

class ProcedimentoController extends Controller
{
    #[OA\Get(
        path: '/api/procedimentos',
        tags: ['Procedimentos'],
        summary: 'Lista todos os procedimentos',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Procedimentos listados com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index()
    {
        $procedimentos = Procedimento::orderBy('proc_nome')->get();

        return response()->json([
            'message' => $procedimentos->isEmpty() ? 'Nenhum procedimento cadastrado' : 'Procedimentos listados com sucesso',
            'data'    => $procedimentos,
        ]);
    }

    #[OA\Post(
        path: '/api/procedimentos',
        tags: ['Procedimentos'],
        summary: 'Cria um novo procedimento',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['proc_nome', 'proc_valor'],
                properties: [
                    new OA\Property(property: 'proc_codigo', type: 'string', example: 'PROC001'),
                    new OA\Property(property: 'proc_nome', type: 'string', example: 'Eletrocardiograma'),
                    new OA\Property(property: 'proc_valor', type: 'number', format: 'float', example: 150.00),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Procedimento criado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StoreProcedimentoRequest $request)
    {
        $procedimento = Procedimento::create($request->validated());

        return response()->json([
            'message' => 'Procedimento criado com sucesso',
            'data'    => $procedimento,
        ], 201);
    }

    #[OA\Get(
        path: '/api/procedimentos/{id}',
        tags: ['Procedimentos'],
        summary: 'Busca um procedimento por ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Procedimento encontrado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Procedimento não encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $procedimento = Procedimento::findOrFail($id);

        return response()->json([
            'message' => 'Procedimento encontrado com sucesso',
            'data'    => $procedimento,
        ]);
    }

    #[OA\Put(
        path: '/api/procedimentos/{id}',
        tags: ['Procedimentos'],
        summary: 'Atualiza um procedimento',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['proc_nome', 'proc_valor'],
                properties: [
                    new OA\Property(property: 'proc_codigo', type: 'string', example: 'PROC001'),
                    new OA\Property(property: 'proc_nome', type: 'string', example: 'Eletrocardiograma'),
                    new OA\Property(property: 'proc_valor', type: 'number', format: 'float', example: 150.00),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Procedimento atualizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Procedimento não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdateProcedimentoRequest $request, string $id)
    {
        $procedimento = Procedimento::findOrFail($id);
        $procedimento->update($request->validated());

        return response()->json([
            'message' => 'Procedimento atualizado com sucesso',
            'data'    => $procedimento,
        ]);
    }

    #[OA\Delete(
        path: '/api/procedimentos/{id}',
        tags: ['Procedimentos'],
        summary: 'Remove um procedimento',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Procedimento removido com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Procedimento não encontrado'),
            new OA\Response(response: 409, description: 'Procedimento vinculado a consultas'),
        ]
    )]
    public function destroy(string $id)
    {
        $procedimento = Procedimento::findOrFail($id);

        if ($procedimento->consultas()->exists()) {
            return response()->json([
                'message' => 'Não é possível remover: este procedimento está vinculado a consultas.',
            ], 409);
        }

        $procedimento->delete();

        return response()->json([
            'message' => 'Procedimento removido com sucesso',
        ]);
    }
}
