<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Type;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PhoneShopResource;
use Illuminate\Support\Facades\Hash;


class VersionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $versions = Version::all();

        return response([ 'versions' => PhoneShopResource::collection($versions), 'message' => 'Versions retrieved successfully'], 200);
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
            'type_id' => 'required',
            'link' => 'required|url|max:255',
            'firmware_hash' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $type = Type::where('id', $data['type_id'])->first();
        if(!$type){
            return response(["Constraint_violation" => "The type id you send doesn't fit any type "]);
        }

        $data['firmware_hash'] = Hash::make($request->firmware_hash);

        $version = Version::create($data);

        return response(['version' => new PhoneShopResource($version), 'message' => 'Version created successfully'], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Version  $version
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $version = Version::where('id', $id)->first();
        if(!$version){
            return response(["Not_Found_Error" => "The version you are retrieving for don't exist"]);
        }

        $version["type"] = $version->type;
        $version["brand"] = $version->type->brand;
        return response(['version' => new PhoneShopResource($version), 'message' => 'Version retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Version  $version
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $version = Version::where('id', $id)->first();

        if(!$version){
            return response(["Not_Found_Error" => "The version you want to update don't exist"]);
        }

        $version->update($request->all());

        return response(['version' => new PhoneShopResource($version), 'message' => 'Version update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Version  $version
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $version = Version::where('id', $id)->first();

        if(!$version){
            return response(["Not_Found_Error" => "The version you want to delete don't exist"]);
        }

        $version->delete();

        return response(['message' => 'Version successfully deleted']);
    }
}