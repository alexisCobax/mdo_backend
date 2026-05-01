# Configuración de GoHighLevel

## Archivo de configuración

El archivo `gohighlevel.json` contiene toda la configuración necesaria para la integración con GoHighLevel:

### Ubicación
```
/data/cobax/mayoristasDeOpticas/api/App/config/gohighlevel.json
```

### Estructura del archivo

```json
{
    "baseUrl": "https://marketplace.gohighlevel.com",
    "clientId": "tu_client_id",
    "clientSecret": "tu_client_secret",
    "access_token": "token_actual",
    "refreshToken": "refresh_token_para_renovacion",
    "companyId": "id_de_la_compania",
    "locationId": "id_de_la_ubicacion",
    "scope": "scopes_autorizados",
    "expires_in": 86399,
    "userId": "id_del_usuario",
    "token_expires_at": 1755788018
}
```

## TokenManager

El `TokenManager` es una clase que maneja automáticamente la renovación de tokens:

### Ubicación
```
/data/cobax/mayoristasDeOpticas/api/App/app/Services/TokenManager.php
```

### Uso

```php
use App\Services\TokenManager;

// Crear instancia
$tokenManager = new TokenManager();

// Obtener token válido (se renueva automáticamente si es necesario)
$accessToken = $tokenManager->getValidToken();

// Usar en llamadas a la API
$headers = [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json',
    'Version: 2021-07-28'
];
```

### Funcionalidades

- ✅ **Renovación automática**: Renueva tokens antes de que expiren
- ✅ **Verificación de validez**: Verifica si el token actual es válido
- ✅ **Manejo de errores**: Maneja errores de renovación apropiadamente
- ✅ **Persistencia**: Guarda automáticamente los nuevos tokens

## Integración en controladores

### Ejemplo de uso en GoHighLevelController

```php
public function enviarNuevosArribos(){
    try {
        // Obtener token válido automáticamente
        $tokenManager = new TokenManager();
        $accessToken = $tokenManager->getValidToken();
        
        // Usar el token en la llamada a la API
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'Version: 2021-07-28'
        ];
        
        // ... resto del código
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
```

## Ventajas de esta implementación

1. **Automático**: No necesitas manejar manualmente la renovación de tokens
2. **Seguro**: Los tokens se renuevan 5 minutos antes de expirar
3. **Transparente**: Tu código no cambia, solo agrega 2 líneas
4. **Persistente**: Los tokens se guardan automáticamente en el archivo de configuración
5. **Laravel-friendly**: Usa la estructura de carpetas estándar de Laravel

## Notas importantes

- Los tokens de GoHighLevel expiran en 24 horas
- El sistema renueva automáticamente los tokens 5 minutos antes de que expiren
- El refresh token se actualiza automáticamente en cada renovación
- Asegúrate de que el archivo `gohighlevel.json` tenga permisos de escritura 
