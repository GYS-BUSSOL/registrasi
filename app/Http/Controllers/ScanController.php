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

        // kalau data karyawan ada
        if (!empty($employee)) {
            $employee = $employee[0];
            // cek user scan dihalaman registrasi atau lunch
            if ($request->scan == 1) {
                // cek karyawan sudah ada di tabel trn_registrasi atau belum
                if (empty($employeeExist)) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'id' => $employee->employee_id,
                            'nama' => $employee->full_name,
                            'department' => $employee->department_name,
                        ],
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'data' => [
                            'message' => 'Karyawan sudah registrasi.',
                            'nama' => $employee->full_name,
                            'department' => $employee->department_name,
                        ],
                    ]);
                }
            } else {
                // scan di halaman lunch
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $employee->employee_id,
                        'nama' => $employee->full_name,
                        'department' => $employee->department_name,
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

        // // data karyawan ada, karyawan belum terdaftar di trn_registration, scan di halaman registrasi
        // if (!empty($employee) && empty($employeeExist) && $request->scan == 1) {
        //     $employee = $employee[0];
        //     return response()->json([
        //         'success' => true,
        //         'data' => [
        //             'id' => $employee->employee_id,
        //             'nama' => $employee->full_name,
        //             'department' => $employee->department_name,
        //         ],
        //     ]);
        // }
        // // data karyawan tidak ada, karyawan sudah terdaftar di trn_registration, scan di halaman registrasi
        // else if (!empty($employee) && !empty($employeeExist) && $request->scan == 1) {
        //     $employee = $employee[0];
        //     return response()->json([
        //         'success' => false,
        //         'data' => [
        //             'message' => 'Karyawan sudah registrasi.',
        //             'nama' => $employee->full_name,
        //             'department' => $employee->department_name,
        //         ],
        //     ]);
        // }
        // // data karyawan ada, scan di halaman lunch
        // else if (!empty($employee) && $request->scan == 1) {
        //     $employee = $employee[0];
        //     return response()->json([
        //         'success' => true,
        //         'data' => [
        //             'id' => $employee->employee_id,
        //             'nama' => $employee->full_name,
        //             'department' => $employee->department_name,
        //         ],
        //     ]);
        // }

        // return response()->json([
        //     'success' => false,
        //     'data' => [
        //         'message' => 'Data karyawan tidak ditemukan.',
        //     ],
        // ]);
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
