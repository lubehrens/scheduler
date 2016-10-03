<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\Availability;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AvailabilityController extends Controller
{
    use Helpers;

    public function index() {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	return $currentUser
    		->availabilities()
    		->orderBy('created_at','DESC')
    		->get()
    		->toArray();
    }

    public function store(Request $request) {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	$availability = new Availability;

    	$availability->begin = $request->get('begin');
    	$availability->end = $request->get('end');

    	if($currentUser->availabilities()->save($availability)) {
    		return $this->response->created();
    	}
    	else {
    		return $this->response->error('erro_criar_disponibilidade',500);
    	}
    }

    public function show($id) {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	$availability = $currentUser->availabilities()->find($id);

    	if(!$availability) {
    		throw new NotFoundHttpException;
    	}
    	return $availability;
    }

    public function update(Request $request, $id) {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	$availability = $currentUser->availabilities()->find($id);

    	if(!$availability) {
    		throw new NotFoundHttpException;
    	}

    	$availability->fill($request->all());

	    if($availability->save()) {
	        return $this->response->noContent();
	    }
	    else {
	        return $this->response->error('erro_atualizar_disponibilidade', 500);
	    }
	}

    public function destroy($id) {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	$availability = $currentUser->availabilities()->find($id);

    	if(!$availability) {
    		throw new NotFoundHttpException;
    	}

	    if($availability->delete()) {
	        return $this->response->noContent();
	    }
	    else {
	        return $this->response->error('erro_excluir_disponibilidade', 500);
	    }
    }
}
