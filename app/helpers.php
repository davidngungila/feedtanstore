<?php

if (!function_exists('formatTzs')) {
    function formatTzs($amount)
    {
        return number_format($amount, 2, '.', ',');
    }
}
