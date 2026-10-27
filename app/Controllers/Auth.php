<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;

class Auth extends ResourceController
{
    public function login()
    {
        // Recibimos los datos que envía React
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $tipo     = $this->request->getVar('tipo'); // 'alumno' o 'docente'

        if (!$username || !$password || !$tipo) {
            return $this->fail('Faltan credenciales o el tipo de usuario', 400);
        }

        $usuarioValido = false;
        $uid = null;

        // Validaciones hardcodeadas temporales
        if ($tipo === 'docente') {
            // Credenciales de prueba para Docente
            if ($username === 'DOC000001' && $password === '123456') {
                $usuarioValido = true;
                $uid = 1; // ID ficticio para el docente
            }
        } elseif ($tipo === 'alumno') {
            // Credenciales de prueba para Alumno
            if ($username === '123456789' && $password === '123456') {
                $usuarioValido = true;
                $uid = 100; // ID ficticio para el alumno
            }
        } else {
            return $this->fail('Rol no válido', 400);
        }

        // Si los datos no coinciden con los de prueba
        if (!$usuarioValido) {
            return $this->failUnauthorized('Credenciales inválidas. Verifica tu usuario y contraseña.');
        }

        // Generamos el Token JWT (sin usar private.pem, ahora usa una clave secreta simple)
        $key = env('JWT_SECRET_KEY', 'esta_es_una_clave_secreta_por_defecto_muy_larga_123');
        
        $payload = [
            'iss' => base_url(),
            'aud' => 'http://localhost:5173',
            'iat' => time(),
            'exp' => time() + 3600, // Expira en 1 hora
            'sub' => $uid,          // Guardamos el ID ficticio
            'rol' => $tipo,         // Guardamos el rol para poder leerlo después en Calificaciones.php
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 200,
            'message' => 'Login exitoso',
            'token' => $jwt,
            'rol' => $tipo
        ]);
    }
}