<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Requirement;
use App\Http\Resources\RequirementResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class RequirementController extends Controller
{
    //
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'module_id'      => 'required',
            'req_module_id'      => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $module_id = DB::table('modules')->selectRaw('COUNT(*)')->where('id', '=', $request->module_id)->count();
        $req_module_id = DB::table('modules')->selectRaw('COUNT(*)')->where('id', '=', $request->req_module_id)->count();

        $mod = [
            'module_id'     => $request->module_id,
                'req_module_id'   => $request->req_module_id,
        ];

        if($req_module_id==0 || $module_id==0){
            return new RequirementResource(false, 'No Module found', $mod);
        }
        else{
            $modules = Requirement::create([
                'module_id'     => $request->module_id,
                'req_module_id'   => $request->req_module_id,
            ]);
            return new RequirementResource(true, 'New Requirement added', $modules);
        }
    }

    public function show($id){
        $module = DB::table('requirements')->where('module_id', '=', $id)->get();

        if($module->isEmpty()){
            return new RequirementResource(false, 'No Requirement found', $module);
        }
        else{
            return new RequirementResource(true, 'Requirement found', $module);
        }
    }
}
