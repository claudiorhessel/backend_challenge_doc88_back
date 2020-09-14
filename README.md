# DOC88 - API para pedidos

Aplicação criada para atender os requisitos do teste do link abaixo:

https://github.com/doc88git/backend-challenge

API para pedidos, com cadastro de clientes, produtos, tipo de produtos e pedidos.

Criada utilizando o framework "Laravel/Lumen"

## Requisitos do servidor

PHP >= 7.0
OpenSSL PHP Extension
PDO PHP Extension
Mbstring PHP Extension
Composer

## Instalação

Via GITHUB:

Na pasta do servidor onde você deseja baixar a aplicação executar o comando abaixo:

git clone [URL da APLICAÇÃO]

## Criação do arquivo .env

Criar na raiz do projeto um arquivo .env, você pode renomear o arquivo .env.exemplo e modificálo com as configurações do seu servidor

## Baixar pacotes e dependências

Acessar o console de linha de comando na pasta raiz do projeto e executar o comando abaixo:

    composer install

## Criar tabelas

php artisan migrate

## Popular tabelas

php artisan db:seed

## Iniciar o servidor

php -S localhost:8000 -t public

## Rotas

Retorna a versão do framework

    get('/')

Retorna a documentação da API

    get('/api/v1/')

Rotas de Clientes

    prefixo = /api/v1/client
        get('/', "ClientController@getPagination")
        get('/all', "ClientController@getAll");
        get('/{id}', "ClientController@getById")
        post('/', "ClientController@store")
        put('/{id}', "ClientController@update")
        delete('/{id}', "ClientController@destroy")

Rotas de Produtos

    prefixo = /api/v1/product
        get('/', "ProductController@getPagination")
        get('/all', "ProductController@getAll")
        get('/{id}', "ProductController@getById")
        post('/', "ProductController@store")
        put('/{id}', "ProductController@update")
        delete('/{id}', "ProductController@destroy")

Rotas de Typos de produtos

    prefixo = /api/v1/type
        get('/', "TypeController@getPagination")
        get('/all', "TypeController@getAll")
        get('/{id}', "TypeController@getById")
        post('/', "TypeController@store")
        put('/{id}', "TypeController@update")
        delete('/{id}', "TypeController@destroy")

Rotas de Pedidos

    prefixo = /api/v1/order
        get('/', "OrderController@getPagination")
        get('/all', "OrderController@getAll")
        get('/{id}', "OrderController@getById")
        post('/', "OrderController@store")
        put('/{id}', "OrderController@update")
        delete('/{id}', "OrderController@destroy")

Tratamento para rotas inexistentes

    get('/{any:.*}', "HomeController@notFound")
    post('/{any:.*}', "HomeController@notFound")
    put('/{any:.*}', "HomeController@notFound")
    delete('/{any:.*}', "HomeController@notFound")
    patch('/{any:.*}', "HomeController@notFound")
    options('/{any:.*}', "HomeController@notFound")

## Consultar a documentação da API

public/swagger.json

## Atualizar documentação da API

.\vendor\bin\openapi app -o .\public\swagger.json

## ToDo

Implementar Autenticação
Criar testes unitários
Dockerizar a aplicação

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
