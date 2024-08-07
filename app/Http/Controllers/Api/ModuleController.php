<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Http\Resources\ModuleResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ModuleController extends Controller
{
    public function index()
    {
        //get modules
        $modules = Module::all();

        //return collection of modules as a resource
        return new ModuleResource(true, 'List of Modules', $modules);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'description'      => 'required',
            'file'     => 'mimes:xlsx,doc,docx,ppt,pptx,pdf',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        if($request->hasFile('file')){
            $modulefile = $request->file('file');
            $modulefile->storeAs('public/Modules', $modulefile->hashName());
            $moduleDb = $modulefile->hashName();
        }
        else{
            $moduleDb = "null";
        }


        //create module
        $modules = Module::create([
            'name'     => $request->name,
            'description'   => $request->description,
            'file'     => $moduleDb,
            'course_id' => $request->course_id
        ]);


        return new ModuleResource(true, 'New Module added', $modules);
    }

    public function show($id)
    {
        //find post by ID
        $module = Module::find($id);

        //return single post as a resource
        if($module==null){
            return new ModuleResource(false, 'Module not found', $module);
        }
        else{
            return new ModuleResource(true, 'Detail module', $module);
        }

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'description'      => 'required',
            'file'     => 'mimes:xlsx,doc,docx,ppt,pptx,pdf',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $module = Module::find($id);

        if(!empty($module)){
            //upload image
            if($request->hasFile('file')){
                $modulefile = $request->file('file');
                $modulefile->storeAs('public/Modules', $modulefile->hashName());
                $moduleDb = $modulefile->hashName();
            }
            else{
                $moduleDb = "null";
            }
            $request->merge(['file' => $moduleDb]);

            $module->update([
                'name' => $request->name,
                'description' => $request->description,
                'file' => $moduleDb,
                'course_id' => $request->course_id
            ]);
        }
        elseif(empty($module)){
            return new ModuleResource(false, 'Module Not Found', $module);
        }

        return new ModuleResource(true, 'Updated Module', $module);

    }
}
