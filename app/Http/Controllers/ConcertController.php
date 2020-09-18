<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConcertController extends ResponseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message = null;

        $concerts = Concert::all();

        if($concerts->isEmpty()){
            $message = $this->sendError('Error en la consulta', ['No hay registros de conciertos'], 422);
        }else{
            $message = $this->sendResponse($concerts, 'Conciertos consultados correctamente');
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
            'description' => 'required',
            'date'  => 'required|date_format:Y-m-d H:i:s',
            'city'  => 'required',
            'place' => 'required',
            'max_number_people' => 'required'
        ]);

        if($validator->fails()){
            $message = $this->sendError('Error de validación', [$validator->errors()], 422);
        }else{
            $concert = new Concert();
            $concert->description = $request->get('description');
            $concert->date = $request->get('date');
            $concert->city = $request->get('city');
            $concert->place = $request->get('place');
            $concert->max_number_people = $request->get('max_number_people');
            $concert->save();

            $message = $this->sendResponse($concert, 'Concierto agregado correctamente');
        }

        return $message;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = null;

        $concert = Concert::find($id);

        if($concert === null){
            $message = $this->sendError('Error en la consulta', ['Registro no encontrado'], 422);
        }else{
            $message = $this->sendResponse($concert, 'Consulta exitosa');
        }

        return $message;

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = null;

        $concert = Concert::find($id);

        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'city' => 'required',
            'place' => 'required',
            'max_number_people' => 'required'
        ]);

        if($concert === null){
            $message = $this->sendError('Error en la consulta', ['Registro no encontrado']);
        }elseif($validator->fails()){
            $message = $this->sendError('Error de validación', [$validator->errors()], 422);
        }else{
            $concert->description = $request->get('description');
            $concert->date = $request->get('date');
            $concert->city = $request->get('city');
            $concert->place = $request->get('place');
            $concert->max_number_people = $request->get('max_number_people');
            $concert->save();

            $message = $this->sendResponse($concert, 'Concierto actualizado correctamente');
        }

        return $message;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = null;

        $concert = Concert::find($id);

        if($concert === null){
            $message = $this->sendError('Error en la consulta', ['No se encontro el registro'], 422);
        }else{
            $concert->delete();
            $message = $this->sendResponse($concert, 'Concierto eliminado correctamente');
        }

        return $message;
    }
}
