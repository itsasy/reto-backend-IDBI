<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Services\VoucherService;
use Exception;

class DeleteVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {

    }

    public function __invoke(string $id)
    {
        try {
            $deleted = $this->voucherService->deleteVoucher($id);

            if (!$deleted) {
                return response([
                    'message' => 'Voucher not found'
                ]);
            }

            return response([
                'message' => 'Voucher deleted'
            ]);

        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
