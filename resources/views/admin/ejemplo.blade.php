@extends('admin.layout')

@section('title', 'Ejemplo - Panel Administrativo')
@section('page-title', 'Ejemplo')

@php
    $header = true;
@endphp

@section('content')
    <h2 style="margin-bottom: 20px; color: #2c3e50;">Ejemplo</h2>
    
    <p style="color: #7f8c8d; margin-bottom: 30px;">
        Aquí puedes gestionar los nuevos arribos de productos al inventario.
    </p>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Proveedor</th>
                    <th>Fecha de Arribo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($arribos as $arribo)
                <tr>
                    <td>{{ $arribo['id'] }}</td>
                    <td>{{ $arribo['producto'] }}</td>
                    <td>{{ number_format($arribo['cantidad']) }}</td>
                    <td>{{ $arribo['proveedor'] }}</td>
                    <td>{{ $arribo['fecha_arribo'] }}</td>
                    <td>
                        @if($arribo['estado'] == 'Recibido')
                            <span class="badge badge-success">{{ $arribo['estado'] }}</span>
                        @elseif($arribo['estado'] == 'En Tránsito')
                            <span class="badge badge-warning">{{ $arribo['estado'] }}</span>
                        @else
                            <span class="badge badge-danger">{{ $arribo['estado'] }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
        <p style="color: #7f8c8d; font-size: 14px;">
            Total de arribos: <strong>{{ count($arribos) }}</strong>
        </p>
    </div>
@endsection






