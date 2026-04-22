<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateConsultaRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('consulta');

        return [
            'cons_codigo'     => "nullable|string|max:20|unique:consultas,cons_codigo,{$id}",
            'data'            => 'required|date',
            'hora'            => 'required|date_format:H:i',
            'particular'      => 'boolean',
            'paciente_id'     => 'required|integer|exists:pacientes,id',
            'medico_id'       => 'required|integer|exists:medicos,id',
            'plano_saude_id'  => 'nullable|integer|exists:planos_saude,id',
            'procedimentos'   => 'nullable|array',
            'procedimentos.*' => 'integer|exists:procedimentos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'data.required'          => 'A data da consulta é obrigatória.',
            'data.date'              => 'Informe uma data válida.',
            'hora.required'          => 'A hora da consulta é obrigatória.',
            'hora.date_format'       => 'Informe a hora no formato HH:MM.',
            'paciente_id.required'   => 'O paciente é obrigatório.',
            'paciente_id.exists'     => 'O paciente informado não existe.',
            'medico_id.required'     => 'O médico é obrigatório.',
            'medico_id.exists'       => 'O médico informado não existe.',
            'plano_saude_id.exists'  => 'O plano de saúde informado não existe.',
            'procedimentos.*.exists' => 'Um ou mais procedimentos informados não existem.',
        ];
    }
}
