<?php

namespace App\Api\V1\Controllers;

use JWTAuth;
use Validator;
use Config;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\ValidationHttpException;


class UserController extends Controller
{
    use Helpers;

    public function index()	{
	    return User::all();
	}

	public function store(Request $request)
    {
    	$password = str_random(8);
    	$request->merge(array('password' => $password));

        $signupFields = Config::get('boilerplate.signup_fields');

        $userData = $request->only($signupFields);

        $validator = Validator::make($userData, Config::get('boilerplate.signup_fields_rules'));

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        User::unguard();
        $user = User::create($userData);
        User::reguard();

        if(!$user->id) {
            return $this->response->error('could_not_create_user', 500);
        }

        $data = ['email' => $userData['email'], 'password' => $password, 'link' => 'http://escalador.sagg.com.br'];
        \Mail::send('user_created', $data, function ($message) use ($data) {
     
            $message->from('no-reply@sagg.com.br', 'Escalador SAGG');
     
            $message->to($data['email'])->subject('[Escalador SAGG | Cadastro realizado');
     
        });

        return $this->response->created();
    }

    public function destroy($id) {
        $user = User::find($id);

        if(!$user) {
            throw new NotFoundHttpException;
        }

        $currentUser = JWTAuth::parseToken()->authenticate();
        if($currentUser->id == $id) {
            return $this->response->error('cant_destroy_yourself', 500);
        }

        if($user->delete()) {
            return $this->response->noContent();
        }
        else {
            return $this->response->error('error_destroy_user', 500);
        }
    }
}