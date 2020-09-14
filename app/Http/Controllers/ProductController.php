<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    private $productsModel;

    public function __construct(Product $productsModel)
    {
        $this->productsModel = $productsModel;
        DB::enableQueryLog();
    }

    /**
     * @OA\Get(
     *   path="/v1/product",
     *   summary="Paginação para lista de produtos.",
     *   @OA\Parameter(
     *     name="page",
     *     description="Página a ser exibida.",
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
     *     description="Quantidade de itens por página.",
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
     *     description="Campo para ordenação da pesquisa.",
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
     *     description="Ordenação da pesquisa asc ou desc.",
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
     *     name="id",
     *     description="Valor para o campo 'id'.",
     *     in="header",
     *     required=false,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     description="Valor para o campo 'name', pesquisa do tipo 'like'.",
     *     in="header",
     *     required=false,
     *     example="Carne",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="price",
     *     description="Valor para o campo 'price', pesquisa do tipo 'like'.",
     *     in="header",
     *     required=false,
     *     example="10.50",
     *     @OA\Schema(
     *       type="decimal"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="type_name",
     *     description="Valor para o campo 'type_name', pesquisa do tipo 'like'.",
     *     in="header",
     *     required=false,
     *     example="Pastel",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="show_deleted",
     *     description="Exibir (1) ou não (0) os dados deletados.",
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
     *     description="Lista de produtos com paginação."
     *   ),
     *   @OA\Response(
     *     response=406,
     *     description="Erro no formato ou tipo dos dados enviados."
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
    public function getPagination(Request $request)
    {
        $filters = $request->all();

        $where = array();
        $page = 1;
        $perPage = 5;
        $orderBy = 'id';
        $order = 'asc';

        $showDeleted = false;
        $typeName = '';

        if(count($filters) >= 0) {
            $validator = Validator::make(
                $request->all(),
                ValidationProduct::RULE_PRODUCT_PAGINATION,
                ValidationProduct::MESSAGE_PRODUCT
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
                case 'price':
                    $where[] = [$key, '=', $value];
                    break;
                case 'name':
                    $where[] = [$key, 'like', '%'.$value.'%'];
                    break;
                case 'type_name':
                    $typeName = '%'.$value.'%';
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
            $query = $this->productsModel
                          ->with('type')
                          ->whereHas('type', function (Builder $query) use($typeName) {
                              $query->withTrashed();
                              if($typeName)
                                  $query->where('name', 'like', $typeName);
                          })
                          ->where($where);

            if($showDeleted) {
                $query->withTrashed();
            }

            $product = $query->orderBy($orderBy, $order)
                             ->paginate($perPage, ['*'], 'page', $page);

            if($product && count($product) > 0) {
                return response()->json($product, Response::HTTP_OK);
            } else {
                return response()->json(
                    [
                        'message' => 'Nenhum registro encontrado.'
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
     *   path="/v1/product/all",
     *   summary="Exibe todos os registros, exceto deletados",
     *   @OA\Response(
     *     response=200,
     *     description="Lista de produtos"
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erro de conexão com o banco de dados"
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
            $products = $this->productsModel
                             ->with('type')
                             ->get();

            if($products && count($products) > 0) {
                return response()->json($products, Response::HTTP_OK);
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
     *   path="/v1/product/{id}",
     *   summary="Exibe um produto ativo pelo {id}",
     *   @OA\Parameter(
     *     name="show_deleted",
     *     description="{id} do produto a ser localizado.",
     *     in="header",
     *     required=false,
     *     example="0",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Dados do produto solicitado"
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
            ValidationProduct::RULE_PRODUCT_ID,
            ValidationProduct::MESSAGE_PRODUCT
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
            $product = $this->productsModel
                            ->with('type')
                            ->find($id);
            if($product) {
                return response()->json($product, Response::HTTP_OK);
            } else {
                return response()->json(
                    ['message' => 'Nenhum produto ativo encontrado com o \'id\': '.$id.'.'],
                    Response::HTTP_OK
                );
            }
        } catch(QueryException $e) {
            return response()->json(
                [
                    'error' => 'Erro de conexão com o banco de dados',
                    'message' => $e
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR);
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
     *   path="/v1/product",
     *   summary="Insere um novo produto",
     *   @OA\Parameter(
     *     name="name",
     *     description="Valor para o campo 'name'",
     *     in="header",
     *     required=true,
     *     example="Pizza",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="price",
     *     description="Valor para o campo 'price'",
     *     in="header",
     *     required=true,
     *     example="10.50",
     *     @OA\Schema(
     *       type="decimal"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="type_id",
     *     description="Valor para o campo 'type_id'",
     *     in="header",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="photo",
     *     description="Valor para o campo 'photo'",
     *     in="header",
     *     required=true,
     *     example="image",
     *     @OA\Schema(
     *       type="image"
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
            ValidationProduct::RULE_PRODUCT,
            ValidationProduct::MESSAGE_PRODUCT
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $photoOriginalName = $photo->getClientOriginalName();

                    $photoName = pathinfo($photoOriginalName, PATHINFO_FILENAME);
                    $photoName = $this->cleanString($photoName);
                    $photoName = preg_replace('/[^A-Za-z0-9\-]/', '_', $photoName);
                    $photoName = strtolower($photoName);

                    $photoExtension = pathinfo($photoOriginalName, PATHINFO_EXTENSION);

                    $photoNewName = uniqid().'_'.$photoName.'.'.$photoExtension;

                    $destinationPath = rtrim(app()->basePath('public/images'));
                    $photo->move($destinationPath, $photoNewName);

                    $requestData = $request->all();
                    $saveData = array();

                    $saveData['name'] = $requestData['name'];
                    $saveData['price'] = $requestData['price'];
                    $saveData['type_id'] = isset($requestData['type_id']) ?: 1;
                    $saveData['photo_original_name'] = $photoOriginalName;
                    $saveData['photo_destination_path'] = 'public/images';
                    $saveData['photo_name'] = $photoNewName;

                    $product = $this->productsModel->create($saveData);

                    return response()->json($product, Response::HTTP_CREATED);
                } else {
                    return response()->json(['error' => 'Erro com a foto.'], Response::HTTP_BAD_REQUEST);
                }
            } catch(QueryException $e) {
                return response()->json(
                    [
                        'error' => 'Erro de conexão com o banco de dados',
                        'message' => $e
                    ],
                    Response::HTTP_INTERNAL_SERVER_ERROR);
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
     *   path="/v1/product",
     *   summary="Atualiza um produto",
     *   @OA\Parameter(
     *     name="id",
     *     description="{id} do registro a ser atualizado",
     *     in="header",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     description="Valor para o campo 'name'",
     *     in="header",
     *     required=true,
     *     example="Pizza",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="price",
     *     description="Valor para o campo 'price'",
     *     in="header",
     *     required=true,
     *     example="10.50",
     *     @OA\Schema(
     *       type="decimal"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="type_id",
     *     description="Valor para o campo 'type_id'",
     *     in="header",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="photo",
     *     description="Valor para o campo 'photo'",
     *     in="header",
     *     required=true,
     *     example="image",
     *     @OA\Schema(
     *       type="image"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cliente atualizado com sucesso"
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
    public function update($id, Request $request)
    {
        $validationData = $request->all();
        $validationData['id'] = $id;
        $validator = Validator::make(
            $validationData,
            ValidationProduct::RULE_PRODUCT_UPDATE,
            ValidationProduct::MESSAGE_PRODUCT
        );

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        } else {
            try {
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $photoOriginalName = $photo->getClientOriginalName();

                    $photoName = pathinfo($photoOriginalName, PATHINFO_FILENAME);
                    $photoName = $this->cleanString($photoName);
                    $photoName = preg_replace('/[^A-Za-z0-9\-]/', '_', $photoName);
                    $photoName = strtolower($photoName);

                    $photoExtension = pathinfo($photoOriginalName, PATHINFO_EXTENSION);

                    $photoNewName = uniqid().'_'.$photoName.'.'.$photoExtension;

                    $destinationPath = rtrim(app()->basePath('public/images'));
                    $photo->move($destinationPath, $photoNewName);

                    $requestData = $request->all();
                    $saveData = array();

                    $saveData['id'] = $id;
                    $saveData['name'] = $requestData['name'];
                    $saveData['price'] = $requestData['price'];
                    $saveData['type_id'] = isset($requestData['type_id']) ?: 1;
                    $saveData['photo_original_name'] = $photoOriginalName;
                    $saveData['photo_destination_path'] = 'public/images';
                    $saveData['photo_name'] = $photoNewName;

                    $product = $this->productsModel
                                    ->find($id)
                                    ->update($saveData);

                    return response()->json(
                        [
                            'message' => 'Produto atualizado com sucesso.'
                        ],
                        Response::HTTP_CREATED
                    );
                } else {
                    return response()->json(['error' => 'Erro com a foto.'], Response::HTTP_BAD_REQUEST);
                }
            } catch(QueryException $e) {
                return response()->json(
                    [
                        'error' => 'Erro de conexão com o banco de dados',
                        'message' => $e
                    ],
                    Response::HTTP_INTERNAL_SERVER_ERROR);
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
     * @OA\delete(
     *   path="/v1/product/{id}",
     *   summary="Deleta um produto pelo {id}",
     *   @OA\Parameter(
     *     name="id",
     *     description="{id} do produto a ser deletado",
     *     in="header",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Mensagem informando que o registro foi deletado."
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
            ValidationProduct::RULE_PRODUCT_ID,
            ValidationProduct::MESSAGE_PRODUCT
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
            $product = $this->productsModel->find($id)->delete();

            return response()->json(
                ['ok' => 'Produto com \'id\': '.$id.' foi deletado com sucesso.'],
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

    private function cleanString($text) {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-',
            '/[\'<>,"-]/u'   =>   '_',
            '/ /'           =>   '__',
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }
}
