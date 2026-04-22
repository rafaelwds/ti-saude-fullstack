<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreMedicoRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'med_codigo'      => 'nullable|string|max:20|unique:medicos,med_codigo',
            'med_nome'        => 'required|string|max:255',
            'med_crm'         => 'required|string|max:20|unique:medicos,med_crm',
            'especialidade_id' => 'required|integer|exists:especialidades,id',
        ];
    }

    public function messages(): array
    {
        return [
            'med_nome.required'          => 'O nome do médico é obrigatório.',
            'med_crm.required'           => 'O CRM é obrigatório.',
            'med_crm.unique'             => 'Já existe um médico com este CRM.',
            'med_codigo.unique'          => 'Já existe um médico com este código.',
            'especialidade_id.required'  => 'A especialidade é obrigatória.',
            'especialidade_id.exists'    => 'A especialidade informada não existe.',
        ];
    }
}
