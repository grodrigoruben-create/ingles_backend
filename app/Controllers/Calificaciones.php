<?php

namespace App\Controllers;

use App\Libraries\JwtService;
use App\Models\CalificacionModel;
use CodeIgniter\RESTful\ResourceController;

class Calificaciones extends ResourceController
{
    protected $format = 'json';

    /**
     * GET /api/calificaciones
     * El docente ve todas; el alumno solo las suyas (el id sale del token, no de la petición).
     */
    public function index()
    {
        $payload  = (new JwtService())->desdeRequest($this->request);
        $alumnoId = ($payload->rol ?? '') === 'alumno' ? (int) $payload->sub : null;

        return $this->respond(model(CalificacionModel::class)->conAlumno($alumnoId));
    }
}
