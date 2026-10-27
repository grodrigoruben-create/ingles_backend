<?php

namespace App\Controllers;

use App\Models\AlumnoModel;
use CodeIgniter\RESTful\ResourceController;

class Alumnos extends ResourceController
{
    protected $format = 'json';

    /** GET /api/alumnos (solo docente). Nunca incluye contraseña ni hash. */
    public function index()
    {
        return $this->respond(model(AlumnoModel::class)->listaSegura());
    }

    /** POST /api/alumno (Registro desde la app Android) */
    public function create()
    {
        // 1. Recibir los datos enviados por Retrofit
        $data = $this->request->getJSON();

        if (!$data) {
            return $this->fail('No se recibieron datos del formulario', 400);
        }

        // 2. Mapear los datos de Android a las columnas de la Base de Datos
        $datosParaGuardar = [
            'nombre_completo' => $data->nombre, // Android envía "nombre", la BD guarda "nombre_completo"
            'matricula'       => $data->matricula,
            'correo'          => $data->correo,
            'carrera'         => $data->carrera,
            'contrasena'      => password_hash($data->contrasena, PASSWORD_DEFAULT) // Encriptación obligatoria
        ];

        // 3. Insertar en la base de datos
        $modelo = new AlumnoModel();

        try {
            if ($modelo->insert($datosParaGuardar)) {
                // 4. Respuesta de éxito que la app Android está esperando
                return $this->respond([
                    'status'  => 201,
                    'message' => 'Registro exitoso. Ya puedes iniciar sesión.'
                ], 201);
            } else {
                // Si tienes reglas de validación en el modelo y fallan
                return $this->fail($modelo->errors(), 400);
            }
        } catch (\Exception $e) {
            // Si la base de datos rechaza la inserción (por ejemplo, si la matrícula o correo ya existen)
            return $this->failServerError('Error al registrar: La matrícula o el correo ya están en uso.');
        }
    }
}