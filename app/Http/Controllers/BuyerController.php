<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuyerController extends ResponseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message = null;

        $buyers = Buyer::all();

        if($buyers->isEmpty()){
            $message = $this->sendError('No hay datos', ['No hay clientes registrados'], 422);
        }else{
            $message = $this->sendResponse($buyers, 'Datos recuperados correctamente');
        }

        return $message;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = null;

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'identification' => 'required|numeric|unique:buyers',
            'telephone' => 'required|numeric'
        ]);

        if($validator->fails()){
            $message = $this->sendError('Error de validación', [$validator->errors()], 422);
        }else{
            $buyer = new Buyer();
            $buyer->name = $request->get('name');
            $buyer->surname = $request->get('surname');
            $buyer->identification = $request->get('identification');
            $buyer->telephone = $request->get('telephone');
            $buyer->save();

            $message = $this->sendResponse($buyer, 'Cliente registrado correctamente');
        }

        return $message;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Buyer  $buyer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = null;

        $buyer = Buyer::find($id);

        if($buyer === null){
            $message = $this->sendError('Error en la consulta', ['El cliente no existe'], 422);
        }else{
            $message = $this->sendResponse($buyer, 'Cliente encontrado correctamente');
        }

        return $message;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Buyer  $buyer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = null;

        $buyer = Buyer::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'identification' => 'required|numeric',
            'telephone' => 'required|numeric'
        ]);

        if($buyer === null){
            $message = $this->sendError('Error al actualizar el registro', ['Cliente no encontrado'], 422);
        }elseif ($validator->fails()) {
            $message = $this->sendError('Error de validación', [$validator->errors()], 422);
        }else{
            $buyer->name = $request->get('name');
            $buyer->surname = $request->get('surname');
            $buyer->identification = $request->get('identification');
            $buyer->telephone = $request->get('telephone');
            $buyer->save();

            $message = $this->sendResponse($buyer, 'Cliente actualizado correctamente');
        }

        return $message;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Buyer  $buyer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = null;

        $buyer = Buyer::find($id);

        if($buyer === null){
            $message = $this->sendError('Error en la consulta', ['No se encontro el registro'], 422);
        }else{
            $buyer->delete();
            $message = $this->sendResponse($buyer, 'Cliente eliminado correctamente');
        }

        return $message;
    }
}
