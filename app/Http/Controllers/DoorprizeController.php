<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DoorprizeController extends Controller
{

    public function index()
    {
        if (Gate::denies('access-admin-or-operator')) {
            abort(403, 'Anda tidak memiliki akses Admin atau Operator.');
        }
        return view('doorprize');
    }

    public function getParticipants()
    {
        $employees = DB::select('SELECT employee_id FROM trn_registration WHERE employee_id NOT IN (SELECT employee_id FROM trn_undian)');
        $participants = collect($employees)->pluck('employee_id');

        if ($participants->isEmpty()) {
            return response()->json(['message' => 'Tidak ada peserta terdaftar.'], 404);
        }

        return response()->json($participants);
    }

    public function draw(Request $request)
    {
        $winner = DB::select('SELECT trn_registration.employee_id, tmp_employee_day.full_name, tmp_employee_day.department_name FROM trn_registration 
        INNER JOIN tmp_employee_day ON trn_registration.employee_id = tmp_employee_day.employee_id 
        WHERE trn_registration.employee_id = ?', [$request->employee_id]);

        $checkWinner = DB::select('SELECT * FROM trn_undian INNER JOIN tmp_employee_day ON trn_undian.employee_id = tmp_employee_day.employee_id WHERE trn_undian.employee_id = ?', [$request->employee_id]);


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

    public function report()
    {
        $users = DB::select('SELECT *, tmp_employee_day.full_name, tmp_employee_day.department_name  FROM trn_undian 
        INNER JOIN tmp_employee_day ON trn_undian.employee_id = tmp_employee_day.employee_id');
        if (Gate::denies('access-admin-or-hr')) {
            abort(403, 'Anda tidak memiliki akses admin atau HR.');
        }
        return view('reportUndian', ['users' => $users]);
    }
}
