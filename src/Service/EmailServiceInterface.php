<?php


namespace App\Service;


interface EmailServiceInterface
{

    public function sendOrderStatusChangedEmail(string $to, string $status);

    public function exportCompletedEmail(string $to);
}