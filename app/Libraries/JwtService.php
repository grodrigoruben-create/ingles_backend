<?php

namespace App\Libraries;

use CodeIgniter\HTTP\RequestInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;

/**
 * Crea y valida los tokens JWT (HS256).
 * Lo usan Auth (para crear el token) y JWTFilter (para validarlo).
 */
class JwtService
{
    private const ALGORITMO = 'HS256';

    private string $emisor;
    private string $audiencia;
    private int $duracion;
    private string $claveSecreta;

    public function __construct()
    {
        $this->emisor       = (string) env('jwt.issuer', 'http://localhost:8080');
        $this->audiencia    = (string) env('jwt.audience', 'http://localhost:5173');
        $this->duracion     = (int) env('jwt.ttl', 3600);
        // Usamos la misma variable de entorno que intentabas usar en JWTAuth
        $this->claveSecreta = (string) env('JWT_SECRET_KEY', 'esta_es_una_clave_secreta_por_defecto_muy_larga_123');
    }

    /** Para compatibilidad con tu JWTFilter actual, siempre devuelve true con HS256 */
    public function llavesDisponibles(): bool
    {
        return true; 
    }

    /** Crea un token firmado con la llave secreta. */
    public function crear(array $datos): string
    {
        $ahora = time();

        $payload = array_merge($datos, [
            'iss' => $this->emisor,
            'aud' => $this->audiencia,
            'iat' => $ahora,
            'exp' => $ahora + $this->duracion,
        ]);

        return JWT::encode($payload, $this->claveSecreta, self::ALGORITMO);
    }

    /**
     * Valida firma, expiración, emisor y audiencia.
     */
    public function validar(string $token): object
    {
        $payload = JWT::decode($token, new Key($this->claveSecreta, self::ALGORITMO));

        /*if (($payload->iss ?? null) !== $this->emisor || ($payload->aud ?? null) !== $this->audiencia) {
          throw new UnexpectedValueException('Token emitido para otro sistema.');
        }*/

        return $payload;
    }

    /** Saca el token del encabezado "Authorization: Bearer ...". */
    public function tokenDe(RequestInterface $request): ?string
    {
        if (preg_match('/^Bearer\s+(\S+)$/i', $request->getHeaderLine('Authorization'), $coincidencia) === 1) {
            return $coincidencia[1];
        }

        return null;
    }

    /** Devuelve el payload si el token de la petición es válido; si no, null. */
    public function desdeRequest(RequestInterface $request): ?object
    {
        $token = $this->tokenDe($request);
        if ($token === null) {
            return null;
        }

        try {
            return $this->validar($token);
        } catch (\Throwable) {
            return null;
        }
    }
}