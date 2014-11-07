<?php
/**
 * Fing Radius Auth ZF2 Adapter
 *
 * @link http://github.com/
 * @copyright Copyright (c) 2014 Felipe Zipitria
 * @license MIT License
 * @package Fing
 */

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if (is_readable('TestConfiguration.php')) {
    require_once 'TestConfiguration.php';
} else {
    require_once 'TestConfiguration.php.dist';
}

/**
 * Setup autoloading
 */
include __DIR__ . '/../vendor/autoload.php';