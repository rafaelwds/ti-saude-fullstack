<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreEspecialidadeRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'espec_codigo' => 'nullable|string|max:20|unique:especialidades,espec_codigo',
            'espec_nome'   => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'espec_nome.required'      => 'O nome da especialidade é obrigatório.',
            'espec_codigo.unique'      => 'Já existe uma especialidade com este código.',
        ];
    }
}
