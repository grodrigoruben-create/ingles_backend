<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Crea un docente y un alumno de prueba con la contraseña guardada como hash.
 *   php spark db:seed UsuariosDemoSeeder
 * Docente: clave DOC000001  / contraseña docente123
 * Alumno:  matrícula 123456789 / contraseña alumno123
 * Bórralos antes de usar el sistema con datos reales.
 */
class UsuariosDemoSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('docente')->where('clave_docente', 'DOC000001')->countAllResults() === 0) {
            $this->db->table('docente')->insert([
                'clave_docente'   => 'DOC000001',
                'nombre_completo' => 'Docente de Prueba',
                'password'        => '', // la columna existe, pero ya no se guarda la contraseña en texto plano
                'hash'            => password_hash('docente123', PASSWORD_DEFAULT),
            ]);
        }

        if ($this->db->table('alumnos')->where('matricula', 123456789)->countAllResults() === 0) {
            $this->db->table('alumnos')->insert([
                'nombre_completo' => 'Alumno de Prueba',
                'matricula'       => 123456789,
                'correo'          => 'alumno@example.com',
                'carrera'         => 'Ingeniería en Sistemas',
                'contrasena'      => '',
                'hash'            => password_hash('alumno123', PASSWORD_DEFAULT),
            ]);
        }
    }
}
