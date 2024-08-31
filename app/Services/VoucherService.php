<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param array $values
     * @return Collection
     */
    public function getVoucher(array $values): Collection
    {
        $queryBuilder = Voucher::query();

        if (!empty($values['serie'])) {
            $queryBuilder->where('serie', $values['serie']);
        }

        if (!empty($values['number'])) {
            $queryBuilder->where('number', $values['number']);
        }

        if (!empty($values['start_date'])) {
            $queryBuilder->where('start_date', '>=', $values['start_date']);
        }

        if (!empty($values['end_date'])) {
            $queryBuilder->where('end_date', '<=', $values['end_date']);
        }

        $queryBuilder->where('user_id', auth()->id());

        return $queryBuilder->get();
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $serie = (string)$xml->xpath('//cbc:ID')[0];
        $number = (string)$xml->xpath('//cbc:ID')[1];
        $type = (string)$xml->xpath('//cbc:InvoiceTypeCode')[0];
        $currency = (string)$xml->xpath('//cbc:DocumentCurrencyCode')[0];


        $issuerName = (string)$xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string)$xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string)$xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string)$xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string)$xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string)$xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string)$xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        $voucher = new Voucher([
            'serie' => $serie,
            'number' => $number,
            'type' => $type,
            'currency' => $currency,
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string)$invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float)$invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float)$invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }

    /**
     * @param string $id
     * @return Voucher|Voucher[]|Collection|Model
     * @throws \Exception
     */
    public function deleteVoucher(string $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        $voucher->delete();

        return $voucher;
    }

    /**
     * @param string|null $currency
     * @return array
     */
    public function getTotalAmounts(?string $currency = null): array
    {
        return Voucher::selectRaw('currency, SUM(total_amount) as total_amount')
            ->when($currency, function ($query, $currency) {
                return $query->where('currency', $currency);
            })
            ->groupBy('currency')
            ->pluck('total_amount', 'currency')
            ->toArray();
    }
}
