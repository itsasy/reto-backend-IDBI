<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVoucherUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $xmlContents;
    protected User $user;
    private string $voucherService;

    /**
     * @param array $xmlContents
     * @param User $user
     */
    public function __construct(array $xmlContents, User $user)
    {
        $this->xmlContents = $xmlContents;
        $this->user = $user;
    }

    public function handle(): void
    {
        $voucherService = new VoucherService();
        $voucherService->storeVouchersFromXmlContents($this->xmlContents, $this->user);
    }

}
