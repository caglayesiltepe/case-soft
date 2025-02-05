<?php

namespace App\Enum;

enum DiscountType: string
{
    case OVER_1000 = '10_PERCENT_OVER_1000';
    case BUY_5_GET_1 = 'BUY_5_GET_1';
    case CHEAPEST_20_PERCENT = 'CHEAPEST_20_PERCENT';

    public function description(): string
    {
        return match ($this) {
            self::OVER_1000 => '1000 TL üzeri siparişlerde %10 indirim uygulanmıştır.',
            self::BUY_5_GET_1 => '6 adet alınan ürünlerden biri ücretsiz verilmiştir.',
            self::CHEAPEST_20_PERCENT => 'Kategori 1’deki en ucuz ürüne %20 indirim uygulanmıştır.',
        };
    }
}
