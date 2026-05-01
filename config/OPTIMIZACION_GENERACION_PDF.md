# Análisis y Optimización de Generación de PDF

## Problema Identificado

La generación del PDF tarda **~49.5 segundos**, lo cual es extremadamente lento e inaceptable.

## Análisis del Código Actual

### Problemas Encontrados:

#### 1. **N+1 Query Problem (CRÍTICO)**
**Ubicación:** `FindByIdTransformer.php` línea 32

```php
foreach ($detalle as $d) {
    $imagenPrincipal = Fotoproducto::where('id',$d->productos->imagenPrincipal)->first();
    // ...
}
```

**Problema:** Por cada producto en el pedido, se ejecuta una query separada a la base de datos para obtener la imagen. Si hay 50 productos, son 50 queries adicionales.

**Impacto:** Si cada query tarda 100ms, 50 productos = 5 segundos solo en queries.

#### 2. **Carga de Imágenes Remotas en PDF (CRÍTICO)**
**Ubicación:** `proforma.blade.php` línea 145

```php
<img src="{{ $p['imagen'] }}" style="width:90px; height: 70px;" alt="" />
```

**Problema:** DomPDF tiene que descargar cada imagen desde una URL remota durante la generación del PDF. Esto es extremadamente lento porque:
- Cada imagen requiere una petición HTTP
- Las imágenes pueden estar en servidores lentos
- No hay caché de las imágenes descargadas
- DomPDF procesa las imágenes de forma síncrona

**Impacto:** Si hay 50 productos y cada imagen tarda 800ms en descargarse, son 40 segundos solo en descargar imágenes.

#### 3. **Query Mal Optimizada**
**Ubicación:** `FindByIdTransformer.php` línea 21

```php
$detalle = Pedidodetalle::with('productos.colores')->orWhere('pedido', $pedido->id)->get();
```

**Problema:** Usa `orWhere` en lugar de `where`, lo cual puede traer más datos de los necesarios y es menos eficiente.

#### 4. **Sin Eager Loading de Fotoproducto**
**Problema:** Las imágenes no se cargan de forma eficiente con eager loading.

#### 5. **Memory Limit Alto**
**Ubicación:** `FindByIdTransformer.php` línea 17

```php
ini_set('memory_limit', '1G');
```

**Problema:** Necesitar 1GB de memoria sugiere que el proceso es muy ineficiente.

---

## Soluciones Propuestas

### Solución 1: Eliminar N+1 Query con Eager Loading

**Antes:**
```php
$detalle = Pedidodetalle::with('productos.colores')->orWhere('pedido', $pedido->id)->get();

foreach ($detalle as $d) {
    $imagenPrincipal = Fotoproducto::where('id',$d->productos->imagenPrincipal)->first();
    // ...
}
```

**Después:**
```php
// Cargar todas las imágenes en una sola query
$imagenIds = $detalle->pluck('productos.imagenPrincipal')->filter()->unique()->toArray();
$imagenes = Fotoproducto::whereIn('id', $imagenIds)->get()->keyBy('id');

foreach ($detalle as $d) {
    $imagenPrincipal = $imagenes[$d->productos->imagenPrincipal] ?? null;
    // ...
}
```

**Mejora esperada:** De N queries a 1 query adicional. Ahorro: ~4-5 segundos.

---

### Solución 2: Convertir Imágenes Remotas a Base64 o Descargarlas Localmente

**Opción A: Descargar y convertir a Base64 (Recomendado)**

```php
// En el transformer, descargar imágenes y convertir a base64
foreach ($detalle as $d) {
    $imagenBase64 = $this->convertirImagenABase64($imagen);
    $pedidoDetalle[] = [
        // ...
        'imagen' => $imagenBase64, // En lugar de URL
    ];
}

private function convertirImagenABase64($url)
{
    try {
        $imageData = file_get_contents($url);
        $base64 = base64_encode($imageData);
        $mimeType = getimagesizefromstring($imageData)['mime'] ?? 'image/jpeg';
        return 'data:' . $mimeType . ';base64,' . $base64;
    } catch (\Exception $e) {
        // Fallback a imagen por defecto
        return '';
    }
}
```

**Opción B: Usar imágenes locales en lugar de remotas**

Si las imágenes están disponibles localmente, usar rutas locales en lugar de URLs remotas.

**Mejora esperada:** Elimina el tiempo de descarga durante la generación del PDF. Ahorro: ~35-40 segundos.

---

### Solución 3: Corregir Query con where en lugar de orWhere

**Antes:**
```php
$detalle = Pedidodetalle::with('productos.colores')->orWhere('pedido', $pedido->id)->get();
```

**Después:**
```php
$detalle = Pedidodetalle::with('productos.colores')
    ->where('pedido', $pedido->id)
    ->get();
```

**Mejora esperada:** Query más eficiente. Ahorro: ~100-200ms.

---

### Solución 4: Caché de Imágenes

Implementar un sistema de caché para las imágenes descargadas:

```php
private function obtenerImagenConCache($url, $imagenId)
{
    $cacheKey = "producto_imagen_{$imagenId}";
    $cached = Cache::get($cacheKey);
    
    if ($cached) {
        return $cached;
    }
    
    $base64 = $this->convertirImagenABase64($url);
    Cache::put($cacheKey, $base64, 3600); // Cache por 1 hora
    
    return $base64;
}
```

**Mejora esperada:** En pedidos repetidos, ahorro significativo.

---

### Solución 5: Optimizar Configuración de DomPDF

```php
$pdf = Pdf::loadView('pdf.proforma', ['proforma'=>$proforma])
    ->setPaper('a4', 'portrait')
    ->setOption('enable-local-file-access', true)
    ->setOption('isHtml5ParserEnabled', true)
    ->setOption('isRemoteEnabled', false); // Deshabilitar carga remota si usamos base64
```

---

## Implementación Priorizada

### Fase 1 (Impacto Alto - Implementar Inmediatamente):
1. ✅ Eliminar N+1 Query con Eager Loading
2. ✅ Convertir imágenes a Base64 antes de generar PDF
3. ✅ Corregir query con `where` en lugar de `orWhere`

**Mejora esperada total:** De ~49.5s a ~5-8s (reducción del 85-90%)

### Fase 2 (Impacto Medio):
4. Implementar caché de imágenes
5. Optimizar configuración de DomPDF

**Mejora esperada adicional:** De ~5-8s a ~3-5s

---

## Métricas Esperadas

| Métrica | Antes | Después (Fase 1) | Después (Fase 2) |
|---------|-------|------------------|------------------|
| Tiempo total | 49.5s | 5-8s | 3-5s |
| Queries a BD | 50+ | 3-4 | 3-4 |
| Descargas HTTP | 50+ | 0 | 0 |
| Memoria usada | ~1GB | ~200MB | ~150MB |

---

## Notas Importantes

1. **Base64 aumenta el tamaño del PDF**: Las imágenes en base64 pueden aumentar el tamaño del PDF en ~30-40%, pero el tiempo de generación se reduce drásticamente.

2. **Timeout de descarga**: Si una imagen no se puede descargar, usar una imagen por defecto o omitirla.

3. **Memoria**: Con las optimizaciones, debería poder reducirse el memory_limit de 1GB a 256MB o menos.

4. **Testing**: Probar con pedidos de diferentes tamaños (10, 50, 100 productos) para validar las mejoras.

---

## Fecha de Análisis

2026-01-14
