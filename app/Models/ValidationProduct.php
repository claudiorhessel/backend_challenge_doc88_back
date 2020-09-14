<?php

namespace App\Models;

class ValidationProduct
{
    const RULE_PRODUCT = [
        'name' => 'required|max:250',
        'price' => 'required|max:100|regex:/^\d+(\.\d{1,2})?$/',
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ];

    const RULE_PRODUCT_ID = [
        'id' => 'required|integer'
    ];

    const RULE_PRODUCT_UPDATE = [
        'id' => 'integer',
        'name' => 'required|max:250',
        'price' => 'required|max:100|regex:/^\d+(\.\d{1,2})?$/',
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ];

    const RULE_PRODUCT_PAGINATION = [
        'id' => 'integer',
        'name' => 'min:3',
        'price' => 'regex:/^\d+(\.\d{1,2})?$/',
        'type_name' => 'min:3',
        'page' => 'integer',
        'per_page' => 'integer',
        'order' => 'in:asc,desc',
        'show_deleted' => 'in:0,1'
    ];

    const MESSAGE_PRODUCT = [
        'id.required' => 'O \'id\' é obrigatório.',
        'id.integer' => 'O \'id\' deve conter apenas números.',
        'name.required' => 'O \'name\' é obrigatório.',
        'name.min' => 'Você deve informar ao menos 3 caracteres para o \'name\'.',
        'price.required' => 'O \'email\' é obrigatório.',
        'price.regex' => 'O formato do \'price\' está incorreto, o padrão é \'0.00\'.',
        'type_name.required' => 'O \'type_name\' é obrigatório.',
        'type_id.required' => 'O \'type_id\' é obrigatório.',
        'photo.required' => 'O \'photo\' é obrigatório.',
        'photo.image' => 'O \'photo\' deve ser uma imagem.',
        'photo.mimes' => 'O \'photo\' deve ser uma imagem nos formatos: jpeg, png e jpg.',
        'photo.max' => 'O \'photo\' deve ser uma imagem com tamanho máximo de 2048.',
        'page.integer' => 'O \'cep\' deve conter apenas números.',
        'per_page.integer' => 'O \'cep\' deve conter apenas números.',
        'order.in' => 'O \'order\' deve conter \'asc\' ou \'desc\'.',
        'show_deleted.in' => 'Somente valores \'0\' e \'1\' são permitidos para \'show_deleted\''
    ];
}
