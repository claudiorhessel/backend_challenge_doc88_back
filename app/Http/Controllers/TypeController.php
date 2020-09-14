<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\ValidationType;

class TypeController extends Controller
{
    private $typesModel;

    public function __construct(Type $typesModel)
    {
        $this->typesModel = $typesModel;
    }

    /**
     * @OA\Get(
     *   path="/v1/type",
     *   summary="Paginação para lista de tipos de produtos",
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
     *     name="ID",
     *     description="Valor para o campo 'ID'.",
     *     in="header",
     *     required=false,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
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
     *     description="Uma lista de tipos de produtos com paginação"
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
                ValidationType::RULE_TYPE_PAGINATION,
                ValidationType::MESSAGE_TYPE
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
                    $where[] = [$key, 'like', '%'.$value.'%'];
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
            $query = $this->typesModel
                            ->where($where);
            if($showDeleted) {
                $query->withTrashed();
            }

            $clients = $query->orderBy($orderBy, $order)
                             ->paginate($perPage, ['*'], 'page', $page);

            if($clients && count($clients) > 0) {
                return response()->json($clients, Response::HTTP_OK);
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
     *   path="/v1/type/all",
     *   summary="Exibe todos os registros, exceto deletados",
     *   @OA\Response(
     *     response=200,
     *     description="Lista de tipo de produtos"
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
            $types = $this->typesModel->all();
            if($types && count($types) > 0) {
                return response()->json($types, Response::HTTP_OK);
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
     *   path="/v1/type/{id}",
     *   summary="Exibe um tipo de produto ativo pelo {id}",
     *   @OA\Parameter(
     *     name="show_deleted",
     *     description="{id} do tipo de produto a ser localizado.",
     *     in="header",
     *     required=false,
     *     example="0",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Dados do tipo de produto solicitado"
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
        try {
            $type = $this->typesModel->find($id);
            if($type) {
                return response()->json($type, Response::HTTP_OK);
            } else {
                return response()->json(
                    ['message' => 'Nenhum tipo de produto ativo encontrado com o \'id\': '.$id.'.'],
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
     *   path="/v1/type",
     *   summary="Insere um novo tipo de produto",
     *   @OA\Parameter(
     *     name="name",
     *     description="Valor para o campo 'name'",
     *     in="header",
     *     required=true,
     *     example="Pastel",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Novo tipo de produto registrado com sucesso"
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
            ValidationType::RULE_TYPE
        );

        if($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Erro no formato ou tipo dos dados enviados.',
                    'message' => $validator->errors()
                ],
                Response::HTTP_BAD_REQUEST
            );
        } else {
            try {
                $type = $this->typesModel->create($request->all());

                return response()->json($type, Response::HTTP_CREATED);
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
     *   path="/v1/type",
     *   summary="Atualiza um tipo de produto",
     *   @OA\Parameter(
     *     name="name",
     *     description="Valor para o campo 'name'",
     *     in="header",
     *     required=true,
     *     example="Pastel",
     *     @OA\Schema(
     *       type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Novo tipo de produto registrado com sucesso"
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
            ValidationType::RULE_TYPE_UPDATE,
            ValidationType::MESSAGE_TYPE
        );

        if($validator->fails()) {
            return response()->json(
                [
                    'error' => 'Erro no formato ou tipo dos dados enviados.',
                    'messages' => $validator->errors()
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        } else {
            try {
                $type = $this->typesModel
                             ->find($id)
                             ->update($request->all());

                return response()->json(
                    [
                        'message' => 'Produto atualizado com sucesso.'
                    ],
                    Response::HTTP_CREATED
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

    /**
     * @OA\delete(
     *   path="/v1/type/{id}",
     *   summary="Deleta um tipo de produto pelo {id}",
     *   @OA\Parameter(
     *     name="id",
     *     description="{id} do tipo de produto a ser deletado",
     *     in="header",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Mensagem informando que o tipo de produto foi deletado."
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
            ValidationType::RULE_TYPE_ID,
            ValidationType::MESSAGE_TYPE
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
            $type = $this->typesModel->find($id)->delete();

            return response()->json(
                ['ok' => 'Tipo de produto com \'id\': '.$id.' foi deletado com sucesso.'],
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
