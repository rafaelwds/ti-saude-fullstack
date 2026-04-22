<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicoRequest;
use App\Http\Requests\UpdateMedicoRequest;
use App\Models\Medico;
use OpenApi\Attributes as OA;

class MedicoController extends Controller
{
    #[OA\Get(
        path: '/api/medicos',
        tags: ['Médicos'],
        summary: 'Lista todos os médicos',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Médicos listados com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index()
    {
        $medicos = Medico::with('especialidade')->orderBy('med_nome')->get();

        return response()->json([
            'message' => $medicos->isEmpty() ? 'Nenhum médico cadastrado' : 'Médicos listados com sucesso',
            'data'    => $medicos,
        ]);
    }

    #[OA\Post(
        path: '/api/medicos',
        tags: ['Médicos'],
        summary: 'Cria um novo médico',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['med_nome', 'med_crm', 'especialidade_id'],
                properties: [
                    new OA\Property(property: 'med_codigo', type: 'string', example: 'MED001'),
                    new OA\Property(property: 'med_nome', type: 'string', example: 'Dr. Carlos Souza'),
                    new OA\Property(property: 'med_crm', type: 'string', example: 'CRM-PE 12345'),
                    new OA\Property(property: 'especialidade_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Médico criado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StoreMedicoRequest $request)
    {
        $medico = Medico::create($request->validated());
        $medico->load('especialidade');

        return response()->json([
            'message' => 'Médico criado com sucesso',
            'data'    => $medico,
        ], 201);
    }

    #[OA\Get(
        path: '/api/medicos/{id}',
        tags: ['Médicos'],
        summary: 'Busca um médico por ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Médico encontrado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Médico não encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $medico = Medico::with('especialidade')->findOrFail($id);

        return response()->json([
            'message' => 'Médico encontrado com sucesso',
            'data'    => $medico,
        ]);
    }

    #[OA\Put(
        path: '/api/medicos/{id}',
        tags: ['Médicos'],
        summary: 'Atualiza um médico',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['med_nome', 'med_crm', 'especialidade_id'],
                properties: [
                    new OA\Property(property: 'med_codigo', type: 'string', example: 'MED001'),
                    new OA\Property(property: 'med_nome', type: 'string', example: 'Dr. Carlos Souza'),
                    new OA\Property(property: 'med_crm', type: 'string', example: 'CRM-PE 12345'),
                    new OA\Property(property: 'especialidade_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Médico atualizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Médico não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdateMedicoRequest $request, string $id)
    {
        $medico = Medico::findOrFail($id);
        $medico->update($request->validated());
        $medico->load('especialidade');

        return response()->json([
            'message' => 'Médico atualizado com sucesso',
            'data'    => $medico,
        ]);
    }

    #[OA\Delete(
        path: '/api/medicos/{id}',
        tags: ['Médicos'],
        summary: 'Remove um médico',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Médico removido com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Médico não encontrado'),
            new OA\Response(response: 409, description: 'Médico possui consultas vinculadas'),
        ]
    )]
    public function destroy(string $id)
    {
        $medico = Medico::findOrFail($id);

        if ($medico->consultas()->exists()) {
            return response()->json([
                'message' => 'Não é possível remover: existem consultas vinculadas a este médico.',
            ], 409);
        }

        $medico->delete();

        return response()->json([
            'message' => 'Médico removido com sucesso',
        ]);
    }
}
