<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ReadModule;
use App\Http\Resources\ReadModuleResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ReadModuleController extends Controller
{
    //

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required',
            'module_id'      => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user_id = DB::table('users')->selectRaw('COUNT(*)')->where('id', '=', $request->user_id)->count();
        $module_id = DB::table('modules')->selectRaw('COUNT(*)')->where('id', '=', $request->module_id)->count();

        $mod = [
            'user_id' => $request->user_id,
            'module_id' => $request->module_id,
        ];

        if($user_id==0 || $module_id==0){
            return new ReadModuleResource(false, 'No user or Module found', $mod);
        }
        else{
            $modules = ReadModule::create([
                'user_id'     => $request->user_id,
                'module_id'   => $request->module_id,
            ]);
            return new ReadModuleResource(true, 'New ReadModule added', $modules);
        }





    }
}
