<?php

namespace App\Models;

class ValidationOrder
{
    const RULE_ORDER = [
        'client_id' => 'required|exists:clients,id|integer',
        'product.*.id' => 'required|exists:products,id|integer',
        'product.*.qtd' => 'required|integer'
    ];

    const RULE_ORDER_ID = [
        'id' => 'required|integer'
    ];

    const RULE_ORDER_PAGINATION = [
        'id' => 'integer',
        'product_id' => 'integer',
        'product_name' => 'min:3',
        'product_type' => 'min:3',
        'client_id' => 'integer',
        'client_name' => 'min:3',
        'client_email' => 'min:3',
        'page' => 'integer',
        'per_page' => 'integer',
        'order' => 'in:asc,desc',
        'show_deleted' => 'in:0,1'
    ];

    const MESSAGE_ORDER = [
        'id.required' => 'O \'id\' é obrigatório.',
        'id.integer' => 'O \'id\' deve conter apenas números.',
        'product_id.integer' => 'O \'product_id\' deve ser um número.',
        'product_name.min' => 'O \'product_name\' deve conter ao menos 3 caracteres.',
        'product_type.min' => 'O \'product_type\' deve conter ao menos 3 caracteres.',

        'client_id.integer' => 'O \'client_id\' deve ser um número.',
        'client_name.min' => 'O \'client_name\' deve conter ao menos 3 caracteres.',
        'client_email.min' => 'O \'client_email\' deve conter ao menos 3 caracteres.',

        'page.integer' => 'O \'cep\' deve conter apenas números.',
        'per_page.integer' => 'O \'cep\' deve conter apenas números.',
        'order.in' => 'O \'order\' deve conter \'asc\' ou \'desc\'.',
        'show_deleted.in' => 'Somente valores \'0\' e \'1\' são permitidos para \'show_deleted\''
    ];
}
