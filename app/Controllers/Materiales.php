<?php
namespace App\Controllers;

use App\Models\MaterialModel;
use CodeIgniter\RESTful\ResourceController;

class Materiales extends ResourceController
{
    protected $format = 'json';

    /**
     * GET /api/materiales
     * Consultar materiales con su categoría.
     */
    public function index()
    {
        return $this->respond(
            model(MaterialModel::class)->conCategoria()
        );
    }

    /**
     * POST /api/materiales
     * Solo docente, según el filtro configurado en Routes.php.
     */
    public function create()
    {
        // Recibir los campos del FormData.
        $datos = [
            'nombre'       => trim((string) $this->request->getPost('nombre')),
            'categoria_id' => trim((string) $this->request->getPost('categoria_id')),
            'nivel'        => trim((string) $this->request->getPost('nivel')),
        ];

        $reglas = [
            'nombre'       => 'required|max_length[150]',
            'categoria_id' => 'required|is_natural_no_zero|is_not_unique[categorias.id]',
            'nivel'        => 'required|in_list[1,2,3]',
        ];

        $mensajes = [
            'nombre' => [
                'required' => 'Escribe el nombre del material.',
                'max_length' => 'El nombre no puede superar 150 caracteres.',
            ],
            'categoria_id' => [
                'required' => 'Selecciona una categoría.',
                'is_natural_no_zero' => 'La categoría no es válida.',
                'is_not_unique' => 'La categoría seleccionada no existe.',
            ],
            'nivel' => [
                'required' => 'Selecciona un nivel.',
                'in_list' => 'El nivel debe ser 1, 2 o 3.',
            ],
        ];

        if (! $this->validateData($datos, $reglas, $mensajes)) {
            return $this->fail(
                implode(' ', $this->validator->getErrors()),
                400
            );
        }

        // Obtener el archivo enviado desde React.
        $archivo = $this->request->getFile('archivo');

        if (! $archivo || ! $archivo->isValid()) {
            return $this->fail(
                'Selecciona un archivo válido para subir.',
                400
            );
        }

        if ($archivo->hasMoved()) {
            return $this->fail(
                'El archivo ya fue procesado.',
                400
            );
        }

        // Tamaño máximo: 10 MB.
        if ($archivo->getSize() > 10 * 1024 * 1024) {
            return $this->fail(
                'El archivo no puede superar los 10 MB.',
                400
            );
        }

        // Validar el tipo real detectado por el servidor.
        $tiposPermitidos = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        ];

        $mime = $archivo->getMimeType();

        if (! isset($tiposPermitidos[$mime])) {
            return $this->fail(
                'Tipo de archivo no permitido. Usa imágenes JPG, PNG, GIF, WEBP, PDF, DOC, DOCX, PPT o PPTX.',
                400
            );
        }

        // Crear la carpeta pública de destino.
        $directorio = FCPATH . 'uploads/materiales/';

        if (
            ! is_dir($directorio)
            && ! mkdir($directorio, 0755, true)
            && ! is_dir($directorio)
        ) {
            return $this->failServerError(
                'No se pudo crear la carpeta para los archivos.'
            );
        }

        if (! is_writable($directorio)) {
            return $this->failServerError(
                'La carpeta de archivos no tiene permisos de escritura.'
            );
        }

        // Nombre aleatorio y extensión validada por el servidor.
        $nombreArchivo = bin2hex(random_bytes(16))
            . '.'
            . $tiposPermitidos[$mime];

        try {
            $archivo->move($directorio, $nombreArchivo);
        } catch (\Throwable $e) {
            log_message('error', 'Error al guardar archivo: ' . $e->getMessage());

            return $this->failServerError(
                'No se pudo guardar el archivo.'
            );
        }

        // Guardar la URL pública en la columna existente "imagen".
        $urlArchivo = base_url(
            'uploads/materiales/' . $nombreArchivo
        );

        $materialModel = model(MaterialModel::class);

        try {
            $id = $materialModel->insert([
                'nombre'       => $datos['nombre'],
                'categoria_id' => (int) $datos['categoria_id'],
                'nivel'        => (int) $datos['nivel'],
                'imagen'       => $urlArchivo,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error al registrar material: ' . $e->getMessage());
            $id = false;
        }

        // Evitar dejar un archivo huérfano si falla el registro.
        if (! $id) {
            if (is_file($directorio . $nombreArchivo)) {
                unlink($directorio . $nombreArchivo);
            }

            return $this->failServerError(
                'El archivo se subió, pero no se pudo registrar el material.'
            );
        }

        return $this->respondCreated([
            'status'  => 201,
            'id'      => $id,
            'imagen'  => $urlArchivo,
            'message' => 'Material guardado correctamente.',
        ]);
    }
}