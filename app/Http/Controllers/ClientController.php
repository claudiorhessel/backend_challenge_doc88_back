<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationClient;
use Illuminate\Support\Carbon;

class ClientController extends Controller
{
    private $clientsModel;

    public function __construct(Client $clientsModel)
    {
        $this->clientsModel = $clientsModel;
    }

    /**
     * @OA\Get(
     *   path="/v1/client",
     *   summary="Paginação para lista de clientes",
     *   @OA\Parameter(
     *     name="page",
     *     description="Página a ser exibida",
     *     in="header",
     *     required=false,
     *     example=1,
     *     @OA\Schema(
     *       type="integer",
     *       default="1"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     description="Quantidade de itens por página",
     *     in="header",
     *     required=false,
     *     example=1,
     *     @OA\Schema(
     *       type="integer",
     *       default="15"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="order_by",
     *     description="Campo para ordenação da pesquisa de clientes",
     *     in="header",
     *     required=false,
     *     example="cep",
     *     @OA\Schema(
     *       type="string",
     *       default="id"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="order",
     *     description="Ordenação da pesquisa asc ou desc",
     *     in="header",
     *     required=false,
     *     example="asc",
     *     @OA\Schema(
     *       type="string",
     *       default="asc",
     *       enum={"asc", "desc"}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     description="Valor para o campo 'name', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="Fulado",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="email",
     *     description="Valor para o campo 'email', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="teste@teste.com",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="address",
     *     description="Valor para o campo 'address', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="Rua dos Tolos",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="complement",
     *     description="Valor para o campo 'complement', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="Ap 25",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="complement",
     *     description="Valor para o campo 'neighborhood' (bairro), pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="Berrini",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="phone",
     *     description="Valor para o campo 'phone', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="2345678",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cep",
     *     description="Valor para o campo 'cep', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="00000001",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="birth_date_start",
     *     description="Valor para o campo 'birth_date', pesquisa a partir desta data",
     *     in="header",
     *     required=false,
     *     example="1993-09-22",
     *     @OA\Schema(
     *       type="date"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="birth_date_end",
     *     description="Valor para o campo 'birth_date', pesquisa até esta data",
     *     in="header",
     *     required=false,
     *     example="1993-09-22",
     *     @OA\Schema(
     *       type="date"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="show_deleted",
     *     description="Exibir (1) ou não (0) os dados deletados",
     *     in="header",
     *     required=false,
     *     example="0",
     *     @OA\Schema(
     *       type="integer",
     *       default="0",
     *       enum={"0", "1"}
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="A list with clients"
     *   ),
     *   @OA\Response(
     *     response=406,
     *     description="Erro no formato ou tipo dos dados enviados"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erro no formato ou tipo dos dados enviados"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Erro interno do servidor"
     *   )
     * )
     */
    public function getPagination(Request $request)
    {
        $filters = $request->all();

        $where = array();
        $page = 1;
        $perPage = 5;
        $orderBy = 'id';
        $order = 'asc';

        $showDeleted = false;

        if(count($filters) >= 0) {
            $validator = Validator::make(
                $request->all(),
                ValidationClient::RULE_CLIENT_PAGINATION,
                ValidationClient::MESSAGE_CLIENT
            );

            if($validator->fails()) {
                return response()->json(
                    [
                        'error' => 'Erro no formato ou tipo dos dados enviados.',
                        'messages' => $validator->errors()
                    ],
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }
        }

        if(isset($filters['show_deleted']) && $filters['show_deleted'] == 1) {
            $showDeleted = true;
        }

        foreach($filters as $key => $value) {
            switch($key) {
                case 'id':
                    $where[] = [$key, '=', $value];
                    break;
                case 'name':
                case 'email':
                case 'address':
                case 'complement':
                case 'neighborhood':
                case 'phone':
                case 'cep':
                    $where[] = [$key, 'like', '%'.$value.'%'];
                    break;
                case 'birth_date_start':
                    $where[] = ['birth_date', '>=', Carbon::parse($value)->format('Y-m-d')];
                    break;
                case 'birth_date_end':
                    $where[] = ['birth_date', '<=', Carbon::parse($value)->format('Y-m-d')];
                    break;
                case 'order_by':
                    $orderBy = $value;
                    break;
                case 'order':
                    if($order === 'asc' || $order === 'desc') {
                        $order = $value;
                    }
                    break;
                case 'page':
                    $page = $value;
                    break;
                case 'per_page':
                    $perPage = $value;
                    break;
            }
        }

        try {
            $query = $this->clientsModel
                            ->where($where);
            if($showDeleted) {
                $query->withTrashed();
            }

            $clients = $query->orderBy($orderBy, $order)
                             ->paginate($perPage, ['*'], 'page', $page);

            if($clients && count($clients) > 0) {
                return response()->json($clients, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch (QueryException $e) {
            return response()->json(
                [
                    'error' => 'Erro de conexão com o banco de dados',
                    'message' => $e
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Erro interno do servidor.',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } finally {
            return response()->json(
                [
                    'error' => 'Erro interno do servidor.'
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @OA\Get(
     *   path="/v1/client/all",
     *   summary="Exibe todos os registros, exceto deletados",
     *   @OA\Response(
     *     response=200,
     *     description="Lista de clientes"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erro no formato ou tipo dos dados enviados"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Erro interno do servidor"
     *   )
     * )
     */
    public function getAll()
    {
        try {
            $clients = $this->clientsModel
                            ->get();
            if($clients && count($clients) > 0) {
                return response()->json($clients, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch (QueryException $e) {
            return response()->json(
                ['error' => 'Erro de conexão com o banco de dados'],
                array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR)
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Erro interno do servidor.',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @OA\Get(
     *   path="/v1/client/{id}",
     *   summary="Exibe um cliente ativo pelo {id}",
     *   @OA\Parameter(
     *     name="show_deleted",
     *     description="{id} do cliente a ser localizado.",
     *     in="header",
     *     required=false,
     *     example="0",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Dados do cliente solicitado"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erro no formato ou tipo dos dados enviados"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Erro interno do servidor"
     *   )
     * )
     */
    public function getById($id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ValidationClient::RULE_CLIENT_ID,
            ValidationClient::MESSAGE_CLIENT
        );

        if($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Erro no formato ou tipo dos dados enviados.',
                    'messages' => $validator->errors()
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        try {
            $client = $this->clientsModel->find($id);
            if($client) {
                return response()->json($client, Response::HTTP_OK);
            } else {
                return response()->json(
                    ['message' => 'Nenhum cliente ativo encontrado com o \'id\': '.$id.'.'],
                    Response::HTTP_OK
                );
            }
        } catch(QueryException $e) {
            return response()->json(
                ['error' => 'Erro de conexão com o banco de dados'],
                array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR)
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Erro interno do servidor.',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @OA\post(
     *   path="/v1/client",
     *   summary="Insere um novo cliente",
     *   @OA\Parameter(
     *     name="name",
     *     description="Valor para o campo 'name'",
     *     in="header",
     *     required=true,
     *     example="Fulado",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="email",
     *     description="Valor para o campo 'email'",
     *     in="header",
     *     required=true,
     *     example="teste@teste.com",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="address",
     *     description="Valor para o campo 'address'",
     *     in="header",
     *     required=true,
     *     example="Rua dos Tolos",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="complement",
     *     description="Valor para o campo 'complement'",
     *     in="header",
     *     required=true,
     *     example="Ap 25",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="complement",
     *     description="Valor para o campo 'neighborhood' (bairro)",
     *     in="header",
     *     required=true,
     *     example="Berrini",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="phone",
     *     description="Valor para o campo 'phone'",
     *     in="header",
     *     required=true,
     *     example="2345678",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cep",
     *     description="Valor para o campo 'cep'",
     *     in="header",
     *     required=true,
     *     example="00000001",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="birth_date",
     *     description="Valor para o campo 'birth_date'",
     *     in="header",
     *     required=true,
     *     example="1993-09-22",
     *     @OA\Schema(
     *       type="date"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Novo cliente registrado com sucesso"
     *   ),
     *   @OA\Response(
     *     response=406,
     *     description="Erro no formato ou tipo dos dados enviados"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Erro interno do servidor"
     *   )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ValidationClient::RULE_CLIENT,
            ValidationClient::MESSAGE_CLIENT
        );

        if($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Erro no formato ou tipo dos dados enviados.',
                    'message' => $validator->errors()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $client = $this->clientsModel->create($request->all());

            return response()->json($client, Response::HTTP_CREATED);
        } catch(QueryException $e) {
            return response()->json(
                ['error' => 'Erro de conexão com o banco de dados'],
                array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR)
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Erro interno do servidor.',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @OA\put(
     *   path="/v1/client/{id}",
     *   summary="Atualiza os dados do cliente",
     *   @OA\Parameter(
     *     name="id",
     *     description="{id} do cliente a ser atualizado.",
     *     in="header",
     *     required=true,
     *     example="0",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     description="Valor para o campo 'name'.",
     *     in="header",
     *     required=false,
     *     example="Fulado",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="email",
     *     description="Valor para o campo 'email'.",
     *     in="header",
     *     required=false,
     *     example="teste@teste.com",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="address",
     *     description="Valor para o campo 'address'.",
     *     in="header",
     *     required=false,
     *     example="Rua dos Tolos",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="complement",
     *     description="Valor para o campo 'complement'.",
     *     in="header",
     *     required=false,
     *     example="Ap 25",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="complement",
     *     description="Valor para o campo 'neighborhood' (bairro).",
     *     in="header",
     *     required=false,
     *     example="Berrini",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="phone",
     *     description="Valor para o campo 'phone'.",
     *     in="header",
     *     required=false,
     *     example="2345678",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="cep",
     *     description="Valor para o campo 'cep'.",
     *     in="header",
     *     required=false,
     *     example="00000001",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="birth_date",
     *     description="Valor para o campo 'birth_date'.",
     *     in="header",
     *     required=false,
     *     example="1993-09-22",
     *     @OA\Schema(
     *       type="date"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Dados do cliente atualizado."
     *   ),
     *   @OA\Response(
     *     response=406,
     *     description="Erro no formato ou tipo dos dados enviados."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Erro interno do servidor."
     *   )
     * )
     */
    public function update($id, Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ValidationClient::RULE_CLIENT_UPDATE,
            ValidationClient::MESSAGE_CLIENT
        );

        if($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Erro no formato ou tipo dos dados enviados.',
                    'messages' => $validator->errors()
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        try {
            $client = $this->clientsModel->find($id)
                ->update($request->all());

            return response()->json($client, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(
                ['error' => 'Erro de conexão com o banco de dados'],
                array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR)
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Erro interno do servidor.',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @OA\delete(
     *   path="/v1/client/{id}",
     *   summary="Deleta um cliente pelo {id}",
     *   @OA\Parameter(
     *     name="id",
     *     description="{id} do cliente a ser deletado",
     *     in="header",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Mensagem informando que o cliente foi deletado."
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erro no formato ou tipo dos dados enviados."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Erro interno do servidor."
     *   )
     * )
     */
    public function destroy($id)
    {
        $validator = Validator::make(
            ['id' => $id],
            ValidationClient::RULE_CLIENT_ID,
            ValidationClient::MESSAGE_CLIENT
        );

        if($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Erro no formato ou tipo dos dados enviados.',
                    'messages' => $validator->errors()
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        try {
            $client = $this->clientsModel->find($id)->delete();

            return response()->json(
                ['ok' => 'Cliente com \'id\': '.$id.' foi deletado com sucesso.'],
                Response::HTTP_OK
            );
        } catch(QueryException $e) {
            return response()->json(
                ['error' => 'Erro de conexão com o banco de dados'],
                array('status'=> Response::HTTP_INTERNAL_SERVER_ERROR)
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Erro interno do servidor.',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
