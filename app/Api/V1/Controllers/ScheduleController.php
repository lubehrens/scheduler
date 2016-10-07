<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Validator;
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
            return $this->response->error('error_begin_after_equal_end',500);
        }

        //if NOT get disponibilidade antes do inicio e depois do fim datas return
        $isAvailable = $currentUser
                        ->availabilities()
                        ->where('doctor_id',$schedule->doctor_id)
                        ->where('begin','<=',$schedule->begin)
                        ->where('end','=>',$schedule->end)
                        ->get()
                        ->toArray();
        if(!$isAvailable) {
            return $this->response->error('doctor_not_available',500);
        }

        //if get escala durante datas return
        $hasSchedule = $currentUser
                        ->schedules()
                        ->where('begin','<=',$schedule->begin)
                        ->where('end','=>',$schedule->end)
                        ->get()
                        ->toArray();
        if(!$isAvailable) {
            return $this->response->error('doctor_not_available',500);
        }

    	if($currentUser->schedules()->save($schedule)) {
    		return $this->response->created();
    	}
    	else {
    		return $this->response->error('error_create_schedule',500);
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
	        return $this->response->error('error_update_schedule', 500);
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
	        return $this->response->error('error_destroy_schedule', 500);
	    }
    }
}
