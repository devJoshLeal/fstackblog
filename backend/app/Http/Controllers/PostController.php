<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;

class PostController extends Controller
{
    public $apiTemplate = array(
        'status'    => 'success',
        'code'      => 200,
        'message'   => ''
    );
    public $whoAmI = null;
    public function __construct(Request $request){
        $this->middleware('api.auth', ['except' =>
        ['index', 'show','getImage','postsByCategory','postsByUser']]);
        $this->middleware('api.checkparams', ['except' =>
        ['index', 'show','destroy','getImage','postsByCategory','postsByUser']]);
        $token = $request->header('Authorization',null);
        if(isset($token)){
            $jwtAuth = new \JwtAuth();
            // Consigue los datos del usuario segun el token
            $this->whoAmI = $jwtAuth->checkToken($token, true);
        }
    }

    public function index(){
        $posts = Post::all()->load('category')->load('user');
        $this->apiTemplate["data"] = $posts;
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }
    public function show($id){
        $post = Post::find($id)->load('category')->load('user');
        if($post){
            $this->apiTemplate["data"] = $post;
        }
        else{
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 404;
            $this->apiTemplate["message"] = "Publicacion no encontrada";
        }
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }
    public function store(Request $request){
        $json = $request->input('json', null);
        $paramsArray = json_decode($json, true);
        $validate = \Validator::make($paramsArray, [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);
        if($validate->fails()){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 400;
            $this->apiTemplate["message"] = "Error al validar los datos";
            $this->apiTemplate['data'] = $validate->errors();
        }
        else{
            $post = new Post();
            $post->title = $paramsArray["title"];
            $post->content = $paramsArray["content"];
            $post->category_id = $paramsArray["category_id"];
            $post->user_id = $this->whoAmI->sub;
            $post->save();
            $this->apiTemplate["data"] = $post;
            $this->apiTemplate["code"] = 201;
            $this->apiTemplate["message"] = "Publicacion creada con exito";
        }
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }
    public function update($id, Request $request){
        $post = Post::find($id);
        if(!$post){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 404;
            $this->apiTemplate["message"] = "No se encontro la publicacion";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        if($post->user_id != $this->whoAmI->sub){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 401;
            $this->apiTemplate["message"] = "No tienes permisos para editar esta publicacion";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        $json = $request->input('json', null);
        $paramsArray = json_decode($json, true);
        $validate = \Validator::make($paramsArray, [
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);
        if($validate->fails()){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 400;
            $this->apiTemplate["message"] = "Error al validar los datos";
            $this->apiTemplate['data'] = $validate->errors();
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        $post->title = $paramsArray["title"];
        $post->content = $paramsArray["content"];
        $post->category_id = $paramsArray["category_id"];
        $post->save();
        $this->apiTemplate["data"] = $post;
        $this->apiTemplate["code"] = 200;
        $this->apiTemplate["message"] = "Publicacion actualizada con exito";
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }
    public function destroy($id){
        $post = Post::find($id);
        if(!$post){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 404;
            $this->apiTemplate["message"] = "No se encontro la publicacion";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        if($post->user_id != $this->whoAmI->sub){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 401;
            $this->apiTemplate["message"] = "No tienes permisos para eliminar esta publicacion";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        $post->delete();
        $this->apiTemplate["code"] = 200;
        $this->apiTemplate["message"] = "Publicacion eliminada con exito";
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }

    public function upload(Request $request){
        // Busca el post que se le subira la imagen
        $json = $request->input('json', null);
        $paramsArray = json_decode($json, true);
        $validate = \Validator::make($paramsArray, [
            'id' => 'required|exists:posts,id',
        ]);
        if($validate->fails()){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 400;
            $this->apiTemplate["message"] = "Error al validar los datos";
            $this->apiTemplate['data'] = $validate->errors();
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        $post = Post::find($paramsArray["id"]);
        if(!$post){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 404;
            $this->apiTemplate["message"] = "No se encontro la publicacion";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        // Verifica si el usuario que sube la imagen es el mismo que el que creo la publicacion
        if($post->user_id != $this->whoAmI->sub){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 401;
            $this->apiTemplate["message"] = "No tienes permisos para subir una imagen a esta publicacion";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        // Recoger la imagen de la peticion
        $image = $request->file('file0');
        // Validar la imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif,webp, svg',
        ]);
        // Guardar la imagen
        if(!$image || $validate->fails()){
            $this->apiTemplate['code'] = 400;
            $this->apiTemplate['status'] = 'error';
            $this->apiTemplate['message'] =
            $validate->fails() ? "Error en la validación de datos" : "Error al subir la imagen";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        else{
            $imageName = time().$image->getClientOriginalName();
            \Storage::disk('posts')->put($imageName, \File::get($image));
            // Toma el nombre de la imagen y lo asigna a la publicacion en la base de datos
            $post->image = $imageName;
            $post->save();
            $this->apiTemplate['code'] = 200;
            $this->apiTemplate['status'] = 'success';
            $this->apiTemplate['message'] = "Imagen subida con exito";
            $this->apiTemplate['imageName'] = $imageName;

        }
        // Devolver la imagen
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);

    }
    public function getImage($imageName){
        $onStore = \Storage::disk('posts')->exists($imageName);
        if(!$onStore){
            $this->apiTemplate["status"] = 'error';
            $this->apiTemplate["code"] = 404;
            $this->apiTemplate["message"] = "No se encontro la imagen";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        $file = \Storage::disk('posts')->get($imageName);
        return new Response($file, 200);

    }

    public function postsByCategory($id){
        $posts = Post::where('category_id', $id)->get()->load('category')->load('user');
        if( count($posts) == 0){
            $this->apiTemplate['code'] = 404;
            $this->apiTemplate['status'] = 'error';
            $this->apiTemplate['message'] = "No hay posts en esta categoria";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        $this->apiTemplate['code'] = 200;
        $this->apiTemplate['status'] = 'success';
        $this->apiTemplate['message'] = "Posts por categoria";
        $this->apiTemplate['posts'] = $posts;
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);

    }
    public function postsByUser($id){
        $posts = Post::where('user_id', $id)->get()->load('category')->load('user');
        if( count($posts) == 0){
            $this->apiTemplate['code'] = 404;
            $this->apiTemplate['status'] = 'error';
            $this->apiTemplate['message'] = "No hay posts de este usuario";
            return response()->json($this->apiTemplate, $this->apiTemplate['code']);
        }
        $this->apiTemplate['code'] = 200;
        $this->apiTemplate['status'] = 'success';
        $this->apiTemplate['message'] = "Posts por usuario";
        $this->apiTemplate['posts'] = $posts;
        return response()->json($this->apiTemplate, $this->apiTemplate['code']);
    }
}
