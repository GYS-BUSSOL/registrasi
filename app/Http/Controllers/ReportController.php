<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use DB;

class ReportController extends Controller
{
    public function showReport()
    {
        // Ambil data user dari database
        $users = Report::orderBy('employee_id', 'asc')->get();

        // Kirim data ke view
        return view('report', ['users' => $users]);
    }
}
