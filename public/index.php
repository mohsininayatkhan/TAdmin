<?php
/**
 * Forge - PHP Framework
 *
 * @package Forge
 * @version
 * @author  John Tipping <john.tipping@supanet.net.uk>
 *          Ashley Wilson <awilson@supanet.net.uk>
 */

error_reporting(-1 & ~E_DEPRECATED);

/**
 * constant for app startup time
 */
define('FORGE_START', microtime(true));

require_once '../engine.php';