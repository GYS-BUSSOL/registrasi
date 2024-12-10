<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    /**
     * EXEC [dbo].[sp_checkEmployee] '1', '100212001' = sp buat cek hasil scan, ada 2 parameter, 
     * yg pertama buat liat dia scan register atau lunch, yang kedua itu nomor karyawannya.
     * nah nanti, setelah dapet, hasilnya tampilin di popup, data karyawannya, buat konfirmasi
     
     * trn_registration = kalau user scan buat register = 1, kalau user scan lagi buat lunch update jadi 2
     
     * his_registration = masukin semua data, mau itu scan buat register maupun scan buat lunch
     */

    public function showRegister()
    {
        return view('register');
    }

    public function showLunch()
    {
        return view('lunch');
    }

    public function checkEmployee(Request $request)
    {
        $employee = DB::select('EXEC [dbo].[sp_checkEmployee] ?, ?', [$request->scan, $request->employee_id]);

        if (!empty($employee)) {
            $employee = $employee[0];
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $employee->employee_id,
                    'nama' => $employee->full_name,
                    'department' => $employee->department_name,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Karyawan tidak ditemukan.',
        ]);
    }

    public function scanRegister(Request $request)
    {
        DB::insert('INSERT INTO trn_registration (employee_id, is_flag, created_at, created_by, updated_at, updated_by) VALUES (?, ?, ?, ?, ?, ?)', [
            $request->employee_id,
            1,
            now()->format('Y-m-d H:i:s'),
            session('id'),
            now()->format('Y-m-d H:i:s'),
            session('id'),
        ]);

        DB::insert('INSERT INTO his_registration (employee_id, is_flag, created_at, created_by) VALUES (?, ?, ?, ?)', [
            $request->employee_id,
            1,
            now()->format('Y-m-d H:i:s'),
            session('id'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Registrasi Berhasil',
        ]);
        // return redirect()->route('register')->with('success', true)->with('msg', 'Registrasi Berhasil');
    }
    public function scanLunch(Request $request)
    {
        DB::insert('UPDATE trn_registration SET is_flag = ? , updated_at = ?, updated_by = ?  WHERE employee_id = ?', [
            2,
            now()->format('Y-m-d H:i:s'),
            session('id'),
            $request->employee_id,
        ]);

        DB::insert('INSERT INTO his_registration (employee_id, is_flag, created_at, created_by) VALUES (?, ?, ?, ?)', [
            $request->employee_id,
            2,
            now()->format('Y-m-d H:i:s'),
            session('id'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Scan Lunch Berhasil',
        ]);
    }
    public function report()
    {
        $users = DB::select('SELECT *, mst_dbox_employee.full_name, mst_dbox_employee.department_name FROM trn_registration INNER JOIN mst_dbox_employee ON trn_registration.employee_id = mst_dbox_employee.employee_id');
        // dd($users);
        return view('report', ['users' => $users]);
    }
}
