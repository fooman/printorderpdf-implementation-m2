<?php

use Fooman\PhpunitBridge\Magento2UnitTestSetup;
require (__DIR__.'/../../vendor/autoload.php');
$unitTestSetup = new Magento2UnitTestSetup();
$unitTestSetup->run();
