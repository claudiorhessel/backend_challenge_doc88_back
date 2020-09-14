<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Builder;

class OrderController extends Controller
{
    private $ordersModel;
    private $orderProductModel;

    public function __construct(Order $ordersModel, OrderProduct $orderProductModel)
    {
        $this->ordersModel = $ordersModel;
        $this->orderProductModel = $orderProductModel;
        DB::enableQueryLog();
    }

    /**
     * @OA\Get(
     *   path="/v1/order",
     *   summary="Paginação para lista de pedidos",
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
     *     name="order_id",
     *     description="Valor para o campo 'order_id'.",
     *     in="header",
     *     required=false,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="product_id",
     *     description="Valor para o campo 'product_id'.",
     *     in="header",
     *     required=false,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="product_name",
     *     description="Valor para o campo 'product_name', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="Fulado",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="product_type",
     *     description="Valor para o campo 'product_type', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="Fulado",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="client_id",
     *     description="Valor para o campo 'client_id'.",
     *     in="header",
     *     required=false,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="client_name",
     *     description="Valor para o campo 'client_name', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="Fulado",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="client_email",
     *     description="Valor para o campo 'client_email', pesquisa do tipo 'like'",
     *     in="header",
     *     required=false,
     *     example="teste@teste.com",
     *     @OA\Schema(
     *       type="string"
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

        $productId = null;
        $productName = null;
        $productType = null;

        $clientId = null;
        $clientName = null;
        $clientEmail = null;

        $page = 1;
        $perPage = 5;
        $orderBy = 'id';
        $order = 'asc';

        $showDeleted = false;

        if(count($filters) >= 0) {
            $validator = Validator::make(
                $request->all(),
                ValidationOrder::RULE_ORDER_PAGINATION,
                ValidationOrder::MESSAGE_ORDER
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

        if(count($filters) > 0) {
            foreach($filters as $key => $value) {
                switch($key) {
                    case 'order_id':
                        $where[] = ['id', '=', $value];
                        break;

                    case 'product_id':
                        $productId = $value;
                        break;
                    case 'product_name':
                        $productName = '%'.$value.'%';
                        break;
                    case 'product_type':
                        $productType = '%'.$value.'%';
                        break;

                    case 'client_id':
                        $clientId = ['id', '=', $value];
                        break;
                    case 'client_name':
                        $clientName = '%'.$value.'%';
                        break;
                    case 'client_email':
                        $clientEmail = '%'.$value.'%';
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
        }

        try {
            $query = $this->ordersModel
                            ->with('client')
                            ->whereHas('client', function (Builder $queryClient) use($clientId, $clientName, $clientEmail) {
                                $queryClient->withTrashed();
                                if($clientId)
                                    $queryClient->where('id', '=', $clientId);
                                if($clientName)
                                    $queryClient->where('name', 'like', $clientName);
                                if($clientEmail)
                                    $queryClient->where('name', 'like', $clientEmail);
                            })
                            ->with('orderProduct.product.type')
                            ->whereHas('orderProduct.product', function (Builder $queryOrderProduct) use($productId, $productName) {
                                $queryOrderProduct->withTrashed();
                                if($productId)
                                    $queryOrderProduct->where('id', '=', $productId);
                                if($productName)
                                    $queryOrderProduct->where('name', 'like', $productName);
                            })
                            ->whereHas('orderProduct.product.type', function (Builder $query) use($productType) {
                                $query->withTrashed();
                                if($productType)
                                    $query->where('name', 'like', $productType);
                            });

            if($showDeleted) {
                $query->withTrashed();
            }

            $orders = $query->orderBy($orderBy, $order)
                             ->paginate($perPage, ['*'], 'page', $page);

            if($orders && count($orders) > 0) {
                return response()->json($orders, Response::HTTP_OK);
            } else {
                return response()->json(
                    [
                        'message' => 'Nenhum registro encontrado'
                    ],
                    Response::HTTP_OK
                );
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
        }
    }

    /**
     * @OA\Get(
     *   path="/v1/order/all",
     *   summary="Exibe todos os registros, exceto deletados",
     *   @OA\Response(
     *     response=200,
     *     description="Lista de Pedidos"
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
            $clients = $this->ordersModel
                            ->with('client')
                            ->with('orderProduct.product.type')
                            ->get();
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
     *   path="/v1/order/{id}",
     *   summary="Exibe um pedido ativo pelo {id}",
     *   @OA\Parameter(
     *     name="show_deleted",
     *     description="{id} do pedido a ser localizado.",
     *     in="header",
     *     required=false,
     *     example="0",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Dados do pedido solicitado"
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
            array("id" => $id),
            ValidationOrder::RULE_ORDER_ID,
            ValidationOrder::MESSAGE_ORDER
        );

        if($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Erro no formato/tipo dos dados enviados.',
                    'messages' => $validator->errors()
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        try {
            $order = $this->ordersModel
                          ->with('client')
                          ->withTrashed()
                          ->whereHas('client', function (Builder $query) {
                              $query->withTrashed();
                          })
                          ->with('orderProduct.product.type')
                          ->whereHas('orderProduct.product', function (Builder $query) {
                              $query->withTrashed();
                          })
                          ->where('id', '=', (int)$id)
                          ->first();
            if($order) {
                return response()->json($order, Response::HTTP_OK);
            } else {
                return response()->json(
                    ['message' => 'Nenhum pedido ativo encontrado com o \'id\': '.$id.'.'],
                    Response::HTTP_OK
                );
            }
        } catch (QueryException $e) {
            return response()->json(
                [
                    'error' => 'Erro de conexão com o banco de dados',
                    'message' => $e
                ],
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
     *   path="/v1/order",
     *   summary="Insere um novo pedido",
     *   @OA\Parameter(
     *     name="client_id",
     *     description="Valor para o campo 'client_id'",
     *     in="header",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="product",
     *     description="Valor para o campo 'email'",
     *     in="header",
     *     required=true,
     *     example="teste@teste.com",
     *     @OA\Schema(
     *       type="array",
     *       @OA\Items(
     *         type="integer",
     *         example="{product_id: 1, product_qtd:20}"
     *       )
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
            ValidationOrder::RULE_ORDER,
            ValidationOrder::MESSAGE_ORDER
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                $order = $this->ordersModel->create($request->all());
                if($order) {
                    $products = $request->all()['products'];
                    foreach($products as $product) {
                        $product['order_id'] = $order->id;
                        $this->orderProductModel->create($product);
                    }
                }
                $orderCreated = $this->ordersModel
                              ->with('client')
                              ->with('orderProduct.product.type')
                              ->find($order->id);
                $this->newOrderMail($orderCreated);
                return response()->json($order, Response::HTTP_CREATED);
            } catch(QueryException $e) {
                return response()->json(
                    [
                        'error' => 'Erro de conexão com o banco de dados',
                        'message' => $e
                    ],
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

    /**
     * @OA\put(
     *   path="/v1/order/{id}",
     *   summary="Alterações no pedido não são autorizadas, você deve cancelá-lo e efetuar um novo pedido.",
     *   @OA\Response(
     *     response=404,
     *     description="Alterações no pedido não são autorizadas, você deve cancelá-lo e efetuar um novo pedido."
     *   )
     * )
     */
    public function update($id, Request $request)
    {
        return response()->json(
            ['error' => 'Alterações no pedido não são autorizadas, você deve cancelá-lo e efetuar um novo pedido.'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @OA\delete(
     *   path="/v1/order/{id}",
     *   summary="Deleta um pedido pelo {id}",
     *   @OA\Parameter(
     *     name="id",
     *     description="{id} do pedido a ser deletado",
     *     in="header",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Mensagem informando que o pedido foi deletado."
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
        try {
            $order = $this->ordersModel->find($id)->delete();

            return response()->json(
                ['ok' => 'Pedido com \'id\': '.$id.' foi deletado com sucesso.'],
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

    private function newOrderMail($order)
    {
        $data = array('name' => $order->client->name, 'order' => $order);

        Mail::send('orders/mail', $data, function($message) use ($order) {
            $message->to( $order->client->email, $order->client->name)
                    ->subject('Novo pedido efetuado: #' . $order->id);
            $message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
        });

        return true;
    }
}
