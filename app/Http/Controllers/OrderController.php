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

class OrderController extends Controller
{
    private $ordersModel;

    public function __construct(Order $ordersModel)
    {
        $this->ordersModel = $ordersModel;
        DB::enableQueryLog();
    }

    public function getAll() {
        try {
            $orders = $this->ordersModel
                           ->select(
                               'orders.*',
                               'products.*',
                               'types.name as type_name',
                               'clients.name as client_name'
                            )
                           ->leftJoin('clients', 'clients.id', '=', 'orders.client_id')
                           ->leftJoin('order_products', 'order_products.order_id', '=', 'orders.id')
                           ->leftJoin('products', 'products.id', '=', 'order_products.product_id')
                           ->leftJoin('types', 'types.id', '=', 'products.type_id')
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
        try {
            /*$order = OrderProduct::with('product')
                          ->with('order')
                          ->with('orderClient')
                          ->where('order_id', '=', (int)$id)
                          ->get();*/

            $order = $this->ordersModel
                          ->with('client')
                          ->with('productOrder')
                          ->where('id', '=', (int)$id)
                          ->get();
            if($order) {
                return response()->json($order, Response::HTTP_OK);
            } else {
                return response()->json(null, Response::HTTP_OK);
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
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $photoName = $photo->getClientOriginalName();
                    $destinationPath = rtrim(app()->basePath('public/images'));
                    $photo->move($destinationPath, $photoName);

                    $requestData = $request->all();
                    $requestData['photo'] = $photoName;
                    $order = $this->ordersModel->create($requestData);

                    return response()->json($order, Response::HTTP_CREATED);
                } else {
                    return response()->json(['error' => 'Erro com a foto.'], Response::HTTP_BAD_REQUEST);
                }
            } catch(QueryException $e) {
                return response()->json(['error' => 'Erro de conexão com o banco de dados'],
                                Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function update($id, Request $request) {
        $validator = Validator::make(
            $request->all(),
            ValidationOrder::RULE_ORDER
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $photoName = $photo->getClientOriginalName();
                    $destinationPath = rtrim(app()->basePath('public/images'));
                    $photo->move($destinationPath, $photoName);

                    $requestData = $request->all();
                    $requestData['photo'] = $photoName;
                    $order = $this->ordersModel->find($id)
                        ->update($requestData);

                    return response()->json($order, Response::HTTP_CREATED);
                } else {
                    return response()->json(['error' => 'Erro com a foto.'], Response::HTTP_BAD_REQUEST);
                }
            } catch(QueryException $e) {
                return response()->json(['error' => 'Erro de conexão com o banco de dados'],
                                Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function destroy($id) {
        try {
            $order = $this->ordersModel->find($id)->delete();

            return response()->json(null, Response::HTTP_OK);
        } catch(QueryException $e) {
            return response()->json(['error' => 'Erro de conexão com o banco de dados'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
