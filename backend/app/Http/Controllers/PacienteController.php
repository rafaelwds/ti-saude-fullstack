<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PacienteController extends Controller
{
    #[OA\Get(
        path: '/api/pacientes',
        tags: ['Pacientes'],
        summary: 'Lista todos os pacientes',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Pacientes listados com sucesso'
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado'
            )
        ]
    )]
    public function index()
    {
        $pacientes = Paciente::latest()->get();

        return response()->json([
            'data' => $pacientes,
            'message' => $pacientes->isEmpty()
                ? 'Nenhum paciente cadastrado'
                : 'Pacientes listados com sucesso'
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
                required: ['nome', 'data_nascimento', 'telefone'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Maria Silva'),
                    new OA\Property(property: 'data_nascimento', type: 'string', format: 'date', example: '1995-08-10'),
                    new OA\Property(property: 'telefone', type: 'string', example: '81999999999'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Paciente criado com sucesso'
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado'
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação'
            )
        ]
    )]
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'telefone' => 'required|string|max:20',
        ]);

        $paciente = Paciente::create($data);

        return response()->json($paciente, 201);
    }

    #[OA\Get(
        path: '/api/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Busca um paciente por ID',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID do paciente',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paciente encontrado com sucesso'
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado'
            ),
            new OA\Response(
                response: 404,
                description: 'Paciente não encontrado'
            )
        ]
    )]
    public function show(string $id)
    {
        $paciente = Paciente::findOrFail($id);

        return response()->json($paciente);
    }

    #[OA\Put(
        path: '/api/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Atualiza um paciente',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID do paciente',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome', 'data_nascimento', 'telefone'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Maria Silva Santos'),
                    new OA\Property(property: 'data_nascimento', type: 'string', format: 'date', example: '1995-08-10'),
                    new OA\Property(property: 'telefone', type: 'string', example: '81888888888'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paciente atualizado com sucesso'
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado'
            ),
            new OA\Response(
                response: 404,
                description: 'Paciente não encontrado'
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação'
            )
        ]
    )]
    public function update(Request $request, string $id)
    {
        $paciente = Paciente::findOrFail($id);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'telefone' => 'required|string|max:20',
        ]);

        $paciente->update($data);

        return response()->json($paciente);
    }

    #[OA\Delete(
        path: '/api/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Remove um paciente',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID do paciente',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paciente removido com sucesso'
            ),
            new OA\Response(
                response: 401,
                description: 'Não autenticado'
            ),
            new OA\Response(
                response: 404,
                description: 'Paciente não encontrado'
            )
        ]
    )]
    public function destroy(string $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->delete();

        return response()->json([
            'message' => 'Paciente removido com sucesso'
        ]);
    }
}
