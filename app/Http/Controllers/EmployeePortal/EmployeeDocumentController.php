<?php

namespace App\Http\Controllers\EmployeePortal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeDocumentController extends Controller
{
    public function index()
    {
        return redirect()->route('employee.dashboard');
    }

    public function download($type)
    {
        return redirect()->route('employee.dashboard');
    }
}
