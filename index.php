<?php

/**
 * 
 * @desc - Initial load file to initiate the MVC Pattern
 */
$raiseMVC = require_once './inc/config.php';
$rootConnection = [];
$requestApiToken = '';

$root = new \inc\Raise();
$root->initApp();