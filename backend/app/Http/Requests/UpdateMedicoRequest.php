<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateMedicoRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('medico');

        return [
            'med_codigo'       => "nullable|string|max:20|unique:medicos,med_codigo,{$id}",
            'med_nome'         => 'required|string|max:255',
            'med_crm'          => "required|string|max:20|unique:medicos,med_crm,{$id}",
            'especialidade_id' => 'required|integer|exists:especialidades,id',
        ];
    }

    public function messages(): array
    {
        return [
            'med_nome.required'         => 'O nome do médico é obrigatório.',
            'med_crm.required'          => 'O CRM é obrigatório.',
            'med_crm.unique'            => 'Já existe um médico com este CRM.',
            'med_codigo.unique'         => 'Já existe um médico com este código.',
            'especialidade_id.required' => 'A especialidade é obrigatória.',
            'especialidade_id.exists'   => 'A especialidade informada não existe.',
        ];
    }
}
