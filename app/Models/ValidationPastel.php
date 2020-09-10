<?php

namespace App\Models;

class ValidationPastel
{
    const RULE_PASTEL = [
        'name' => 'required|max:250',
        'price' => 'required|max:100',
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ];

    const RULE_PASTEL_UPDATE = [
        'name' => 'required|max:250',
        'price' => 'required|max:100',
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ];
}
