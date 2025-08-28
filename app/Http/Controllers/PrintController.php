<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Journal;
use App\Models\Purchase;
use App\Models\SalesPayment;
use Illuminate\Http\Request;
use App\Models\ItemTransaction;
use App\Models\PurchasePayment;

class PrintController extends Controller
{
    public function journal($id)
    {
        $journal = Journal::findOrFail($id);
        return view('print.journal', compact('journal'));
    }

    public function itemTransaction($id)
    {
        $itemTransaction = ItemTransaction::findOrFail($id);
        return view('print.itemTransaction', compact('itemTransaction'));
    }

    public function purchase($id)
    {
        $purchase = Purchase::findOrFail($id);
        return view('print.purchase', compact('purchase'));
    }

    public function purchasePayment($id)
    {
        $purchasePayment = PurchasePayment::findOrFail($id);
        return view('print.purchasePayment', compact('purchasePayment'));
    }

    public function sales($id)
    {
        $sales = Sales::findOrFail($id);
        return view('print.sales', compact('sales'));
    }

    public function salesPayment($id)
    {
        $salesPayment = SalesPayment::findOrFail($id);
        return view('print.salesPayment', compact('salesPayment'));
    }
}
