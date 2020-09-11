<?php

namespace App\Models;

class ValidationProduct
{
    const RULE_PRODUCT = [
        'name' => 'required|max:250',
        'price' => 'required|max:100',
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ];

    const RULE_PRODUCT_UPDATE = [
        'name' => 'required|max:250',
        'price' => 'required|max:100',
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ];
}
