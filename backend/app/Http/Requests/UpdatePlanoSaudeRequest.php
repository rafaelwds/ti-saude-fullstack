<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdatePlanoSaudeRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('plano_saude');

        return [
            'plano_codigo'    => "nullable|string|max:20|unique:planos_saude,plano_codigo,{$id}",
            'plano_descricao' => 'required|string|max:255',
            'plano_telefone'  => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'plano_descricao.required' => 'A descrição do plano é obrigatória.',
            'plano_codigo.unique'      => 'Já existe um plano com este código.',
        ];
    }
}
