<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Transformers\Pdf\FindByIdTransformer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ProformaService
{
    /**
     * Verifica si el logging de tiempos está habilitado
     * 
     * Se puede activar/desactivar mediante la variable de entorno ENABLE_PAGO_WEB_TIME_LOGS
     * o mediante config('pago_web.enable_time_logs', false)
     * 
     * @return bool true si los logs están habilitados, false en caso contrario
     */
    private function isTimeLoggingEnabled()
    {
        // Prioridad: 1) Variable de entorno, 2) Config, 3) Default false
        $envValue = env('ENABLE_PAGO_WEB_TIME_LOGS', null);
        if ($envValue !== null) {
            return filter_var($envValue, FILTER_VALIDATE_BOOLEAN);
        }
        
        return config('pago_web.enable_time_logs', false);
    }

    /**
     * Registra tiempo de una operación en el log
     * 
     * NOTA: Este logging se puede activar/desactivar mediante:
     * - Variable de entorno: ENABLE_PAGO_WEB_TIME_LOGS=true
     * - Configuración: config('pago_web.enable_time_logs', true)
     * 
     * Por defecto está DESACTIVADO para no impactar el rendimiento en producción.
     * Activar solo cuando se necesite analizar tiempos de ejecución.
     */
    private function logTiempo($operacion, $tiempoInicio)
    {
        // Si el logging está deshabilitado, salir inmediatamente
        if (!$this->isTimeLoggingEnabled()) {
            return;
        }
        
        $duracion = (microtime(true) - $tiempoInicio) * 1000; // en ms
        $fecha = date('Y-m-d');
        $logFile = storage_path('logs/pago_web_tiempos_' . $fecha . '.txt');
        $timestamp = date('H:i:s');
        
        $linea = sprintf(
            "[%s] %s | Duración: %.2fms\n",
            $timestamp,
            $operacion,
            $duracion
        );
        
        file_put_contents($logFile, $linea, FILE_APPEND | LOCK_EX);
    }

    public function findAll(Request $request)
    {
        //--
    }

    public function findById(Request $request)
    {
        $pedido = Pedido::where('id', $request->id)->first();

        $tranformer = new FindByIdTransformer();
        $proforma = $tranformer->transform($pedido);

        $pdf = Pdf::loadView('pdf.proforma', ['proforma'=>$proforma]);

        $pdf->getDomPDF();

        return $pdf->stream();
    }

    public function proformaParaEmail($pedidoId)
    {
        $tiempoInicio = microtime(true);
        
        $tiempoPedido = microtime(true);
        $pedido = Pedido::where('id', $pedidoId)->first();
        $this->logTiempo('ProformaService: Obtener pedido', $tiempoPedido);

        $tiempoTransform = microtime(true);
        $tranformer = new FindByIdTransformer();
        $proforma = $tranformer->transform($pedido);
        $this->logTiempo('ProformaService: Transformar datos', $tiempoTransform);

        // ✅ OPTIMIZACIÓN: Configurar DomPDF para mejor rendimiento
        // Como las imágenes ya están en base64, deshabilitamos carga remota
        $tiempoPdf = microtime(true);
        $pdf = Pdf::loadView('pdf.proforma', ['proforma'=>$proforma])
            ->setPaper('a4', 'portrait')
            ->setOption('enable-local-file-access', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false); // Deshabilitar carga remota (usamos base64)

        $pdfContent = $pdf->output();
        $this->logTiempo('ProformaService: Generar PDF (DomPDF)', $tiempoPdf);

        // Guardar el PDF en el directorio storage/app/public/tmpPdf
        $tiempoGuardar = microtime(true);
        $pdfPath = 'public/tmpdf/' . 'proforma_' . $pedidoId . '.pdf';

        try {
            Storage::put($pdfPath, $pdfContent);
            $this->logTiempo('ProformaService: Guardar PDF en storage', $tiempoGuardar);
            $this->logTiempo('ProformaService: TOTAL proformaParaEmail', $tiempoInicio);

            return response()->json(['response' => 'Pdf Guardado!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logTiempo('ProformaService: ERROR al guardar PDF', $tiempoInicio);
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }


    public function update(Request $request)
    {
        //--
    }

    public function delete(Request $request)
    {
        //--
    }
}
