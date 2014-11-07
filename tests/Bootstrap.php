<?php
/**
* Zend Framework (http://framework.zend.com/)
*
* @link http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
* @package Zend
*/

/*
* Load the user-defined test configuration file, if it exists; otherwise, load
* the default configuration.
*/
if (is_readable($zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
require_once $zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
require_once $zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}

/**
* Setup autoloading
*/
include __DIR__ . '/../vendor/autoload.php';