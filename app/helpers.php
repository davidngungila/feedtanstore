<?php

if (!function_exists('formatTzs')) {
    function formatTzs($amount)
    {
        return 'TZS ' . number_format($amount, 0, '.', ',');
    }
}
