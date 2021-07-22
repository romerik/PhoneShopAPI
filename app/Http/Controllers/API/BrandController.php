<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PhoneShopResource;
use App\Http\Requests\BrandRequest;


class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::all();

        return response([ 'brands' => PhoneShopResource::collection($brands), 'message' => 'Brands retrieved successfully'], 200);
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
            'name' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $result = Brand::where('name', $data['name'])->first();

        if($result){
            return response(['Duplicate_Error' => "This brands already exists"]);
        }

        $brand = Brand::create($data);

        return response(['brand' => new PhoneShopResource($brand), 'message' => 'Brand created successfully'], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
        $brand = Brand::where('id', $id)->first();
        if(!$brand){
            return response(["Not_Found_Error" => "The brand you are retrieving for don't exist"]);
        }
        $brand['types'] = $brand->types;
        return response(['brand' => new PhoneShopResource($brand), 'message' => 'Brand retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::where('id', $id)->first();

        if(!$brand){
            return response(["Not_Found_Error" => "The brand you want to update don't exist"]);
        }

        if($brand->name== $request->name){
            return response(['IntegretyConstraintError' => "A brand with the same name already exists, so can't still create a brand this name "]);
        }


        $brand->update($request->all());

        return response(['brand' => new PhoneShopResource($brand), 'message' => 'Brand update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::where('id', $id)->first();

        if(!$brand){
            return response(["Not_Found_Error" => "The brand you want to delete don't exist"]);
        }

        $brand->delete();

        return response(['message' => 'Brand successfully deleted']);
    }
}
