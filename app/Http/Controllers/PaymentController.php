<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;

class PaymentController extends Controller
{
    public function payments(Request $request, $id, $payment_for){
        $payments = Payment::where('sale_id',$id)->where('payment_for', $payment_for)->get();
        
        // Get the bill (service or sale) information
        $bill = null;
        if($payment_for == '1'){
            $bill = Service::where('id', $id)->first();
        }
        if($payment_for == '2'){
            $bill = Sale::where('id', $id)->first();
        }
        
        return view('frontend.pages.payment.bill_payment', compact('payments','id', 'payment_for', 'bill'));
    }

    public function addPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_for'       => 'required|in:1,2',
            'id'                => 'required|integer',
            'amount'            => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|string',
            'remarks'           => 'nullable|string|max:500',
        ]);

        $paymentFor = (string)$request->payment_for;
        $bill = $paymentFor === '1'
            ? Service::find($request->id)
            : Sale::find($request->id);

        if (!$bill) {
            return redirect()->back()->with('error', 'Bill record not found. Please try again.');
        }

        $amount = (float)$request->amount;
        $currentDue = $paymentFor === '1' ? (float)($bill->due_amount ?? 0) : (float)($bill->due_payment ?? 0);

        if ($currentDue > 0 && $amount > $currentDue) {
            return redirect()->back()->with('error', 'Payment amount cannot exceed the outstanding due of ৳' . number_format($currentDue, 2));
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $bill, $paymentFor, $amount) {
            $payment = new Payment;
            $payment->payment_for = (int)$paymentFor;
            $payment->customer_id = $bill->customer_id ?? 0;
            $payment->sale_id = $bill->id;
            $payment->payment_method = $request->payment_method_id;
            $payment->amount = $amount;
            $payment->remarks = $request->remarks;
            $payment->save();

            if ($paymentFor === '1') {
                $bill->paid_amount = (float)($bill->paid_amount ?? 0) + $amount;
                $bill->due_amount = max(0, (float)($bill->bill ?? 0) - $bill->paid_amount);
                $bill->save();
            } else {
                $bill->advanced_payment = (float)($bill->advanced_payment ?? 0) + $amount;
                $bill->due_payment = max(0, (float)($bill->payble ?? 0) - $bill->advanced_payment);
                $bill->save();
            }
        });

        return redirect()->back()->with('success', 'Payment added successfully.');
    }

    public function updatePayment(Request $request, $id)
    {
        $validated = $request->validate([
            'amount'            => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|string',
            'remarks'           => 'nullable|string|max:500',
        ]);

        $payment = Payment::find($id);
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found.');
        }

        $paymentFor = (string)$payment->payment_for;
        $bill = $paymentFor === '1'
            ? Service::find($payment->sale_id)
            : Sale::find($payment->sale_id);

        if (!$bill) {
            return redirect()->back()->with('error', 'Linked bill record not found.');
        }

        $newAmount = (float)$request->amount;
        $oldAmount = (float)$payment->amount;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $payment, $bill, $paymentFor, $newAmount, $oldAmount) {
            if ($paymentFor === '1') {
                $bill->paid_amount = max(0, (float)($bill->paid_amount ?? 0) - $oldAmount + $newAmount);
                $bill->due_amount = max(0, (float)($bill->bill ?? 0) - $bill->paid_amount);
                $bill->save();
            } else {
                $bill->advanced_payment = max(0, (float)($bill->advanced_payment ?? 0) - $oldAmount + $newAmount);
                $bill->due_payment = max(0, (float)($bill->payble ?? 0) - $bill->advanced_payment);
                $bill->save();
            }

            $payment->payment_method = $request->payment_method_id;
            $payment->amount = $newAmount;
            $payment->remarks = $request->remarks;
            $payment->save();
        });

        return redirect()->back()->with('success', 'Payment updated successfully.');
    }

    public function deletePayment(Request $request, $id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment record not found.');
        }

        $paymentFor = (string)$payment->payment_for;
        $bill = $paymentFor === '1'
            ? Service::find($payment->sale_id)
            : Sale::find($payment->sale_id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $bill, $paymentFor) {
            if ($bill) {
                if ($paymentFor === '1') {
                    $bill->paid_amount = max(0, (float)($bill->paid_amount ?? 0) - (float)$payment->amount);
                    $bill->due_amount = max(0, (float)($bill->bill ?? 0) - $bill->paid_amount);
                    $bill->save();
                } else {
                    $bill->advanced_payment = max(0, (float)($bill->advanced_payment ?? 0) - (float)$payment->amount);
                    $bill->due_payment = max(0, (float)($bill->payble ?? 0) - $bill->advanced_payment);
                    $bill->save();
                }
            }

            $payment->delete();
        });

        return redirect()->back()->with('success', 'Payment deleted successfully.');
    }
}
