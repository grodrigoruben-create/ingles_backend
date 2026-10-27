<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Login unificado 
$routes->post('auth/login', 'Auth::login');

// ¡NUEVA RUTA PÚBLICA PARA EL REGISTRO DE ANDROID!
// Cambia 'Alumnos::create' por el nombre real de tu función en el controlador si se llama distinto.
$routes->post('api/alumno', 'Alumnos::create'); 

// Rutas de la API Web (React)
$routes->get('api/materiales', 'Materiales::index', ['filter' => 'jwt']);
// ... (resto de tus rutas)
$routes->get('/', function() {
    return response()->setJSON(['status' => 'API de English Cards funcionando correctamente']);
});

$routes->options('(:any)', static function () {});

// Login unificado (el que modificamos antes)
$routes->post('auth/login', 'Auth::login');

// Rutas de la API Web (React)
$routes->get('api/materiales', 'Materiales::index', ['filter' => 'jwt']);
$routes->post('api/materiales', 'Materiales::create', ['filter' => 'jwt:docente']); // Protegido!
$routes->get('api/categorias', 'Categorias::index', ['filter' => 'jwt']);
$routes->get('api/alumnos', 'Alumnos::index', ['filter' => 'jwt:docente']);
$routes->get('api/calificaciones', 'Calificaciones::index', ['filter' => 'jwt']);

// Si realmente necesitas estas rutas aparte para la App Móvil, agrégalas con cuidado:
$routes->group('api/movil', ['filter' => 'jwt'], function($routes) {
    $routes->get('flashcards/diarias', 'Api\Flashcards::getDiarias');
    $routes->post('evaluaciones/guardar', 'Api\Evaluaciones::guardarPuntuacion');
    // Si vas a dejar esto, protégelo con el filtro de docente:
    // $routes->post('materiales/crear', 'Api\Materiales::crear', ['filter' => 'jwt:docente']);
});