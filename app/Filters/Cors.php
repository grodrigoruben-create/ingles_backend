<?php
namespace App\Filters;
 
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
 
class Cors implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
        {
            // Solo debe existir UNA línea de Allow-Origin
            header('Access-Control-Allow-Origin: http://localhost:5173');
            header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
    
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                header("HTTP/1.1 200 OK");
                exit(0);
            }
        }
 
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Asegurar que las cabeceras también viajen en la respuesta final
        $response->setHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->setHeader('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    }
}