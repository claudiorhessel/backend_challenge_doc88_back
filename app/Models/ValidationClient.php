<?php

namespace App\Models;

class ValidationClient
{
    const RULE_CLIENT = [
        'name' => 'required|max:250',
        'email' => 'required|email|unique:clients|max:100',
        'phone' => 'required|max:11|integer',
        'birth_date' => 'required|date_format:"Y-m-d"',
        'address' => 'required|max:250',
        'complement' => 'max:250',
        'neighborhood' => 'required|max:50',
        'cep' => 'required|max:8|integer'
    ];

    const MESSAGE_CLIENT = [
        'name.required' => 'O "NOME" é obrigatório'
    ];
}
