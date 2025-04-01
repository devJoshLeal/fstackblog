<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
        $this->middleware('api.checkparams', ['except' => ['index', 'show']]);
    }
    public $apiTemplate = array(
        'status'    => 'success',
        'code'      => 200,
        'message'   => ''
    );

    public function index(){
        $categories = Category::all();
        $this->apiTemplate['data'] = $categories;
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }
    public function show($id){
        $category = Category::find($id);
        if($category){
            $this->apiTemplate['message'] = 'Categoria encontrada';
            $this->apiTemplate['data'] = $category;
        }
        else{
            $this->apiTemplate['status'] = 'error';
            $this->apiTemplate['code'] = 404;
            $this->apiTemplate['message'] = 'Categoria no encontrada';
        }
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }
    public function store(Request $request){
        // Recoger los datos por POST
        $json = $request->input('json', null);
        $paramsArray = json_decode($json, true);

        // Validar los datos
        $validate = \Validator::make($paramsArray, [
            'name' => 'required',
            'description' => 'required'
        ]);
        if($validate->fails()){
            $this->apiTemplate['status'] = 'error';
            $this->apiTemplate['code'] = 400;
            $this->apiTemplate['message'] = 'Error al validar los datos';
            $this->apiTemplate['data'] = $validate->errors();
        }
        else{
            // Crear una nueva categoría
            $category = new Category();
            $category->name = $paramsArray['name'];
            $category->description = $paramsArray['description'];
            $category->save();
            $this->apiTemplate['data'] = $category;
            $this->apiTemplate['code'] = 201;
            $this->apiTemplate['message'] = 'La categoría se ha creado correctamente';
        }
        // Devolver la respuesta
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);

    }
    public function update($id, Request $request){
        $category = Category::find($id);
        if($category){
            $json = $request->input('json', null);
            $paramsArray = json_decode($json, true);
            $validate = \Validator::make($paramsArray, [
                'name' => 'required',
                'description' => 'required'
            ]);
            if($validate->fails()){
                $this->apiTemplate['status'] = 'error';
                $this->apiTemplate['code'] = 400;
                $this->apiTemplate['message'] = 'Error al validar los datos';
                $this->apiTemplate['data'] = $validate->errors();
            }
            else{
                $category->name = $paramsArray['name'];
                $category->description = $paramsArray['description'];
                $category->save();
                $this->apiTemplate['data'] = $category;
                $this->apiTemplate['code'] = 200;
                $this->apiTemplate['message'] = 'La categoría se ha actualizado correctamente';
            }
        }
        else{
            $this->apiTemplate['status'] = 'error';
            $this->apiTemplate['code'] = 404;
            $this->apiTemplate['message'] = 'La categoría no existe';
        }
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }
}
