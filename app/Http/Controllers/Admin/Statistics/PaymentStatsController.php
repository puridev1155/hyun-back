<?php

namespace App\Http\Controllers\Admin\Statistics;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\Country;
use App\Models\Payment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PaymentStatsController extends Controller
{
    public function index()
    {
        //전체 participant 수 (이벤트, 오디션, 티켓)
        $totalPayment = Payment::count();


        //전체 카테고리당 participant 수 (이벤트, 오디션, 티켓)
        $totalPaymentByType = Payment::groupBy('payment_type')
                        ->select('payment_type', DB::raw('COUNT(*) as payment_count'))
                        ->get();

        //전체 payment status 당 participant 수 (hold, paid, cancelled)
        $totalPaymentByStatus = Payment::groupBy('status')
        ->select('status', DB::raw('COUNT(*) as payment_count'))
        ->get();



        // Print the final JSON
        return response()->json(compact('totalPayment', 'totalPaymentByType',
                            'totalPaymentByStatus'));
    }
}
