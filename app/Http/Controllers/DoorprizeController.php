<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Type\TrueType;

class DoorprizeController extends Controller
{

    public function index()
    {
        return view('doorprize');
    }

    public function getParticipants()
    {
        // $participants = DB::table('mst_dbox_employee')->pluck('employee_id'); // Sample data
        $employees = DB::select('SELECT employee_id FROM trn_registration WHERE employee_id NOT IN (SELECT employee_id FROM trn_undian)');
        $participants = collect($employees)->pluck('employee_id');

        if ($participants->isEmpty()) {
            return response()->json(['message' => 'Tidak ada peserta terdaftar.'], 404);
        }

        return response()->json($participants);
    }

    public function draw(Request $request)
    {
        // $winner = DB::select('SELECT employee_id, full_name, department_name FROM mst_dbox_employee WHERE employee_id = ?', [$request->employee_id]); // sample data
        $winner = DB::select('SELECT trn_registration.employee_id, mst_dbox_employee.full_name, mst_dbox_employee.department_name FROM trn_registration INNER JOIN mst_dbox_employee ON trn_registration.employee_id = mst_dbox_employee.employee_id WHERE trn_registration.employee_id = ?', [$request->employee_id]);

        $checkWinner = DB::select('SELECT * FROM trn_undian INNER JOIN mst_dbox_employee ON trn_undian.employee_id = mst_dbox_employee.employee_id WHERE trn_undian.employee_id = ?', [$request->employee_id]);


        if (!empty($checkWinner)) {
            return response()->json([
                'success' => false,
                'data' => [
                    'message' => 'Pemenang sudah ada',
                    'winner' => $checkWinner[0]
                ],
            ]);
        }
        DB::insert('INSERT INTO trn_undian (employee_id, created_at, created_by) VALUES (?,?,?)', [$request->employee_id, now()->format('Y-m-d H:i:s'), session('id')]);
        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => 'Selamat Kepada:',
            'winner' => $winner[0],
        ]);
    }
}
