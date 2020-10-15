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
        $number_people = null;

        try {
            $buyer = Buyer::where('id', '=', $request->get('id_buyer'))->first();
            $concert = Concert::where('id', '=', $request->get('id_concert'))->first();

            $validator = Validator::make($request->all(), [
                'id_buyer' => 'required',
                'id_concert' => 'required',
                'date' => 'required|date',
                'number_people' => 'required|numeric',
                'status' => 'required|in:reservada,pagada,cancelada'
            ]);

            $capacity = $concert->max_number_people;
            $registered_persons = $concert->total_persons;
            $number_people = (int)$request->get('number_people');
            $total = $registered_persons+(int)$request->get('number_people');
            $places_available = $capacity - $registered_persons;

            if($validator->fails()){
                $message = $this->sendError('Error de validación', [$validator->errors()], 422);
            }elseif($buyer === null || $concert === null){
                $message = $this->sendError('Error en la consulta', ['El comprador o concierto no existen, valida nuevamente'], 422);
            }elseif($number_people > $capacity || $total > $capacity || $number_people == 0){
                $message = $this->sendError('Error al registrar la reserva', ['Capacidad total aforo: '.$capacity.' - Cupos disponibles: '.$places_available], 422);
            }else{
                $reservation = new Reservation();
                $reservation->id_buyer = $request->get('id_buyer');
                $reservation->id_concert = $request->get('id_concert');
                $reservation->date = $request->get('date');
                $reservation->ticket_number = rand(0, 100000000);
                $reservation->status = $request->get('status');

                //Guarda la reserva
                $reservation->number_people = $request->get('number_people');
                $reservation->save();

                //Guarda el total de las personas en el concierto
                $concert->total_persons = $total;
                $concert->save();

                $message = $this->sendResponse($reservation, 'La reserva se ha registrado correctamente');
            }
        } catch (\Throwable $e) {
                $message =  $this->sendError($e->getMessage(), ['Error en la consulta'],  422);
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
            'status' => 'required|in:reservada,pagada,cancelada'
        ]);

        if($validator->fails()){
            $message = $this->sendError('Error en la validación', [$validator->errors()], 422);
        }elseif($reservation === null){
            $message = $this->sendError('Error en la consulta', ['La reservación no existe'], 422);
        }else{
            $concert = Concert::where('id', '=', $reservation->id_concert)->first();
            $reservation_status = $request->get('status');

            if($reservation_status === "cancelada"){
                $concert->total_persons -= $reservation->number_people;
                $concert->save();
            }

            $reservation->id_buyer = $request->get('id_buyer');
            $reservation->id_concert = $request->get('id_concert');
            $reservation->status = $reservation_status;
            $reservation->save();

            $message = $this->sendResponse($reservation->number_people, 'Reserva actualizada correctamente');
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
            $id_concert = $reservation->id_concert;
            $number_people = $reservation->number_people;

            $concert = Concert::where('id', '=', $id_concert)->first();
            $registered_persons = $concert->total_persons;
            $total = $registered_persons-$number_people;

            $concert->total_persons = $total;
            $concert->save();

            $reservation->delete();
            $message = $this->sendResponse($reservation, 'Reserva eliminada correctamente');
        }

        return $message;
    }
}
