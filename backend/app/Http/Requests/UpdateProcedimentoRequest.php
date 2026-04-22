<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiFormRequest;

class UpdateProcedimentoRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('procedimento');

        return [
            'proc_codigo' => "nullable|string|max:20|unique:procedimentos,proc_codigo,{$id}",
            'proc_nome'   => 'required|string|max:255',
            'proc_valor'  => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'proc_nome.required'  => 'O nome do procedimento é obrigatório.',
            'proc_valor.required' => 'O valor do procedimento é obrigatório.',
            'proc_valor.numeric'  => 'O valor deve ser numérico.',
            'proc_valor.min'      => 'O valor não pode ser negativo.',
            'proc_codigo.unique'  => 'Já existe um procedimento com este código.',
        ];
    }
}
