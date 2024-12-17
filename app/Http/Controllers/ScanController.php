<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{

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
        $employee_id = $request->employee_id;
        if (strlen($request->employee_id) > 9) {
            $employee_id = substr($request->employee_id, 1);
        }
        $employee = DB::select('EXEC [dbo].[sp_checkEmployee] ?, ?', [$request->scan, $employee_id]);

        $employeeExist = DB::select('SELECT * FROM trn_registration WHERE employee_id = ?', [$employee_id]);

        // cek data karyawan ada atau tidak
        if (!empty($employee)) {
            $employee = $employee[0];
            // cek apakah data karyawan sudah ada di tabel trn_registration
            if (!empty($employeeExist)) { // ada di trn_registration 
                $employeeExist = $employeeExist[0];
                if ($employeeExist->is_flag == 1 && $request->scan == 2) {
                    // scan lunch berhasil
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'id' => $employee->employee_id,
                            'nama' => $employee->full_name,
                            'department' => $employee->department_name,
                        ],
                    ]);
                } else if ($employeeExist->is_flag == 1 || $employeeExist->is_flag == 2 && $request->scan == 1) {
                    return response()->json([
                        'success' => false,
                        'data' => [
                            'message' => 'Karyawan sudah registrasi.',
                            'nama' => $employee->full_name,
                            'department' => $employee->department_name,
                        ],
                    ]);
                } else if ($employeeExist->is_flag == 2 && $request->scan == 2) {
                    return response()->json([
                        'success' => false,
                        'data' => [
                            'message' => 'Karyawan sudah scan lunch.',
                            'nama' => $employee->full_name,
                            'department' => $employee->department_name,
                        ],
                    ]);
                }
            } else {
                if ($request->scan == 1) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'id' => $employee->employee_id,
                            'nama' => $employee->full_name,
                            'department' => $employee->department_name,
                            'size' => $employee->size,
                        ],
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'data' => [
                            'message' => 'Karyawan belum registrasi.',
                            'nama' => $employee->full_name,
                            'department' => $employee->department_name,
                        ],
                    ]);
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'data' => [
                    'message' => 'Data karyawan tidak ditemukan.',
                ],
            ]);
        }
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
        $users = DB::select('EXEC [dbo].[sp_ListEmployeeDay]');
        // dd($users);
        return view('report', ['users' => $users]);
    }
}
