<?php

namespace App\Models;

class ValidationType
{
    const RULE_TYPE = [
        'name' => 'required|unique:types|max:250'
    ];

    const RULE_TYPE_UPDATE = [
        'id' => 'required|integer|exists:types,id',
        'name' => 'required|unique:types|max:250'
    ];

    const RULE_TYPE_ID = [
        'id' => 'required|integer'
    ];

    const RULE_TYPE_PAGINATION = [
        'id' => 'integer',
        'name' => 'min:3',
        'page' => 'integer',
        'per_page' => 'integer',
        'order' => 'in:asc,desc',
        'show_deleted' => 'in:0,1'
    ];

    const MESSAGE_TYPE = [
        'id.required' => 'O \'id\' é obrigatório.',
        'id.integer' => 'O \'id\' deve conter apenas números.',
        'id.exists' => 'O \'id\' informado não existe ou não está ativo.',
        'name.required' => 'O \'name\' é obrigatório.',
        'name.min' => 'Você deve informar ao menos 3 caracteres para o \'name\'.',
        'page.integer' => 'O \'cep\' deve conter apenas números.',
        'per_page.integer' => 'O \'cep\' deve conter apenas números.',
        'order.in' => 'O \'order\' deve conter \'asc\' ou \'desc\'.',
        'show_deleted.in' => 'Somente valores \'0\' e \'1\' são permitidos para \'show_deleted\''
    ];
}
