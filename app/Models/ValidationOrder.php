<?php

namespace App\Models;

class ValidationOrder
{
    const RULE_ORDER_GET = [
        'id' => 'required|integer'
    ];

    const MESSAGE_ORDER_GET = [
        'id.required' => 'O \'ID\' é obrigatório.',
        'id.integer' => 'O \'ID\' deve ser um número.'
    ];

    const RULE_ORDER = [
        'client_id' => 'required|exists:clients,id|integer',
        'product.*.id' => 'required|exists:products,id|integer',
        'product.*.qtd' => 'required|integer'
    ];
}
