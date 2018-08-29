<?php

// Ensure UCI has shared libs accessible
putenv ( 'LD_LIBRARY_PATH=/usr/local/lib' );

// Composer autoload
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../bootstrap.php';

require_once '../api/Node.php';
require_once '../api/Updates.php';
require_once '../api/Vpn.php';
require_once '../api/WebApp.php';
require_once '../api/Wifi.php';

use Luracast\Restler\Resources;
use Luracast\Restler\Defaults;
Resources::$useFormatAsExtension = false;

use Luracast\Restler\Restler;
use Luracast\Restler\Format\HtmlFormat;

// debug
if (DEBUG_ENABLED) {
    $r = new Restler(false);
}
else {
    // production
    $r = new Restler(true);
}

$r->setSupportedFormats('JsonFormat', 'HtmlFormat');

$r->addAPIClass('Node');
$r->addAPIClass('Updates');
$r->addAPIClass('Vpn');
$r->addAPIClass('WebApp','');
$r->addAPIClass('Wifi');

$r->handle();

?>
