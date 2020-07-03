<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 20/05/2018
 * Time: 17:07
 */

namespace App\Http\Controllers;

use Input;
use Validator;
use Response;
use Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Util\ValidationUtils;

abstract class BaseController extends Controller
{
    protected $model;
    protected $collection;
    protected $eager;

    protected $onlyStore;
    protected $onlyUpdate;

    /* Parametros que pueden ser utilziados para filtrar el recurso en el index */
    protected $whereable;

    abstract function setModel();

    abstract function setCollection();

    abstract function setEager();

    public function setWhereable()
    {
        $this->whereable = array();
    }

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        //$this->middleware('auth:api');
        //$this->middleware('statesChecker');
        //inicializo el modelo para el codigo dinamico
        $this->setModel();
        $this->setCollection();
        $this->setEager();
        $this->setWhereable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = array();

        $getAll = false;

        $hasPaginate = false;

        if (empty($this->eager)) {
            if (empty($this->whereable)) {
                $getAll = true;
            } else {
                $filterValues = array_filter(Input::only($this->whereable));
                if (!empty($filterValues)) {
                    $i = 0;
                    foreach ($filterValues as $col => $value) {
                        if ($i == 0) {
                            $i = 1;
                            $result = call_user_func($this->model . '::where', $col, $value);
                        } else {
                            $result = $result->where($col, $value);
                        }
                    }
                    $getAll = false;
                } else {
                    $getAll = true;
                }
            }
        } else {
            $result = call_user_func($this->model . '::with', $this->eager);
            $filterValues = array_filter(Input::only($this->whereable));
            if (!empty($filterValues)) {
                foreach ($filterValues as $col => $value) {
                    $result = $result->where($col, $value);
                }
            }
            $getAll = false;
        }

        if (Input::has('paginate')) {
            $hasPaginate = Input::get('paginate');
        }

        //Verifico si debo Paginar la respuesta o no
        if ($getAll) {
            if ($hasPaginate) {
                $result = call_user_func($this->model . '::paginate', ($hasPaginate));

                $result->withPath(Input::url() . '?paginate=' . $hasPaginate);
            } else {
                $result = call_user_func($this->model . '::all');
            }
        } else {
            if ($hasPaginate) {
                $result = $result->paginate($hasPaginate)->withPath(Input::url() . '?paginate=' . $hasPaginate);
            } else {
                $result = $result->get();
            }
        }

        return call_user_func($this->collection . '::collection', $result);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $record = $this->findRecord($id);
        if (!isset($record)) {
            return $this->makeResponse('RECORD_DOES_NOT_EXISTS', 400);
        }
        return new $this->collection($record);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $val = $this->isValid($request);

        if ($val !== true) {
            return $this->makeResponse($val, 400);
        }

        $record = new $this->model(isset($this->onlyStore) ? $request->only($this->onlyStore) : $request->all());
        try {
            $record->save();

            if (!empty($this->eager)) {
                $record->load($this->eager);
            }

            return new $this->collection($record);
        } catch (\Exception $ex) {
            return $this->handleEx($ex, $record, $request);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $val = $this->isValid($request, $id);

        if ($val !== true) {
            return $this->makeResponse($val, 400);
        }

        $record = $this->findRecord($id);
        if (!isset($record)) {
            return $this->makeResponse('RECORD_DOES_NOT_EXISTS', 400);
        }

        try {
            $record->fill($this->getAvailableReuqestParamsForUpdate($request));
            $record->save();
            $record = $this->findRecord($id);

            return new $this->collection($record);
        } catch (\Exception $ex) {
            return $this->handleEx($ex, $record, $request);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $record = $this->findRecord($id);
        if (!isset($record)) {
            return $this->makeResponse('RECORD_DOES_NOT_EXISTS', 400);
        }
        try {
            $record->delete();

            return new $this->collection($record);
        } catch (\Exception $ex) {
            return $this->handleEx($ex, $record, $request);
        }
    }

    protected function findRecord($id)
    {
        if (empty($this->eager)) {
            return call_user_func($this->model . '::find', $id);
        } else {
            return call_user_func($this->model . '::with', $this->eager)->find($id);
        }
    }

    /*
     * @param  \Illuminate\Http\Request  $request
    *  @return Boolean
    *   Retorna un array de errores en caso de que no sea valida la peticion.
    */
    protected function isValid(Request $request, $id = null)
    {

        if (!$this->isStore($request) && !$this->isUpdate($request)) {
            return true;
        }

        if (!isset($this->onlyUpdate) && !isset($this->onlyStore)) {
            return true;
        }

        if ($this->isStore($request) && isset($this->onlyStore)) {
            $validationRules = ValidationUtils::getRulesFromArray($this->onlyStore);
        } else if ($this->isUpdate($request) && isset($this->onlyUpdate)) {
            $validationRules = ValidationUtils::getRulesFromArray($this->onlyUpdate);
        } else {
            $validationRules = ValidationUtils::getRulesFromArray($this->getValidableRequestParams($request));
        }

        $customRules = $this->getcustomRules($request);

        /**
         * Si el id no se está enviando, entonces se asigna un ID comodin para poder realizar la consulta
         * y validar que el campo unique se cumpla.
         */
        if (!isset($id)) {
            $id = -12345745678910; //Este ID es comodin, en teoria no deberia existir en la BD - Victor David
        }

        array_walk($customRules, function (&$v, $k) use ($id) {
            $v = str_replace(':id', $id, $v);
        });

        if (count($customRules) > 0) {
            $validationRules[0] = $customRules[0];
            $validationRules[1] = $customRules[1];
        }

        $validate = Validator::make($request->only($this->onlyStore),
            $validationRules[0],
            $validationRules[1]);

        if ($validate->fails()) {
            return $validate->errors();
        }

        return true;
    }

    protected function makeResponse($data, $statusCode)
    {
        return Response::make($data, $statusCode);
    }

    protected function handleEx(\Exception $ex, $record = null, Request $request = null)
    {
        $mensaje = "Exception: " . $ex->getMessage();
        $mensaje .= "\n" . "Record: " . ($record == null ? "Null" : json_encode($record->toArray()));
        $mensaje .= "\n" . "Request: " . ($request == null ? "Null" : json_encode($request->all()));
        $mensaje .= "\n" . "Usuario: " . auth()->user();
        $mensaje .= "\n" . "Traza: " . $ex->getTraceAsString();
        Log::critical($mensaje);
        return $this->makeResponse("INTERNAL_SERVER", 500);
    }

    public static function logEx(\Exception $ex)
    {
        $mensaje = "Exception: " . $ex->getMessage();
        try {
            $mensaje .= "\n" . "Usuario: " . auth()->user();
        } catch (\Exception $te) {
            $mensaje .= "\n" . "Usuario: No fue posible consultar";
        }
        $mensaje .= "\n" . "Traza: " . $ex->getTraceAsString();
        Log::critical($mensaje);
    }

    public function isStore($request)
    {
        return preg_match('/store$/', Route::getCurrentRoute()->getName());
    }

    public function isUpdate($request)
    {
        return preg_match('/update$/', Route::getCurrentRoute()->getName());
    }

    //Retorna un array con las keys de la peticion que están permitidas en
    //only update
    private function getValidableRequestParams($request)
    {

        if (!isset($this->onlyUpdate) || !$this->isUpdate($request)) {
            return array_keys($request->all());
        }
        //Store y Update son los unicos metodos que usan los parametros de la petición los demás lo ignoran
        return (array_keys(array_filter($request->only($this->onlyUpdate))));
    }

    //Debido a que en el momento de actualizar un registro solo se envian algunos valores en la peticion
    //cuando se hace $request->only retorna null para los atributos que no llegan en la petición lo que hace
    //que los objetos guarden null en la base de datos o genera errores indeseados
    //para eso se consultan los atributos de la petición y se filtran los nulos
    protected function getAvailableReuqestParamsForUpdate(Request $request)
    {
        if (!isset($this->onlyUpdate)) {
            return $request->all();
        }

        /*
         * Con este codigo se quitan los valores nulos dejados por el metodo only
        */

        return array_where($request->only($this->onlyUpdate), function ($key, $value) {
            if (is_null($value))
                return false;
            else
                return true;
        });
    }

    /**
     * Reglas de validacion personalizadas, debe retornar un vector con 2 posiciones.
     * La primera el arreglo de validaciones, la segunda el arreglo de mensajes.
     */
    protected function getcustomRules($request)
    {
        return array();
    }
}
