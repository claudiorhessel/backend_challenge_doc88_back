<?php

namespace App\Models;

class ValidationClient
{
    const RULE_CLIENT_ID = [
        'id' => 'required|integer'
    ];

    const RULE_CLIENT_UPDATE = [
        'id' => 'required|integer',
        'name' => 'required|max:250',
        'email' => 'required|email|unique:clients|max:100',
        'phone' => 'required|max:11|integer',
        'birth_date' => 'required|date_format:"Y-m-d"',
        'address' => 'required|max:250',
        'complement' => 'max:250',
        'neighborhood' => 'required|max:50',
        'cep' => 'required|max:8|integer'
    ];

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

    const RULE_CLIENT_PAGINATION = [
        'name' => 'min:3',
        'email' => 'min:3',
        'phone' => 'integer',
        'birth_date' => 'date_format:"Y-m-d"',
        'address' => 'min:3',
        'complement' => 'min:3',
        'neighborhood' => 'min:3',
        'cep' => 'integer',
        'page' => 'integer',
        'per_page' => 'integer',
        'order' => 'in:asc,desc',
        'birth_date_start' =>'date_format:"Y-m-d"|before:"birth_date_end"',
        'birth_date_end' =>'date_format:"Y-m-d"|after:"birth_date_start"',
        'show_deleted' => 'in:0,1'
    ];

    const MESSAGE_CLIENT = [
        'id.required' => 'O \'id\' é obrigatório.',
        'id.integer' => 'O \'id\' deve conter apenas números.',
        'name.required' => 'O \'name\' é obrigatório.',
        'name.min' => 'Você deve informar ao menos 3 caracteres para o \'name\'.',
        'email.required' => 'O \'email\' é obrigatório.',
        'email.email' => 'O \'email\' não um e-mail válido.',
        'email.email' => 'Já existe um cliente cadastrado com o \'email\' informado.',
        'phone.required' => 'O \'phone\' é obrigatório.',
        'phone.integer' => 'O \'phone\' deve conter apenas números.',
        'birth_date.required' => 'O \'birth_date\' é obrigatório.',
        'address.required' => 'O \'address\' é obrigatório.',
        'complement.required' => 'O \'complement\' é obrigatório.',
        'neighborhood.required' => 'O \'neighborhood\' é obrigatório.',
        'cep.required' => 'O \'cep\' é obrigatório.',
        'cep.integer' => 'O \'cep\' deve conter apenas números.',
        'page.integer' => 'O \'cep\' deve conter apenas números.',
        'per_page.integer' => 'O \'cep\' deve conter apenas números.',
        'order.in' => 'O \'order\' deve conter \'asc\' ou \'desc\'.',
        'birth_date_start.date_format' => 'O formato da data não é válido o valor aceito é \'Y-m-d\'.',
        'birth_date_start.before' => 'A data inicial não pode ser maior que a data final.',
        'birth_date_end.date_format' => 'O formato da data não é válido o valor aceito é \'Y-m-d\'.',
        'birth_date_end.after' => 'A data final não pode ser menos que a data de início.',
        'show_deleted.in' => 'Somente valores \'0\' e \'1\' são permitidos para \'show_deleted\''
    ];
}
