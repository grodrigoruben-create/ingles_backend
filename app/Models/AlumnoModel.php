<?php

namespace App\Models;

use CodeIgniter\Model;

class AlumnoModel extends Model
{
    protected $table         = 'alumnos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nombre_completo', 'matricula', 'correo', 'carrera', 'contrasena', 'hash'];

    public function listaSegura(): array
    {
        return $this->select('id, nombre_completo, matricula, correo, carrera')
            ->orderBy('nombre_completo', 'ASC')
            ->findAll();
    }

    /**
     * Verifica las credenciales del alumno.
     */
    public function verifica(string $matricula, string $password)
    {
        // Buscamos al alumno por su matrícula
        $alumno = $this->where('matricula', $matricula)->first();

        if (!$alumno) {
            return false;
        }

        // Verificamos la contraseña (nota: tu columna se llama 'contrasena')
        if (password_verify($password, $alumno['contrasena'])) {
            return $alumno;
        }

        return false;
    }
}