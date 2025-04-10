<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SriService
{
    protected $baseUrl = 'https://srienlinea.sri.gob.ec/movil-servicios/api/v1.0/deudas/porIdentificacion/';

    public function consultarCedula($cedula)
    {
        // Intentar obtener desde caché primero
        $cacheKey = 'sri_consulta_' . $cedula;
        
        return Cache::remember($cacheKey, 3600, function () use ($cedula) {
            try {
                $response = Http::timeout(15)->get($this->baseUrl . $cedula);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['contribuyente'])) {
                        return [
                            'success' => true,
                            'nombre' => $data['contribuyente']['nombreComercial'] ?? null,
                            'tipoIdentificacion' => $data['contribuyente']['tipoIdentificacion'] ?? null,
                            'clase' => $data['contribuyente']['clase'] ?? null,
                            'data' => $data
                        ];
                    }
                }
                
                return [
                    'success' => false,
                    'message' => 'No se encontraron datos en el SRI'
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Error al consultar el SRI: ' . $e->getMessage()
                ];
            }
        });
    }
}
