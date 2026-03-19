<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap file for myadmin-parallels-licensing tests.
 *
 * Defines stub functions for external dependencies that are not available
 * in the test environment, then loads the Composer autoloader.
 */

// Stub external functions used by the source code
if (!function_exists('myadmin_log')) {
    /**
     * Stub for myadmin_log used in production code.
     *
     * @return void
     */
    function myadmin_log(): void
    {
    }
}

if (!function_exists('get_service_define')) {
    /**
     * Stub for get_service_define used in production code.
     *
     * @param string $name
     * @return int
     */
    function get_service_define(string $name): int
    {
        $defines = [
            'PARALLELS' => 1,
        ];
        return $defines[$name] ?? 0;
    }
}

if (!function_exists('function_requirements')) {
    /**
     * Stub for function_requirements used in production code.
     *
     * @return void
     */
    function function_requirements(): void
    {
    }
}

if (!function_exists('request_log')) {
    /**
     * Stub for request_log used in production code.
     *
     * @return void
     */
    function request_log(): void
    {
    }
}

if (!function_exists('get_module_settings')) {
    /**
     * Stub for get_module_settings used in production code.
     *
     * @param string $module
     * @return array
     */
    function get_module_settings(string $module): array
    {
        return ['TABLE' => 'licenses'];
    }
}

// Load Composer autoloader
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../../vendor/autoload.php',
];

foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        break;
    }
}
