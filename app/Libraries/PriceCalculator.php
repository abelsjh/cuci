<?php

namespace App\Libraries;

use InvalidArgumentException;

class PriceCalculator
{
    /**
     * Hitung subtotal untuk satu item layanan.
     */
    public function calculateItemSubtotal(float $price, int $qty): float
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Harga layanan tidak boleh negatif.');
        }
        if ($qty < 1) {
            throw new InvalidArgumentException('Kuantitas item minimal 1.');
        }

        return round($price * $qty);
    }

    /**
     * Hitung total kotor dari sekumpulan item.
     * $items = [ ['price' => 50000, 'qty' => 1], ... ] atau [ ['subtotal' => 50000], ... ]
     */
    public function calculateGrossTotal(array $items): float
    {
        $gross = 0.0;
        foreach ($items as $item) {
            if (isset($item['subtotal'])) {
                $gross += (float) $item['subtotal'];
            } elseif (isset($item['price'], $item['qty'])) {
                $gross += $this->calculateItemSubtotal((float) $item['price'], (int) $item['qty']);
            }
        }

        return round($gross);
    }

    /**
     * Hitung nilai diskon dalam rupiah.
     * $discountType = 'nominal' (Rp) atau 'percent' (%)
     */
    public function calculateDiscount(float $grossTotal, float $discountValue, string $discountType = 'nominal'): float
    {
        if ($discountValue < 0) {
            return 0.0;
        }

        if ($discountType === 'percent') {
            if ($discountValue > 100) {
                $discountValue = 100.0;
            }
            $discount = ($grossTotal * $discountValue) / 100.0;
        } else {
            $discount = $discountValue;
        }

        // Diskon tidak boleh melebihi total kotor
        if ($discount > $grossTotal) {
            $discount = $grossTotal;
        }

        return round(max(0.0, $discount));
    }

    /**
     * Hitung total bersih yang harus dibayar: total kotor - diskon.
     */
    public function calculateTotal(float $grossTotal, float $discount): float
    {
        $total = $grossTotal - $discount;
        return round(max(0.0, $total));
    }

    /**
     * Validasi pembayaran dan hitung kembalian.
     *
     * @return array{valid: bool, paid_amount: float, change_amount: float, error: ?string}
     */
    public function validatePayment(float $total, string $method, float $paidAmount): array
    {
        $method = strtolower(trim($method));
        $validMethods = ['tunai', 'transfer', 'qris'];

        if (! in_array($method, $validMethods, true)) {
            return [
                'valid'         => false,
                'paid_amount'   => 0.0,
                'change_amount' => 0.0,
                'error'         => 'Metode pembayaran tidak valid. Pilih tunai, transfer, atau qris.',
            ];
        }

        if ($method === 'tunai') {
            if ($paidAmount < $total) {
                return [
                    'valid'         => false,
                    'paid_amount'   => $paidAmount,
                    'change_amount' => 0.0,
                    'error'         => 'Jumlah uang tunai kurang dari total yang harus dibayar.',
                ];
            }

            return [
                'valid'         => true,
                'paid_amount'   => round($paidAmount),
                'change_amount' => round($paidAmount - $total),
                'error'         => null,
            ];
        }

        // Untuk transfer dan qris: nominal harus sesuai total dan kembalian 0
        if ($paidAmount <= 0) {
            $paidAmount = $total;
        }

        if (abs($paidAmount - $total) > 0.01) {
            return [
                'valid'         => false,
                'paid_amount'   => $paidAmount,
                'change_amount' => 0.0,
                'error'         => 'Untuk metode ' . strtoupper($method) . ', nominal pembayaran harus sama persis dengan total.',
            ];
        }

        return [
            'valid'         => true,
            'paid_amount'   => round($total),
            'change_amount' => 0.0,
            'error'         => null,
        ];
    }
}
