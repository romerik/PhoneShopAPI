<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PhoneShopResource;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = Type::all();

        return response([ 'types' => PhoneShopResource::collection($types), 'message' => 'Types retrieved successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'brand_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $brand = Brand::where('id', $data['brand_id'])->first();
        if(!$brand){
            return response(["Constraint_violation" => "The brand id you send doesn't fit any brand "]);
        }

        $type = Type::create($data);

        return response(['type' => new PhoneShopResource($type), 'message' => 'Type created successfully'], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $type = Type::where('id', $id)->first();
        if(!$type){
            return response(["Not_Found_Error" => "The type you are retrieving for don't exist"]);
        }
        
        $type["brand"] = $type->brand;
        $type["versions"] = $type->versions;
        return response(['type' => new PhoneShopResource($type), 'message' => 'Type retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $type = Type::where('id', $id)->first();

        if(!$type){
            return response(["Not_Found_Error" => "The type you want to update don't exist"]);
        }

        $type->update($request->all());

        return response(['type' => new PhoneShopResource($type), 'message' => 'Type update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = Type::where('id', $id)->first();

        if(!$type){
            return response(["Not_Found_Error" => "The type you want to delete don't exist"]);
        }

        $type->delete();

        return response(['message' => 'Type successfully deleted']);
    }
}
