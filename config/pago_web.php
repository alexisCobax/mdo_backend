<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Habilitar Logging de Tiempos de Ejecución
    |--------------------------------------------------------------------------
    |
    | Esta opción controla si se registran los tiempos de ejecución de cada
    | paso del proceso de pago con tarjeta de crédito.
    |
    | Los logs se guardan en: storage/logs/pago_web_tiempos_YYYY-MM-DD.txt
    |
    | IMPORTANTE: Por defecto está DESACTIVADO para no impactar el rendimiento
    | en producción. Activar solo cuando se necesite analizar tiempos de ejecución.
    |
    | Se puede activar de dos formas:
    | 1. Variable de entorno: ENABLE_PAGO_WEB_TIME_LOGS=true
    | 2. Este archivo de configuración: 'enable_time_logs' => true
    |
    | La variable de entorno tiene prioridad sobre este archivo.
    |
    */

    'enable_time_logs' => env('ENABLE_PAGO_WEB_TIME_LOGS', false),
];
