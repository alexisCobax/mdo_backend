<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Mail\EnvioCotizacionMailConAdjuntoNotificacion;
use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\Cotizaciondetalle;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class NotificacionesCotizacionService
{
    public function cotizacion()
    {
        $cotizaciones = Cotizacion::where('estado', 0)->get();

        foreach ($cotizaciones as $cotizacion) {

            $fechaActual = date('Y-m-d');
            $datetime1 = date_create($cotizacion->fecha);
            $datetime2 = date_create($fechaActual);
            $contador = date_diff($datetime1, $datetime2);
            $differenceFormat = '%a';
            $diasDiferencia = $contador->format($differenceFormat);

            $enviarEmail = 0;

            switch ($diasDiferencia) {
                case 1:

                    $view = 'mdo.notificaciones.cotizaciones.dia1';

                    $subject = 'Cotizacion';

                    $enviarEmail = 1;

                    break;
                case 2:

                    $view = 'mdo.notificaciones.cotizaciones.dia2';

                    $subject = 'Cotizacion';

                    $enviarEmail = 1;

                    break;
                case 3:

                    $view = 'mdo.notificaciones.cotizaciones.dia3';

                    $subject = 'Cotizacion';

                    $enviarEmail = 1;

                    break;
                case 5:

                    $view = 'mdo.notificaciones.cotizaciones.dia5';

                    $subject = 'Cotizacion';

                    $enviarEmail = 1;

                    break;
                // case 20:

                //     $cotizacionEliminar = Cotizacion::where('id', $cotizacion->id)->first();
                //     $cotizacionEliminar->estado = 2;
                //     $cotizacionEliminar->save();

                //     break;
            }

            if ($enviarEmail) {

                /* genero invoice PDF **/
                $this->generarCotizacionMailPdf($cotizacion->id);

                $cliente = Cliente::where('id', $cotizacion->cliente)->first();

                /** Envio por email PDF**/
                $cuerpo = '';
                $emailMdo = env('MAIL_COTIZACION_MDO');
                // if ($cliente->email) {

                //     $destinatarios = [
                //         $emailMdo,
                //         $cliente->email,
                //     ];
                // } else {
                //     $destinatarios = [
                //         $emailMdo,
                //     ];
                // }

                $destinatarios = ['alexiscobax1@gmail.com', 'mgarralda@cobax.com.ar'];

                $rutaArchivoZip = storage_path('app/public/tmpdf/' . 'cotizacion_' . $cotizacion->id . '.pdf');

                $rutaArchivoFijo = storage_path('app/public/fijos/Inf.TRANSFERENCIA_BANCARIA.pdf');

                Mail::to($destinatarios)->send(new EnvioCotizacionMailConAdjuntoNotificacion($cuerpo, $rutaArchivoZip, $rutaArchivoFijo, $view, $subject));

            }
        }

        return response()->json($cotizacion, Response::HTTP_OK);
    }

    public function generarCotizacionMailPdf($idCotizacion)
    {
        $cotizacion = Cotizacion::where('id', $idCotizacion)->first();

        $detalles = Cotizaciondetalle::where('cotizacion', $idCotizacion)->get()->unique('id')->map(function ($detalle) {
            return [
                'id' => $detalle->id,
                'cotizacion' => $detalle->cotizacion,
                'productoId' => optional($detalle->productos)->id,
                'productoNombre' => optional($detalle->productos)->nombre,
                'productoCodigo' => optional($detalle->productos)->codigo,
                'precio' => $detalle->precio,
                'cantidad' => $detalle->cantidad,
                'subtotal' => number_format($detalle->precio * $detalle->cantidad, 2),
            ];
        })->values();

        $cotizacion = [
            'cotizacion' => $cotizacion->id,
            'fecha' => DateHelper::ToDateCustom($cotizacion->fecha),
            'cliente' => $cotizacion->cliente,
            'nombreCliente' => optional($cotizacion->clientes)->nombre,
            'idCliente' => optional($cotizacion->clientes)->id,
            'telefonoCliente' => optional($cotizacion->clientes)->telefono,
            'direccionCliente' => optional($cotizacion->clientes)->direccion,
            'emailCliente' => optional($cotizacion->clientes)->email,
            'subTotal' => $cotizacion->subTotal,
            'total' => $cotizacion->total,
            'descuento' => $cotizacion->descuento,
            'cantidad' => $detalles->sum('cantidad'),
            'total' => number_format($detalles->sum('subtotal'), 2),
        ];

        $cotizacion['detalles'] = $detalles->all();

        $pdf = Pdf::loadView('pdf.cotizacion', ['cotizacion' => $cotizacion]);
        $pdfContent = $pdf->output();

        // Guardar el PDF en el directorio storage/app/public/tmpPdf
        $pdfPath = 'public/tmpdf/' . 'cotizacion_' . $idCotizacion . '.pdf';

        try {
            Storage::put($pdfPath, $pdfContent);

            return response()->json(['response' => 'Pdf Guardado!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
