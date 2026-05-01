<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;

class EnvioMailAgradecimientoCompra
{
    protected $email;
    protected $pedidoId;
    protected $cantidad;
    protected $subtotal;
    protected $costoEnvio;
    protected $descuentos;
    protected $total;
    protected $fecha;
    protected $direccionEnvio;
    protected $metodoPago;

    public function __construct(
        $email,
        $pedidoId,
        $cantidad,
        $subtotal,
        $costoEnvio,
        $descuentos,
        $total,
        $fecha,
        $direccionEnvio,
        $metodoPago
    ) {
        $this->email = $email;
        $this->pedidoId = $pedidoId;
        $this->cantidad = $cantidad;
        $this->subtotal = $subtotal;
        $this->costoEnvio = $costoEnvio;
        $this->descuentos = $descuentos;
        $this->total = $total;
        $this->fecha = $fecha;
        $this->direccionEnvio = $direccionEnvio;
        $this->metodoPago = $metodoPago;
    }

    public function enviar()
    {
        try {
            // Construimos el payload para enviar al workflow de GoHighLevel
            $payload = [
                'emailCCO' => 'doralice@mayoristasdeopticas.com',
                'email' => $this->email,
                'pedidoNumero'   => $this->pedidoId,
                'totalArticulos' => $this->cantidad,
                'subtotal'       => $this->subtotal,
                'costoEnvio'     => $this->costoEnvio,
                'descuentos'     => $this->descuentos,
                'total'          => $this->total,
                'fecha'          => $this->fecha,
                'direccionEnvio' => $this->direccionEnvio,
                'metodoPago'     => $this->metodoPago,
            ];

            $webhookUrl = 'https://services.leadconnectorhq.com/hooks/40UecLU7dZ4KdLepJ7UR/webhook-trigger/pI4JCu3PSLheEXBFVz5l';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($webhookUrl, $payload);

            if ($response->failed()) {
                throw new \Exception('Error al enviar al webhook: ' . $response->body());
            }

            return [
                'status' => 'success',
                'message' => 'Webhook enviado correctamente',
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
