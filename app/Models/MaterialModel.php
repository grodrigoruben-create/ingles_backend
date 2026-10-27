<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table         = 'materiales';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['categoria_id', 'nombre', 'imagen', 'nivel'];
    protected $useTimestamps = true;

    /** Materiales junto con el nombre de su categoría. */
    public function conCategoria(): array
    {
        return $this->select('materiales.id, materiales.nombre, materiales.imagen, materiales.nivel, '
                . 'materiales.categoria_id, categorias.nombre AS categoria')
            ->join('categorias', 'categorias.id = materiales.categoria_id')
            ->orderBy('materiales.id', 'DESC')
            ->findAll();
    }
}
