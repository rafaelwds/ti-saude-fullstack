<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class StoreVinculoRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nr_contrato'   => 'required|string|max:50',
            'paciente_id'   => 'required|integer|exists:pacientes,id',
            'plano_saude_id' => [
                'required',
                'integer',
                'exists:planos_saude,id',
                "unique:vinculos,plano_saude_id,NULL,id,paciente_id,{$this->input('paciente_id')}",
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nr_contrato.required'    => 'O número do contrato é obrigatório.',
            'paciente_id.required'    => 'O paciente é obrigatório.',
            'paciente_id.exists'      => 'O paciente informado não existe.',
            'plano_saude_id.required' => 'O plano de saúde é obrigatório.',
            'plano_saude_id.exists'   => 'O plano de saúde informado não existe.',
            'plano_saude_id.unique'   => 'Este paciente já está vinculado a este plano de saúde.',
        ];
    }
}
