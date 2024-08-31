<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class GetVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {

    }

    public function __invoke(Request $request)
    {
        $vouchers = $this->voucherService->getVoucher([
            'serie' => $request->query('serie'),
            'number' => $request->query('number'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ]);

        return response([
            'data' => VoucherResource::collection($vouchers),
            'message' => 'Vouchers retrieved'
        ]);
    }
}
