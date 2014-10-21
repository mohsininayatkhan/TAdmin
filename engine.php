<?php

/**
 * Forge - PHP Framework
 *
 * @package  Forge
 * @version  2.0
 * @author   John Tipping <john.tipping@supanet.net.uk>
 * @author   Ashley Wilson <awilson@supanet.net.uk>
 */

// Stop timeout
ini_set('default_socket_timeout', 6000);

// -------------------------------------------------
// -------------- Set directory paths --------------
// -------------------------------------------------
chdir(__DIR__);

// Root
$paths['root'] = __DIR__;

// Application
$paths['app'] = __DIR__ .'/app/';

// Forge framework
$paths['sys'] = __DIR__ .'/forge/';

// Temp/writable files
$paths['storage'] = __DIR__ .'/tmp/';

// Public folder
$paths['public'] = __DIR__ .'/public/';

// Logs
$paths['logs'] = $paths['storage'] .'logs/';

// Modules
$paths['module'] = $paths['app'] .'modules/';

// Vendor
$paths['vendor'] = __DIR__ .'/vendor/';

// Load the paths onto the Core
require_once $paths['sys'] .'core.php';
Forge\Core::setPaths($paths);

// -------------------------------------------------
// ----------------- Environments ------------------
// -------------------------------------------------

$environment = array(
    'dev' => array('*.dev', '*.dev.netline.net.uk'),
    'live' => array('*.prov')
);

Forge\Core::setEnvironments($environment);

// -------------------------------------------------
// ------------ Register autoloader ----------------
// -------------------------------------------------
require_once $paths['sys'] .'autoload.php';

Forge\Autoload::register();

Forge\Autoload::dir(Forge\Config::get('app.autoload.paths'));
Forge\Autoload::map(Forge\Config::get('app.autoload.class'));

// -------------------------------------------------
// ----------- Register timezone ASAP --------------
// -------------------------------------------------
if (Forge\Config::get('app.timezone')) date_default_timezone_set(Forge\Config::get('app.timezone'));

// -------------------------------------------------
// ----------------- Error settings ----------------
// -------------------------------------------------
ini_set('display_errors', 1);

$logging = Forge\Config::get('error.logging');
$vbose   = Forge\Config::get('error.vbose');

// Setup error handlers
set_exception_handler(function($e) use ($logging, $vbose) {
   // Log the error?
   if ($logging) Forge\Log::write(Forge\Log::DEBUG, $e->getMessage() .'->'.$e->getTraceAsString());
   
   $error = ($vbose) ? array('error' => $e->getMessage() .' @ line #'. $e->getLine() .' in '. $e->getFile(), 'exception' => $e) : null;
   
   Forge\Response::error(500, $error);
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {
   throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}, -1 & ~E_DEPRECATED);

// -------------------------------------------------
// ------- Composer and 3rd party libratries -------
// -------------------------------------------------
require_once("vendor/autoload.php");

// -------------------------------------------------
// - Set the enviroment array depending on the URL -
// -------------------------------------------------
$environments = array(
   'local' => array('http://localhost*', '*.dev')
);

// -------------------------------------------------
// ------------------ GO GO GO ---------------------
// -------------------------------------------------
Forge\Core::startup();

