<?php

namespace App\Models;

class ValidationOrder
{
    const RULE_ORDER = [
        'client_id' => 'required',
        'product_id' => 'required'
    ];
}
