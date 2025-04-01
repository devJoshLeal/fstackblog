<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth {
    public $key;

    public function __construct() {
        $this->key = config('app.jwt_secret');
    }

    public function signup($email,$password, $getToken = false) {
        // Buscar si existe el usuario con sus credenciales
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();


        // Comprobar si son correctas las credenciales
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }


        // Generar el token
        if ($signup) {
            $token = array(
                "sub"       => $user->id,
                "email"     => $user->email,
                "name"      =>  $user->name,
                "surname"   => $user->surname,
                "iat"       => time(),
                "exp"       => time() + (7 * 24 * 60 * 60)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            // Devolver los datos del usuario o el token
            if (!$getToken) {
                $data = array(
                    "status"    => "success",
                    "message"   => "Login correcto",
                    "code"      => 200,
                    "data"      => $decoded
                );

            }
            else {
                $data = array(
                    "status"    => "success",
                    "message"   => "Login correcto",
                    "code"      => 200,
                    "token"     => $jwt
                );
            }
        }
        else{
            $userExists = User::where('email', $email)->first();
            if($userExists){
                $data = array (
                    "status"    => "error",
                    "code"      => 400,
                    "message"   => "Credenciales incorrectas"
                );
            }
            else{
                $data = array (
                    "status"    => "error",
                    "code"      => 400,
                    "message"   => "El usuario no existe"
                );
            }
        }
        return response()->json($data, $data["code"]);

    }
    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;
        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        } catch (\DomainException $e) {
            // Captura específicamente DomainException
            $auth = false;
        } catch (\Exception $e) {
            // Captura cualquier otra excepción
            $auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;
    }
}
