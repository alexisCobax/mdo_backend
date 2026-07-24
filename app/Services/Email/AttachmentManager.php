<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Gestor de adjuntos para emails
 * Maneja validación, generación, limpieza y conversión de adjuntos
 */
class AttachmentManager
{
    protected $maxFileSize = 10485760; // 10MB por defecto
    protected $allowedMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    /**
     * Valida un archivo adjunto
     */
    public function validateAttachment(string $path, ?string $name = null): array
    {
        $errors = [];

        // Verificar que el archivo existe
        if (!file_exists($path)) {
            $errors[] = "El archivo no existe: {$path}";
            return ['valid' => false, 'errors' => $errors];
        }

        // Verificar que es legible
        if (!is_readable($path)) {
            $errors[] = "El archivo no es legible: {$path}";
            return ['valid' => false, 'errors' => $errors];
        }

        // Verificar tamaño
        $fileSize = filesize($path);
        if ($fileSize > $this->maxFileSize) {
            $errors[] = "El archivo excede el tamaño máximo permitido ({$this->maxFileSize} bytes)";
            return ['valid' => false, 'errors' => $errors];
        }

        // Verificar tipo MIME
        $mimeType = mime_content_type($path);
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            $errors[] = "Tipo de archivo no permitido: {$mimeType}";
            return ['valid' => false, 'errors' => $errors];
        }

        return [
            'valid' => true,
            'path' => $path,
            'name' => $name ?? basename($path),
            'size' => $fileSize,
            'mime_type' => $mimeType,
        ];
    }

    /**
     * Valida múltiples adjuntos
     */
    public function validateAttachments(array $attachments): array
    {
        $validated = [];
        $errors = [];

        foreach ($attachments as $attachment) {
            $path = is_array($attachment) ? $attachment['path'] : $attachment;
            $name = is_array($attachment) ? ($attachment['name'] ?? null) : null;

            $validation = $this->validateAttachment($path, $name);

            if ($validation['valid']) {
                $validated[] = [
                    'path' => $validation['path'],
                    'name' => $validation['name'],
                    'size' => $validation['size'],
                    'mime_type' => $validation['mime_type'],
                ];
            } else {
                $errors = array_merge($errors, $validation['errors']);
            }
        }

        return [
            'valid' => empty($errors),
            'attachments' => $validated,
            'errors' => $errors,
        ];
    }

    /**
     * Genera un PDF y lo guarda en storage
     */
    public function generateAndStorePdf(
        string $view,
        array $data,
        string $filename,
        string $directory = 'tmpdf'
    ): array {
        try {
            // Generar PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)
                ->setPaper('a4', 'portrait')
                ->setOption('enable-local-file-access', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', false);

            $pdfContent = $pdf->output();

            // Guardar en storage
            $storagePath = "public/{$directory}/{$filename}";
            Storage::put($storagePath, $pdfContent);

            $fullPath = storage_path("app/{$storagePath}");

            Log::info('AttachmentManager: PDF generado y guardado', [
                'filename' => $filename,
                'path' => $fullPath,
                'size' => strlen($pdfContent)
            ]);

            return [
                'success' => true,
                'path' => $fullPath,
                'storage_path' => $storagePath,
                'size' => strlen($pdfContent),
            ];

        } catch (Exception $e) {
            Log::error('AttachmentManager: Error al generar PDF', [
                'view' => $view,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtiene la ruta completa de un archivo en storage
     */
    public function getStoragePath(string $relativePath): ?string
    {
        $fullPath = storage_path("app/{$relativePath}");

        if (file_exists($fullPath)) {
            return $fullPath;
        }

        // Intentar con public/
        $fullPath = storage_path("app/public/{$relativePath}");
        if (file_exists($fullPath)) {
            return $fullPath;
        }

        return null;
    }

    /**
     * Convierte adjuntos a formato para Laravel Mail
     */
    public function prepareForLaravelMail(array $attachments): array
    {
        $prepared = [];

        foreach ($attachments as $attachment) {
            $path = is_array($attachment) ? $attachment['path'] : $attachment;
            $name = is_array($attachment) ? ($attachment['name'] ?? null) : null;

            $validation = $this->validateAttachment($path, $name);

            if ($validation['valid']) {
                $prepared[] = [
                    'path' => $validation['path'],
                    'name' => $validation['name'],
                ];
            } else {
                Log::warning('AttachmentManager: Adjunto inválido omitido', [
                    'path' => $path,
                    'errors' => $validation['errors']
                ]);
            }
        }

        return $prepared;
    }

    /**
     * Convierte adjuntos a URLs públicas para GoHighLevel
     * (GoHighLevel no soporta adjuntos directos, necesita URLs)
     */
    public function prepareForGoHighLevel(array $attachments, bool $uploadToStorage = true): array
    {
        $urls = [];

        foreach ($attachments as $attachment) {
            $path = is_array($attachment) ? $attachment['path'] : $attachment;

            $validation = $this->validateAttachment($path);

            if (!$validation['valid']) {
                Log::warning('AttachmentManager: Adjunto inválido para GoHighLevel', [
                    'path' => $path,
                    'errors' => $validation['errors']
                ]);
                continue;
            }

            // Si el archivo ya está en storage público, generar URL
            if (strpos($path, storage_path('app/public')) !== false) {
                $relativePath = str_replace(storage_path('app/public/'), '', $path);
                $url = asset("storage/{$relativePath}");
                $urls[] = $url;
            } elseif ($uploadToStorage) {
                // Subir a storage público y generar URL
                $filename = basename($path);
                $publicPath = "public/tmpdf/{$filename}";

                try {
                    Storage::put($publicPath, file_get_contents($path));
                    $url = asset("storage/tmpdf/{$filename}");
                    $urls[] = $url;
                } catch (Exception $e) {
                    Log::error('AttachmentManager: Error al subir archivo a storage', [
                        'path' => $path,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $urls;
    }

    /**
     * Limpia archivos temporales antiguos
     */
    public function cleanOldFiles(string $directory = 'tmpdf', int $maxAge = 86400): int
    {
        $cleaned = 0;
        $directoryPath = storage_path("app/public/{$directory}");

        if (!is_dir($directoryPath)) {
            return 0;
        }

        $files = glob($directoryPath . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $age = time() - filemtime($file);
                if ($age > $maxAge) {
                    try {
                        unlink($file);
                        $cleaned++;
                        Log::info('AttachmentManager: Archivo temporal eliminado', [
                            'file' => $file,
                            'age' => $age
                        ]);
                    } catch (Exception $e) {
                        Log::error('AttachmentManager: Error al eliminar archivo temporal', [
                            'file' => $file,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        return $cleaned;
    }

    /**
     * Obtiene información de un archivo
     */
    public function getFileInfo(string $path): ?array
    {
        if (!file_exists($path)) {
            return null;
        }

        return [
            'path' => $path,
            'name' => basename($path),
            'size' => filesize($path),
            'mime_type' => mime_content_type($path),
            'exists' => true,
            'readable' => is_readable($path),
            'modified' => filemtime($path),
        ];
    }

    /**
     * Establece el tamaño máximo permitido
     */
    public function setMaxFileSize(int $bytes): self
    {
        $this->maxFileSize = $bytes;
        return $this;
    }

    /**
     * Agrega tipos MIME permitidos
     */
    public function addAllowedMimeType(string $mimeType): self
    {
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            $this->allowedMimeTypes[] = $mimeType;
        }
        return $this;
    }
}
