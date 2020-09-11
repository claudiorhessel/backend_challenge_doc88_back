<?php

namespace App\Models;

class ValidationType
{
    const RULE_TYPE = [
        'name' => 'required|unique:types|max:250'
    ];

    const RULE_TYPE_UPDATE = [
        'name' => 'required|unique:types|max:250'
    ];
}
