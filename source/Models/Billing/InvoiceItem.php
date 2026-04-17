<?php

namespace Source\Models\Billing;

use Source\Core\Model;

class InvoiceItem extends Model
{
    public function __construct()
    {
        parent::__construct('invoice_items', [], []);
    }

    public function bootstrap(
        int $clinicId,
        int $invoiceId,
        string $description,
        float $quantity = 1.0,
        float $unitPrice = 0.0,
        float $discount = 0.0,
        ?int $serviceId = null
    ): self {
        $this->clinic_id = $clinicId;
        $this->invoice_id = $invoiceId;
        $this->service_id = $serviceId;
        $this->description = trim($description);
        $this->quantity = $quantity;
        $this->unit_price = $unitPrice;
        $this->discount = $discount;
        $this->total = max(($quantity * $unitPrice) - $discount, 0);
        return $this;
    }

    public function saveInvoiceItem(): bool
    {
        if (empty($this->invoice_id) || empty($this->description) || $this->total <= 0) {
            $this->message->warning('Informe a descrição e o valor do item da fatura.');
            return false;
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error('Não foi possível salvar o item da fatura.');
            }
            return false;
        }

        return true;
    }
}
