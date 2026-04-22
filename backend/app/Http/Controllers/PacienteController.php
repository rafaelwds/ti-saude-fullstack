<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Models\Paciente;
use OpenApi\Attributes as OA;

class PacienteController extends Controller
{
    #[OA\Get(
        path: '/api/pacientes',
        tags: ['Pacientes'],
        summary: 'Lista todos os pacientes',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Pacientes listados com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index()
    {
        $pacientes = Paciente::with('telefones')->latest()->get();

        return response()->json([
            'message' => $pacientes->isEmpty() ? 'Nenhum paciente cadastrado' : 'Pacientes listados com sucesso',
            'data'    => $pacientes,
        ]);
    }

    #[OA\Post(
        path: '/api/pacientes',
        tags: ['Pacientes'],
        summary: 'Cria um novo paciente',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome', 'data_nascimento', 'telefones'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Maria Silva'),
                    new OA\Property(property: 'data_nascimento', type: 'string', format: 'date', example: '1995-08-10'),
                    new OA\Property(
                        property: 'telefones',
                        type: 'array',
                        items: new OA\Items(type: 'string', example: '81999990001')
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Paciente criado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StorePacienteRequest $request)
    {
        $validated = $request->validated();

        $paciente = Paciente::create([
            'nome'            => $validated['nome'],
            'data_nascimento' => $validated['data_nascimento'],
        ]);

        $paciente->telefones()->createMany(
            collect($validated['telefones'])->map(fn($t) => ['telefone' => $t])->toArray()
        );

        $paciente->load('telefones');

        return response()->json([
            'message' => 'Paciente criado com sucesso',
            'data'    => $paciente,
        ], 201);
    }

    #[OA\Get(
        path: '/api/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Busca um paciente por ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paciente encontrado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Paciente não encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $paciente = Paciente::with('telefones')->findOrFail($id);

        return response()->json([
            'message' => 'Paciente encontrado com sucesso',
            'data'    => $paciente,
        ]);
    }

    #[OA\Put(
        path: '/api/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Atualiza um paciente',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome', 'data_nascimento', 'telefones'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Maria Silva Santos'),
                    new OA\Property(property: 'data_nascimento', type: 'string', format: 'date', example: '1995-08-10'),
                    new OA\Property(
                        property: 'telefones',
                        type: 'array',
                        items: new OA\Items(type: 'string', example: '81999990001')
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Paciente atualizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Paciente não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdatePacienteRequest $request, string $id)
    {
        $paciente  = Paciente::findOrFail($id);
        $validated = $request->validated();

        $paciente->update([
            'nome'            => $validated['nome'],
            'data_nascimento' => $validated['data_nascimento'],
        ]);

        // Substitui todos os telefones pelo conjunto enviado
        $paciente->telefones()->delete();
        $paciente->telefones()->createMany(
            collect($validated['telefones'])->map(fn($t) => ['telefone' => $t])->toArray()
        );

        $paciente->load('telefones');

        return response()->json([
            'message' => 'Paciente atualizado com sucesso',
            'data'    => $paciente,
        ]);
    }

    #[OA\Delete(
        path: '/api/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Remove um paciente',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paciente removido com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Paciente não encontrado'),
        ]
    )]
    public function destroy(string $id)
    {
        $paciente = Paciente::findOrFail($id);
        // telefones são removidos em cascata via migration
        $paciente->delete();

        return response()->json([
            'message' => 'Paciente removido com sucesso',
        ]);
    }
}
