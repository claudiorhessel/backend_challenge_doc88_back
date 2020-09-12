<?php

namespace App\Models;

class ValidationProduct
{
    const RULE_ORDER_PRODUCT = [
        'order_id' => 'required',
        'product_id' => 'required'
    ];

    const RULE_ORDER_PRODUCT_UPDATE = [
        'order_id' => 'required',
        'product_id' => 'required'
    ];
}
