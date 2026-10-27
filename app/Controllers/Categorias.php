<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use CodeIgniter\RESTful\ResourceController;

class Categorias extends ResourceController
{
    protected $format = 'json';

    /** GET /api/categorias */
    public function index()
    {
        return $this->respond(
            model(CategoriaModel::class)->select('id, nombre, grado_dificultad')->orderBy('nombre', 'ASC')->findAll()
        );
    }
}
