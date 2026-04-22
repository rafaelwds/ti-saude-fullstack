<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdatePacienteRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'            => 'required|string|max:255',
            'data_nascimento' => 'required|date|before_or_equal:today',
            'telefones'       => 'required|array|min:1',
            'telefones.*'     => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'            => 'O nome é obrigatório.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.date'     => 'Informe uma data válida.',
            'data_nascimento.before_or_equal' => 'A data de nascimento não pode ser no futuro.',
            'telefones.required'       => 'Informe ao menos um telefone.',
            'telefones.min'            => 'Informe ao menos um telefone.',
            'telefones.*.required'     => 'O telefone não pode ser vazio.',
        ];
    }
}
