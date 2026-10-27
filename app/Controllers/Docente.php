<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DocenteModel;

class Docente extends ResourceController
{
    protected $modelName = DocenteModel::class;
    protected $format    = 'json';

    /**
     * GET /api/docente
     * Este controlador ahora está libre de la lógica de login. 
     * Puedes usarlo para endpoints propios del docente (ej. obtener su perfil).
     */
    public function index()
    {
        // Ejemplo: retornar lista de docentes o el perfil del docente autenticado.
        return $this->respond([
            'message' => 'Ruta de docente lista para usarse.'
        ]);
    }
}