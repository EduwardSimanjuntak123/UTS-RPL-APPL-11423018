<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $appointment = Appointment::create($request->all());
        return response()->json($appointment);
    }

    public function index()
    {
        return Appointment::with(['patient','doctor'])->get();
    }
}