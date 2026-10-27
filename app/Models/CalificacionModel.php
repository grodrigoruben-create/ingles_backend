<?php

namespace App\Models;

use CodeIgniter\Model;

class CalificacionModel extends Model
{
    protected $table         = 'calificaciones';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['alumno_id', 'nivel', 'puntuacion', 'tipo_prueba'];
    protected $useTimestamps = true;

    /** Calificaciones con el nombre del alumno; si se da $alumnoId, solo las de ese alumno. */
    public function conAlumno(?int $alumnoId = null): array
    {
        $this->select('calificaciones.id, calificaciones.alumno_id, alumnos.nombre_completo, '
                . 'calificaciones.nivel, calificaciones.puntuacion, calificaciones.tipo_prueba, '
                . 'calificaciones.created_at')
            ->join('alumnos', 'alumnos.id = calificaciones.alumno_id');

        if ($alumnoId !== null) {
            $this->where('calificaciones.alumno_id', $alumnoId);
        }

        return $this->orderBy('calificaciones.created_at', 'DESC')->findAll();
    }
}
