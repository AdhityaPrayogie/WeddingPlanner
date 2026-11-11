<?php

namespace App\Support;

class Phone
{
    /**
     * Link wa.me dari nomor HP Indonesia, atau null bila nomor tidak valid ("-", kosong, terlalu pendek).
     * 0812… / +62812… / 62812… → https://wa.me/62812…
     */
    public static function waLink(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        $digits = preg_replace('/^0/', '62', $digits);
        if (!str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return strlen($digits) >= 10 && strlen($digits) <= 15 ? 'https://wa.me/' . $digits : null;
    }
}
