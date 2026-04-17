<?php

ob_start();
require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;
use Source\Core\Session;
use Source\Models\Settings\Submenus;

$session = new Session();
$route = new Router(url(), "@");

$route->namespace("Source\App");
$route->group(null);
$route->get("/", "AppLauncher@index");
$route->get("/dashboard", "AppLauncher@index");

/**
 * 
 * *** ROTAS DE AUTENTICAÇÃO ***    
 */
$route->group('/auth');
$route->get("/login", "Authentic@login");
$route->post("/login", "Authentic@login");
$route->get("/logout", "Authentic@logout");
$route->get("/recover", "Authentic@recover");
$route->post("/recover", "Authentic@recover");

/**
 * ROTAS ESTÁTICAS DE CONFIGURAÇÕES (Setting)
 * Menus principais, submenus, grupos, numeração e instituição
 */
$route->group('/setting');
$route->get('/', 'Setting@index');

$route->get('/menus', 'Setting@menus');
$route->post('/menus', 'Setting@menus');

$route->get('/submenus', 'Setting@submenus');
$route->post('/submenus', 'Setting@submenus');

$route->get('/groups', 'Setting@groups');
$route->post('/groups', 'Setting@groups');

$route->get('/numerador', 'Setting@numerador');
$route->post('/numerador', 'Setting@numerador');

$route->get('/institution', 'Setting@institution');
$route->post('/institution', 'Setting@institution');

/**
 * ROTAS ESTÁTICAS DO MÓDULO CLÍNICO
 */
$route->group('/desk');
$route->get('/patients', 'Clinical@patients');
$route->post('/patients', 'Clinical@patients');
$route->get('/register', 'Clinical@register');

$route->group('/clinical');
$route->get('/patients', 'Clinical@patients');
$route->post('/patients', 'Clinical@patients');
$route->get('/attendance', 'Clinical@attendance');
$route->get('/chart', 'Clinical@chart');
$route->get('/evolution', 'Clinical@evolution');
$route->get('/services', 'Clinical@services');
$route->get('/services/search', 'Clinical@serviceSearch');

$route->group('/agenda');
$route->get('/today', 'Agenda@today');
$route->get('/week', 'Agenda@week');
$route->get('/create', 'Agenda@create');
$route->get('/availability', 'Agenda@availability');

$route->group('/finance');
$route->get('/overview', 'Finance@overview');
$route->get('/revenue', 'Finance@revenue');
$route->get('/expenses', 'Finance@expenses');
$route->get('/reports', 'Finance@reports');

$route->group('/pharmacy');
$route->get('/desk', 'Pharmacy@desk');
$route->get('/products', 'Pharmacy@products');
$route->get('/search', 'Pharmacy@search');

$route->group('/fiscal');
$route->get('/overview', 'Fiscal@overview');
$route->get('/saft', 'Fiscal@saft');
$route->get('/series', 'Fiscal@series');
$route->get('/certificates', 'Fiscal@certificates');
$route->get('/hash-chain', 'Fiscal@hashChain');
$route->get('/documents', 'Fiscal@documents');
$route->get('/audit', 'Fiscal@audit');
$route->get('/reports', 'Fiscal@reports');

$route->group('/search');
$route->get('/global', 'Search@global');

$route->group('/reports');
$route->get('/overview', 'Reports@overview');

/**
 * **************************************
 *** BUSCAR METÓDOS NA TABELA SUBMENUS***
 ****************************************
 */
$rotah = (!empty($_GET['route']) ? substr($_GET['route'], 1) : (!empty($_POST['route']) ? substr($_POST['route'], 1) : null));
$requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$requestRoute = $_GET['route'] ?? $_POST['route'] ?? null;

if (!empty($rotah) && $urlName = (new Submenus())->findByName($rotah)) {
    $route->group(null);
    if ($requestMethod === 'GET' && !empty($requestRoute)) {
        $route->get($requestRoute, $urlName->metods);
    }
    if ($requestMethod === 'POST' && !empty($requestRoute)) {
        $route->post($requestRoute, $urlName->metods);
    }
}
/**
 * ERROR ROUTE
 */


$route->group("/ops")->namespace("Source\App");
$route->get("/{errcode}", "AppLauncher@error");
$route->dispatch();
if ($route->error()) {
    $route->redirect("/ops/{$route->error()}");
}

ob_end_flush();
