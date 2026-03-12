<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\PriceListItem;

class BillingController extends Controller
{

    public function create(Appointment $appointment)
    {

        $appointment->load([
            'pet',
            'treatments.priceItem'
        ]);

        $priceItems = PriceListItem::where('is_active',1)->get();

        return view('clinic.billing.create',[
            'appointment' => $appointment,
            'priceItems' => $priceItems
        ]);

    }

}