<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use App\Schedule;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScheduleController extends Controller
{
    use Helpers;

    public function index() {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	return $currentUser
    		->schedules()
    		->orderBy('created_at','DESC')
    		->get()
    		->toArray();
    }

    public function store(Request $request) {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	$schedule = new Schedule;

    	$schedule->begin = $request->get('begin');
    	$schedule->end = $request->get('end');
    	$schedule->doctor_id = $request->get('doctor_id');

        if($schedule->begin >= $schedule->end) {
            return $this->response->error('inicio_maior_igual_fim',500);
        }

    	if($currentUser->schedules()->save($schedule)) {
    		return $this->response->created();
    	}
    	else {
    		return $this->response->error('erro_criar_escala',500);
    	}
    }

    public function show($id) {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	$schedule = $currentUser->schedules()->find($id);

    	if(!$schedule) {
    		throw new NotFoundHttpException;
    	}
    	return $schedule;
    }

    public function update(Request $request, $id) {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	$schedule = $currentUser->schedules()->find($id);

    	if(!$schedule) {
    		throw new NotFoundHttpException;
    	}

    	$schedule->fill($request->all());

	    if($schedule->save()) {
	        return $this->response->noContent();
	    }
	    else {
	        return $this->response->error('erro_atualizar_escala', 500);
	    }
    }

    public function destroy($id) {
    	$currentUser = JWTAuth::parseToken()->authenticate();

    	$schedule = $currentUser->schedules()->find($id);

    	if(!$schedule) {
    		throw new NotFoundHttpException;
    	}

	    if($schedule->delete()) {
	        return $this->response->noContent();
	    }
	    else {
	        return $this->response->error('erro_excluir_escala', 500);
	    }
    }
}
