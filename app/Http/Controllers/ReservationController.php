<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Buyer;
use App\Models\Concert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends ResponseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message = null;

        $reservations = Reservation::select('reservations.id','buyers.name as buyer','concerts.description as name_concert','reservations.date','reservations.number_people','reservations.ticket_number','reservations.status')
                                    ->join('buyers', 'reservations.id_buyer', '=', 'buyers.id')
                                    ->join('concerts', 'reservations.id_concert', '=', 'concerts.id')
                                    ->get();

        if($reservations->isEmpty()){
            $message = $this->sendError('Error en la consulta', ['No hay reservaciones actualmente'], 422);
        }else{
            $message = $this->sendResponse($reservations, 'Datos consultados correctamente');
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
            'id_buyer' => 'required',
            'id_concert' => 'required',
            'date' => 'required|date_format:Y-m-d H:i:s',
            'number_people' => 'required|numeric',
            'status' => 'required|in:reservada,pagada,cancelada'
        ]);

        $buyer = Buyer::where('id', '=', $request->get('id_buyer'))->first();
        $concert = Concert::where('id', '=', $request->get('id_concert'))->first();

        if($validator->fails()){
            $message = $this->sendError('Error de validación', [$validator->errors()], 422);
        }elseif($buyer === null || $concert === null){
            $message = $this->sendError('Error en la consulta', ['El comprador o concierto no existen, valida nuevamente'], 422);
        }else{
            $reservation = new Reservation();
            $reservation->id_buyer = $request->get('id_buyer');
            $reservation->id_concert = $request->get('id_concert');
            $reservation->date = $request->get('date');
            $reservation->number_people = $request->get('number_people');
            $reservation->ticket_number = rand(0, 100000000);
            $reservation->status = $request->get('status');
            $reservation->save();

            $message = $this->sendResponse($reservation, 'La reserva se ha registrado correctamente');
        }

        return $message;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = null;

        $reservation = Reservation::select('reservations.id','buyers.name as buyer','concerts.description as name_concert','reservations.date','reservations.number_people','reservations.ticket_number','reservations.status')
                                    ->join('buyers', 'reservations.id_buyer', '=', 'buyers.id')
                                    ->join('concerts', 'reservations.id_concert', '=', 'concerts.id')
                                    ->where('reservations.id', '=', $id)
                                    ->first();

        if($reservation === null){
            $message = $this->sendError('Error en la consulta', ['La reservación no existe'], 422);
        }else{
            $message = $this->sendResponse($reservation, 'Consulta exitosa');
        }

        return $message;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = null;

        $reservation = Reservation::find($id);

        $validator = Validator::make($request->all(), [
            'id_buyer' => 'required',
            'id_concert' => 'required',
            'number_people' => 'required|numeric',
            'status' => 'required|in:reservada,pagada,cancelada'
        ]);

        if($validator->fails()){
            $message = $this->sendError('Error en la validación', [$validator->errors()], 422);
        }elseif($reservation === null){
            $message = $this->sendError('Error en la consulta', ['La reservación no existe'], 422);
        }else{
            $reservation->id_buyer = $request->get('id_buyer');
            $reservation->id_concert = $request->get('id_concert');
            $reservation->number_people = $request->get('number_people');
            $reservation->status = $request->get('status');
            $reservation->save();

            $message = $this->sendResponse($reservation, 'Reserva actualizada correctamente');
        }

        return $message;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = null;

        $reservation = Reservation::find($id);

        if($reservation === null){
            $message = $this->sendError('Error en la consulta', ['No se encontro el registro'], 422);
        }else{
            $reservation->delete();
            $message = $this->sendResponse($reservation, 'Reserva eliminada correctamente');
        }

        return $message;
    }
}
