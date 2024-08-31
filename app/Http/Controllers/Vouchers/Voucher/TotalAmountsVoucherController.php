<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Http\Controllers\Controller;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class TotalAmountsVoucherController extends Controller
{
    public function __construct(private readonly VoucherService $voucherService)
    {

    }

    public function __invoke(Request $request)
    {
        $currency = $request->query('currency');
        $totals = $this->voucherService->getTotalAmounts($currency);

        return response([
           'data' => $totals,
        ]);
    }
}
