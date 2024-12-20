<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ScanController extends Controller
{

    public function showRegister()
    {
        if (Gate::denies('access-admin-or-hr')) {
            abort(403, 'Anda tidak memiliki akses admin atau HR.');
        }
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

        if (!empty($employee)) { // data karyawan ada
            $employee = $employee[0];
            if (!empty($employeeExist)) { // ada di trn_registration 
                return response()->json([
                    'success' => false,
                    'data' => [
                        'message' => 'Karyawan sudah registrasi.',
                        'nama' => $employee->full_name,
                        'department' => $employee->department_name,
                    ],
                ]);
            } else { // tidak ada di trn_registration
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $employee->employee_id,
                        'nama' => $employee->full_name,
                        'department' => $employee->department_name,
                        'size' => $employee->size,
                        'group' => $employee->group,
                        'warna_group' => $employee->warna_group,
                    ],
                ]);
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
        $employeeExist = DB::select('SELECT * FROM trn_registration WHERE employee_id = ?', [$request->employee_id]);

        if (empty($employeeExist)) {
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
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan sudah Registrasi',
            ]);
        }
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
        if (Gate::denies('access-admin-or-hr')) {
            abort(403, 'Anda tidak memiliki akses admin atau HR.');
        }
        return view('report', ['users' => $users]);
    }
}
