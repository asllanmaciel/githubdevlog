<?php

namespace App\Services;

class MercadoPagoBillingService
{
    public function isConfigured(): bool
    {
        return filled(config('services.mercado_pago.access_token'));
    }

    public function checkoutStatus(): array
    {
        return [
            'provider' => 'mercado_pago',
            'sdk' => class_exists(\MercadoPago\MercadoPagoConfig::class) ? 'installed' : 'missing',
            'configured' => $this->isConfigured(),
            'next_step' => 'Criar preferência de pagamento e webhook de confirmação.',
        ];
    }
}