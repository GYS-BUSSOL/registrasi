<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoorprizeController extends Controller
{

    public function index()
    {
        return view('doorprize');
    }

    public function getParticipants()
    {
        $participants = DB::table('mst_dbox_employee')->pluck('employee_id'); // Hanya ambil kolom employee_id
        if ($participants->isEmpty()) {
            return response()->json(['message' => 'Tidak ada peserta terdaftar.'], 404);
        }

        return response()->json($participants);
    }

    public function draw()
    {
        $participants = DB::select('SELECT employee_id, full_name, department_name FROM mst_dbox_employee');
        // $participants = DB::select('SELECT trn_registration.employee_id, mst_dbox_employee.full_name, mst_dbox_employee.department_name FROM trn_registration INNER JOIN mst_dbox_employee ON trn_registration.employee_id = mst_dbox_employee.employee_id')

        if (count($participants) > 0) {
            $winner = $participants[array_rand($participants)];
            return response()->json([
                'message' => 'Selamat Kepada:',
                'winner' => $winner
            ]);
        }

        return response()->json(['message' => 'Tidak ada peserta untuk diundi.'], 404);
    }
}
