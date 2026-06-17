<?php

namespace App\Services;

class VFDService
{
    /**
     * Display welcome message on VFD
     */
    public function displayWelcome()
    {
        $message = "\x1B\x40"; // Initialize
        $message .= "WELCOME\n";
        $message .= "\n";
        $message .= "FEEDTAN STORE";
        
        $this->sendToVFD($message);
    }

    /**
     * Display product on VFD
     */
    public function displayProduct($productName, $quantity, $price, $total)
    {
        $message = "\x1B\x40"; // Initialize
        $message .= $productName . "\n";
        $message .= "\n";
        $message .= "Qty: " . $quantity . "\n";
        $message .= "\n";
        $message .= "TZS " . number_format($total, 0);
        
        $this->sendToVFD($message);
    }

    /**
     * Display payment info on VFD
     */
    public function displayPayment($total, $paid, $change, $paymentMethod)
    {
        $message = "\x1B\x40"; // Initialize
        $message .= "TOTAL\n";
        $message .= "TZS " . number_format($total, 0) . "\n";
        $message .= "\n";
        $message .= strtoupper($paymentMethod) . "\n";
        $message .= number_format($paid, 0) . "\n";
        $message .= "\n";
        $message .= "CHANGE\n";
        $message .= number_format($change, 0);
        
        $this->sendToVFD($message);
    }

    /**
     * Display thank you message on VFD
     */
    public function displayThankYou()
    {
        $message = "\x1B\x40"; // Initialize
        $message .= "THANK YOU\n";
        $message .= "\n";
        $message .= "COME AGAIN";
        
        $this->sendToVFD($message);
    }

    /**
     * Send message to VFD
     */
    private function sendToVFD($message)
    {
        // For now, we'll log the message for debugging
        // In production, you'd connect to the serial port using php-serial/php-serial
        \Log::info('VFD Output:', ['message' => $message]);
        
        // To use actual serial communication:
        // $serial = new \PhpSerial\PhpSerial();
        // $serial->deviceSet("COM3"); // Or /dev/ttyUSB0 on Linux
        // $serial->confBaudRate(9600);
        // $serial->deviceOpen();
        // $serial->sendMessage($message);
        // $serial->deviceClose();
    }
}
