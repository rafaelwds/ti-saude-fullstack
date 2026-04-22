<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultaRequest;
use App\Http\Requests\UpdateConsultaRequest;
use App\Models\Consulta;
use OpenApi\Attributes as OA;

class ConsultaController extends Controller
{
    private function withRelations()
    {
        return ['paciente.telefones', 'medico.especialidade', 'planoSaude', 'procedimentos'];
    }

    #[OA\Get(
        path: '/api/consultas',
        tags: ['Consultas'],
        summary: 'Lista todas as consultas',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Consultas listadas com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index()
    {
        $consultas = Consulta::with($this->withRelations())
            ->orderBy('data', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        return response()->json([
            'message' => $consultas->isEmpty() ? 'Nenhuma consulta cadastrada' : 'Consultas listadas com sucesso',
            'data'    => $consultas,
        ]);
    }

    #[OA\Post(
        path: '/api/consultas',
        tags: ['Consultas'],
        summary: 'Cria uma nova consulta',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['data', 'hora', 'paciente_id', 'medico_id'],
                properties: [
                    new OA\Property(property: 'cons_codigo', type: 'string', example: 'CONS001'),
                    new OA\Property(property: 'data', type: 'string', format: 'date', example: '2026-04-22'),
                    new OA\Property(property: 'hora', type: 'string', example: '14:30'),
                    new OA\Property(property: 'particular', type: 'boolean', example: false),
                    new OA\Property(property: 'paciente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'medico_id', type: 'integer', example: 1),
                    new OA\Property(property: 'plano_saude_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'procedimentos', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Consulta criada com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StoreConsultaRequest $request)
    {
        $validated     = $request->validated();
        $procedimentos = $validated['procedimentos'] ?? [];
        unset($validated['procedimentos']);

        $consulta = Consulta::create($validated);

        if (!empty($procedimentos)) {
            $consulta->procedimentos()->sync($procedimentos);
        }

        $consulta->load($this->withRelations());

        return response()->json([
            'message' => 'Consulta criada com sucesso',
            'data'    => $consulta,
        ], 201);
    }

    #[OA\Get(
        path: '/api/consultas/{id}',
        tags: ['Consultas'],
        summary: 'Busca uma consulta por ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Consulta encontrada com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Consulta não encontrada'),
        ]
    )]
    public function show(string $id)
    {
        $consulta = Consulta::with($this->withRelations())->findOrFail($id);

        return response()->json([
            'message' => 'Consulta encontrada com sucesso',
            'data'    => $consulta,
        ]);
    }

    #[OA\Put(
        path: '/api/consultas/{id}',
        tags: ['Consultas'],
        summary: 'Atualiza uma consulta',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['data', 'hora', 'paciente_id', 'medico_id'],
                properties: [
                    new OA\Property(property: 'cons_codigo', type: 'string', example: 'CONS001'),
                    new OA\Property(property: 'data', type: 'string', format: 'date', example: '2026-04-22'),
                    new OA\Property(property: 'hora', type: 'string', example: '14:30'),
                    new OA\Property(property: 'particular', type: 'boolean', example: false),
                    new OA\Property(property: 'paciente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'medico_id', type: 'integer', example: 1),
                    new OA\Property(property: 'plano_saude_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'procedimentos', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Consulta atualizada com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Consulta não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdateConsultaRequest $request, string $id)
    {
        $consulta      = Consulta::findOrFail($id);
        $validated     = $request->validated();
        $procedimentos = $validated['procedimentos'] ?? null;
        unset($validated['procedimentos']);

        $consulta->update($validated);

        if ($procedimentos !== null) {
            $consulta->procedimentos()->sync($procedimentos);
        }

        $consulta->load($this->withRelations());

        return response()->json([
            'message' => 'Consulta atualizada com sucesso',
            'data'    => $consulta,
        ]);
    }

    #[OA\Delete(
        path: '/api/consultas/{id}',
        tags: ['Consultas'],
        summary: 'Remove uma consulta',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Consulta removida com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Consulta não encontrada'),
        ]
    )]
    public function destroy(string $id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->procedimentos()->detach();
        $consulta->delete();

        return response()->json([
            'message' => 'Consulta removida com sucesso',
        ]);
    }
}
