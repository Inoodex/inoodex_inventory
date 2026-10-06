<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaDa;
use Illuminate\Support\Facades\Auth;

class EmployeeTaDaController extends Controller
{
    // public function index()
    // {
    //     $tadas = TaDa::where('user_id', Auth::id())->latest()->get();

    //     return view('frontend.pages.employees.ta_da.index', compact('tadas'));
    // }

    public function index()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked to your user account.');
        }

        $tadas = TaDa::where('employee_id', $employee->id)->latest('date')->get();

        return view('frontend.pages.employees.ta_da.index', compact('tadas'));
    }

    public function create()
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked to your user account.');
        }

        return view('frontend.pages.employees.ta_da.create');
    }

    public function store(Request $request)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked to your user account.');
        }

        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:TA,DA',
            'payment_type' => 'required|in:Advance,Claim',
            'purpose' => 'nullable|string|max:1000',
        ]);

        TaDa::create([
            'user_id' => Auth::id(),
            'employee_id' => $employee->id,
            'date' => $request->date,
            'amount' => $request->amount,
            'type' => $request->type,
            'payment_type' => $request->payment_type,
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('employee.tada.index')->with('success', 'TA/DA request submitted successfully.');
    }

    public function edit($id)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked to your user account.');
        }

        $tadas = TaDa::where('id', $id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        return view('frontend.pages.employees.ta_da.edit', compact('tadas'));
    }

    public function update(Request $request, $id)
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->route('index')->with('error', 'No employee profile linked to your user account.');
        }

        $tadas = TaDa::where('id', $id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $request->validate([
            'used_amount' => 'required|numeric|min:0|max:' . $tadas->amount,
        ]);

        $tadas->used_amount = $request->used_amount;
        $tadas->remaining_amount = max(0, $tadas->amount - $tadas->used_amount);
        $tadas->save();

        return redirect()->route('employee.tada.index')->with('success', 'Amount submitted successfully.');
    }
}