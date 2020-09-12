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

    public function getAll() {
        try {
            $orders = $this->ordersModel
                          ->with('client')
                          ->with('orderProduct.product.type')
                          ->get();

            if($orders && count($orders) > 0) {
                return response()->json($orders, Response::HTTP_OK);
            } else {
                return response()->json([], Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get($id) {
        $validator = Validator::make(
            array("id" => $id),
            ValidationOrder::RULE_ORDER_GET,
            ValidationOrder::MESSAGE_ORDER_GET
        );

        if($validator->fails()) {
            return response()->json(['error' => 'Erro no formato/tipo dos dados enviados.','messages' => $validator->errors()], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            $order = $this->ordersModel
                          ->with('client')
                          ->with('orderProduct.product.type')
                          ->where('id', '=', (int)$id)
                          ->first();
            if($order) {
                return response()->json($order, Response::HTTP_OK);
            } else {
                return response()->json(['error' => 'Pedido não encotrado.'], Response::HTTP_OK);
            }
        } catch(QueryException $e) {
            dd($e);
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request) {
        $validator = Validator::make(
            $request->all(),
            ValidationOrder::RULE_ORDER
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
                return response()->json(['error' => 'Erro de conexão com o banco de dados'],
                                Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function update($id, Request $request) {
        return response()->json(['error' => 'Alterações no pedido não são autorizadas, você deve cancelá-lo e efetuar um novo pedido.'], Response::HTTP_NOT_FOUND);
    }

    public function destroy($id) {
        try {
            $order = $this->ordersModel->find($id)->delete();

            return response()->json(['success' => 'Pedido cancelado.'], Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function newOrderMail($order)
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
