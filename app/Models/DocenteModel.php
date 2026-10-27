<?php

namespace App\Models;

use CodeIgniter\Model;

class DocenteModel extends Model
{
    protected $table         = 'docente';
    protected $primaryKey    = 'id_docente';
    protected $returnType    = 'array';
    protected $allowedFields = ['clave_docente', 'nombre_completo', 'password', 'hash'];

    /**
     * Verifica las credenciales del docente.
     */
    public function verifica(string $clave, string $password)
    {
        // Buscamos al docente por su clave
        $docente = $this->where('clave_docente', $clave)->first();

        if (!$docente) {
            return false; // El docente no existe
        }

        // Verificamos la contraseña (asumiendo que usaste password_hash() al registrarlo)
        // Si las tienes en texto plano temporalmente, cambia esto a: if ($docente['password'] === $password)
        if (password_verify($password, $docente['password'])) {
            return $docente;
        }

        return false;
    }
}