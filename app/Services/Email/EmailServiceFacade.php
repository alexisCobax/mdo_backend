<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Facade;

/**
 * Facade para el EmailService
 * Permite usar EmailService::send() directamente
 */
class EmailServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'email.service';
    }
}
