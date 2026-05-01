@extends('admin.layout')

@section('title', 'Nuevos Arribos - Panel Administrativo')
@section('page-title', 'Nuevos Arribos')

@php
    $header = true;
@endphp

@section('styles')
<style>
    .search-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .search-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }
    
    .form-group-search {
        flex: 1;
    }
    
    .form-group-search label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 500;
        font-size: 14px;
    }
    
    .form-group-search input {
        width: 100%;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    
    .form-group-search input:focus {
        outline: none;
        border-color: #3498db;
    }
    
    .btn-search {
        padding: 10px 24px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
        height: 42px;
    }
    
    .btn-search:hover {
        background: #2980b9;
    }
    
    .btn-search:disabled {
        background: #95a5a6;
        cursor: not-allowed;
    }
    
    .results-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .loading {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }
    
    .loading::after {
        content: '...';
        animation: dots 1.5s steps(4, end) infinite;
    }
    
    @keyframes dots {
        0%, 20% { content: '.'; }
        40% { content: '..'; }
        60%, 100% { content: '...'; }
    }
    
    .no-results {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }
    
    .pagination button {
        padding: 8px 16px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .pagination button:hover:not(:disabled) {
        background: #f8f9fa;
        border-color: #3498db;
    }
    
    .pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination .page-info {
        padding: 8px 16px;
        color: #555;
    }
    
    .results-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .results-table th,
    .results-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .results-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2c3e50;
    }
    
        .results-table tr:hover {
            background: #f8f9fa;
        }
        
        .results-table tr.clickable {
            cursor: pointer;
        }
        
        .results-table tr.clickable:hover {
            background: #e3f2fd;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #2c3e50;
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .response-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 12px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .response-success {
            border-left: 4px solid #27ae60;
        }
        
        .response-error {
            border-left: 4px solid #e74c3c;
        }
</style>
@endsection

@section('content')
    <h2 style="margin-bottom: 20px; color: #2c3e50;">Nuevos Arribos</h2>
    
    <div class="search-container">
        <form class="search-form" id="searchForm">
            <div class="form-group-search">
                <label for="marca">Marca</label>
                <input 
                    type="text" 
                    id="marca" 
                    name="marca" 
                    placeholder="Buscar marca..."
                    autocomplete="off"
                >
            </div>
            <button type="submit" class="btn-search" id="btnSearch">
                Buscar
            </button>
        </form>
    </div>
    
    <div class="results-container" id="resultsContainer">
        <div class="loading">Cargando marcas...</div>
    </div>
    
    <!-- Modal para mostrar respuesta de GoHighLevel -->
    <div id="responseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Respuesta de GoHighLevel</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div id="modalBody">
                <div class="loading">Cargando...</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;
    let currentSearch = '';
    const perPage = 10;
    const apiBaseUrl = '{{ url("/api") }}';
    const apiToken = @json($token ?? null);
    
    // Debug: verificar que el token esté presente
    if (!apiToken) {
        console.error('⚠️ Token de API no disponible');
    } else {
        console.log('✅ Token de API disponible:', apiToken.substring(0, 20) + '...');
    }
    
    // Cargar marcas automáticamente al cargar la página
    window.addEventListener('DOMContentLoaded', function() {
        currentSearch = '';
        searchMarcas();
    });
    
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        currentSearch = document.getElementById('marca').value.trim();
        searchMarcas();
    });
    
    function searchMarcas() {
        const container = document.getElementById('resultsContainer');
        const btnSearch = document.getElementById('btnSearch');
        
        // Mostrar loading
        container.innerHTML = '<div class="loading">Buscando</div>';
        btnSearch.disabled = true;
        
        // Construir URL con parámetros
        const params = new URLSearchParams({
            cantidad: perPage,
            pagina: currentPage,
            src: 'config'
        });
        
        // Si hay búsqueda, agregar parámetro de nombre
        if (currentSearch) {
            params.append('nombre', currentSearch);
        }
        
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        
        // Agregar token de autenticación si está disponible
        if (apiToken) {
            headers['Authorization'] = `Bearer ${apiToken}`;
        } else {
            console.error('❌ No hay token disponible para la petición');
            btnSearch.disabled = false;
            container.innerHTML = '<div class="no-results" style="color: #e74c3c;">Error: No se pudo obtener el token de autenticación. Por favor, cierra sesión y vuelve a iniciar sesión.</div>';
            return;
        }
        
        fetch(`${apiBaseUrl}/marcaproducto?${params.toString()}`, {
            method: 'GET',
            headers: headers
        })
            .then(response => {
                // Si la respuesta no es OK, intentar leer el JSON del error
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(JSON.stringify(errorData));
                    }).catch(() => {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(responseData => {
                btnSearch.disabled = false;
                
                // La API devuelve { data: { original: { results: [...], total: ... } } }
                let data = responseData;
                
                // Acceder a la estructura correcta
                if (data.data && data.data.original) {
                    data = data.data.original;
                } else if (data.data) {
                    data = data.data;
                } else if (data.original) {
                    data = data.original;
                }
                
                console.log('Datos procesados:', data);
                
                if (data.results && data.results.length > 0) {
                    displayResults(data);
                } else {
                    container.innerHTML = '<div class="no-results">No se encontraron resultados</div>';
                }
            })
            .catch(error => {
                btnSearch.disabled = false;
                let errorMessage = 'Error al cargar los datos. Por favor, intenta nuevamente.';
                try {
                    const errorObj = JSON.parse(error.message);
                    errorMessage = errorObj.message || errorObj.error || errorMessage;
                } catch (e) {
                    errorMessage = error.message || errorMessage;
                }
                container.innerHTML = `<div class="no-results" style="color: #e74c3c;">${errorMessage}</div>`;
                console.error('Error completo:', error);
                console.error('Token usado:', apiToken ? 'Token presente' : 'Token ausente');
            });
    }
    
    function displayResults(data) {
        const container = document.getElementById('resultsContainer');
        
        let html = `
            <table class="results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Mostrar en Web</th>
                        <th>VIP</th>
                        <th>Propia</th>
                        <th>Suspendido</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        data.results.forEach(marca => {
            // Convertir valores string a boolean para comparaciones
            const mostrarWeb = marca.mostrarWeb === 1 || marca.mostrarWeb === '1' || marca.mostrarWeb === true;
            const vip = marca.vip === 1 || marca.vip === '1' || marca.vip === true;
            const propia = marca.propia === 1 || marca.propia === '1' || marca.propia === true;
            const suspendido = marca.suspendido === 1 || marca.suspendido === '1' || marca.suspendido === true;
            
            html += `
                <tr class="clickable" onclick="enviarAGohighLevel(${marca.id}, '${marca.nombre || ''}')">
                    <td>${marca.id}</td>
                    <td>${marca.nombre || '-'}</td>
                    <td>${mostrarWeb ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>'}</td>
                    <td>${vip ? '<span class="badge badge-success">Sí</span>' : '<span class="badge">No</span>'}</td>
                    <td>${propia ? '<span class="badge badge-success">Sí</span>' : '<span class="badge">No</span>'}</td>
                    <td>${suspendido ? '<span class="badge badge-danger">Sí</span>' : '<span class="badge badge-success">No</span>'}</td>
                </tr>
            `;
        });
        
        html += `
                </tbody>
            </table>
        `;
        
        // Agregar paginación
        html += createPagination(data);
        
        container.innerHTML = html;
    }
    
    function createPagination(data) {
        // Convertir cantidad_por_pagina a número si viene como string
        const itemsPerPage = parseInt(data.cantidad_por_pagina) || 10;
        const totalPages = Math.ceil(data.total / itemsPerPage);
        
        if (totalPages <= 1) {
            return '';
        }
        
        let html = '<div class="pagination">';
        
        // Botón anterior
        html += `
            <button 
                onclick="changePage(${currentPage - 1})" 
                ${currentPage === 1 ? 'disabled' : ''}
            >
                Anterior
            </button>
        `;
        
        // Información de página
        html += `
            <div class="page-info">
                Página ${data.pagina} de ${totalPages} (Total: ${data.total} registros)
            </div>
        `;
        
        // Botón siguiente
        html += `
            <button 
                onclick="changePage(${currentPage + 1})" 
                ${currentPage >= totalPages ? 'disabled' : ''}
            >
                Siguiente
            </button>
        `;
        
        html += '</div>';
        
        return html;
    }
    
    function changePage(page) {
        currentPage = page;
        searchMarcas();
        // Scroll al inicio de los resultados
        document.getElementById('resultsContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Hacer la función changePage disponible globalmente
    window.changePage = changePage;
    
    // Función para enviar a GoHighLevel
    function enviarAGohighLevel(marcaId, marcaNombre) {
        const modal = document.getElementById('responseModal');
        const modalBody = document.getElementById('modalBody');
        
        // Mostrar modal con loading
        modal.style.display = 'block';
        modalBody.innerHTML = `
            <div class="loading">Enviando a GoHighLevel para la marca: <strong>${marcaNombre}</strong> (ID: ${marcaId})</div>
        `;
        
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        
        if (apiToken) {
            headers['Authorization'] = `Bearer ${apiToken}`;
        }
        
        fetch(`${apiBaseUrl}/ghl/webhook/nuevos-arribos-por-marca`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                marca_id: marcaId
            })
        })
        .then(response => response.json())
        .then(data => {
            const isSuccess = data.status === 'success';
            const responseClass = isSuccess ? 'response-success' : 'response-error';
            
            modalBody.innerHTML = `
                <div class="${responseClass} response-content">
                    <h4 style="margin-top: 0;">${isSuccess ? '✅ Éxito' : '❌ Error'}</h4>
                    <p><strong>Marca:</strong> ${marcaNombre} (ID: ${marcaId})</p>
                    <p><strong>Mensaje:</strong> ${data.message || 'Sin mensaje'}</p>
                    ${data.http_code ? `<p><strong>HTTP Code:</strong> ${data.http_code}</p>` : ''}
                    <details style="margin-top: 15px;">
                        <summary style="cursor: pointer; font-weight: bold;">Ver respuesta completa</summary>
                        <pre style="margin-top: 10px; white-space: pre-wrap; word-wrap: break-word;">${JSON.stringify(data, null, 2)}</pre>
                    </details>
                </div>
            `;
        })
        .catch(error => {
            modalBody.innerHTML = `
                <div class="response-error response-content">
                    <h4 style="margin-top: 0;">❌ Error</h4>
                    <p><strong>Marca:</strong> ${marcaNombre} (ID: ${marcaId})</p>
                    <p><strong>Error:</strong> ${error.message || 'Error desconocido'}</p>
                    <pre style="margin-top: 10px; white-space: pre-wrap; word-wrap: break-word;">${error.stack || error}</pre>
                </div>
            `;
        });
    }
    
    // Función para cerrar el modal
    function closeModal() {
        document.getElementById('responseModal').style.display = 'none';
    }
    
    // Cerrar modal al hacer click fuera de él
    window.onclick = function(event) {
        const modal = document.getElementById('responseModal');
        if (event.target == modal) {
            closeModal();
        }
    }
    
    // Hacer funciones disponibles globalmente
    window.enviarAGohighLevel = enviarAGohighLevel;
    window.closeModal = closeModal;
</script>
@endsection
