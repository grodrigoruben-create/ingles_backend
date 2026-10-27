<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JwtService;
use Config\Services;

class JWTFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');

        if (!$header) {
            return Services::response()
                ->setJSON([
                    'status' => 401,
                    'error' => 'Token no proporcionado'
                ])
                ->setStatusCode(401);
        }

        $token = str_replace('Bearer ', '', $header);
        $decodedData = (new JwtService())->validar($token);

        if (!$decodedData) {
            return Services::response()
                ->setJSON([
                    'status' => 401,
                    'error' => 'Token inválido o expirado'
                ])
                ->setStatusCode(401);
        }

        // Se adjuntan los datos cargados en el token a la petición
        //$request->userData = $decodedData;
        $GLOBALS['userData'] = $decodedData;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
        // No necesitamos hacer nada después de la petición
    }

    private function error(RequestInterface $request, int $codigo, string $mensaje): ResponseInterface
    {
        $respuesta = service('response')
            ->setStatusCode($codigo)
            ->setJSON([
                'status'   => $codigo,
                'error'    => $codigo,
                'messages' => ['error' => $mensaje],
            ]);

        // Añadimos las cabeceras de tu propio CORS manualmente para evitar bloqueos del navegador
        $respuesta->setHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
        $respuesta->setHeader('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
        $respuesta->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');

        return $respuesta;
    }
}