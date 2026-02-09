<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
        18 => 'Eighteen', 19 => 'Nineteen'
    ];

    private static $tens = [
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    ];

    public static function convert($number): string
    {
        $number = (int) $number;
        
        if ($number == 0) {
            return 'Zero';
        }

        if ($number < 0) {
            return 'Minus ' . self::convert(abs($number));
        }

        $words = '';

        // Crore (10 million)
        if ($number >= 10000000) {
            $words .= self::convert(floor($number / 10000000)) . ' Crore ';
            $number %= 10000000;
        }

        // Lakh (100 thousand)
        if ($number >= 100000) {
            $words .= self::convert(floor($number / 100000)) . ' Lakh ';
            $number %= 100000;
        }

        // Thousand
        if ($number >= 1000) {
            $words .= self::convert(floor($number / 1000)) . ' Thousand ';
            $number %= 1000;
        }

        // Hundred
        if ($number >= 100) {
            $words .= self::$ones[floor($number / 100)] . ' Hundred ';
            $number %= 100;
        }

        if ($number > 0) {
            if ($number < 20) {
                $words .= self::$ones[$number];
            } else {
                $words .= self::$tens[floor($number / 10)];
                if ($number % 10 > 0) {
                    $words .= ' ' . self::$ones[$number % 10];
                }
            }
        }

        return trim($words);
    }

    public static function convertTaka($number): string
    {
        $number = (float) $number;
        $whole = (int) $number;
        $paisa = round(($number - $whole) * 100);

        $words = self::convert($whole) . ' Taka';

        if ($paisa > 0) {
            $words .= ' and ' . self::convert($paisa) . ' Paisa';
        }

        return $words . ' Only';
    }
}
