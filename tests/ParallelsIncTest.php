<?php

declare(strict_types=1);

namespace Detain\MyAdminParallels\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the parallels.inc.php procedural functions.
 *
 * These tests verify function existence, signatures, and static analysis
 * without calling into external services or database code.
 *
 * @covers ::activate_parallels
 * @covers ::deactivate_parallels
 * @covers ::deactivate_parallels_by_key
 */
class ParallelsIncTest extends TestCase
{
    /**
     * Ensure the include file is loaded once for all tests.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $incFile = dirname(__DIR__) . '/src/parallels.inc.php';
        if (!function_exists('activate_parallels')) {
            // Define stubs for external functions used by the include file
            if (!function_exists('myadmin_log')) {
                function_exists('myadmin_log') || true; // already defined in bootstrap
            }
            require_once $incFile;
        }
    }

    /**
     * Tests that the activate_parallels function is defined.
     *
     * @return void
     */
    public function testActivateParallelsFunctionExists(): void
    {
        $this->assertTrue(
            function_exists('activate_parallels'),
            'Function activate_parallels() should be defined'
        );
    }

    /**
     * Tests that the deactivate_parallels function is defined.
     *
     * @return void
     */
    public function testDeactivateParallelsFunctionExists(): void
    {
        $this->assertTrue(
            function_exists('deactivate_parallels'),
            'Function deactivate_parallels() should be defined'
        );
    }

    /**
     * Tests that the deactivate_parallels_by_key function is defined.
     *
     * @return void
     */
    public function testDeactivateParallelsByKeyFunctionExists(): void
    {
        $this->assertTrue(
            function_exists('deactivate_parallels_by_key'),
            'Function deactivate_parallels_by_key() should be defined'
        );
    }

    /**
     * Tests that activate_parallels accepts exactly 3 parameters with the last being optional.
     *
     * @return void
     */
    public function testActivateParallelsParameterCount(): void
    {
        $ref = new \ReflectionFunction('activate_parallels');
        $params = $ref->getParameters();

        $this->assertCount(3, $params, 'activate_parallels should have 3 parameters');
        $this->assertSame('ipAddress', $params[0]->getName());
        $this->assertSame('type', $params[1]->getName());
        $this->assertSame('addons', $params[2]->getName());
    }

    /**
     * Tests that activate_parallels has a default value for the addons parameter.
     *
     * @return void
     */
    public function testActivateParallelsAddonsDefaultValue(): void
    {
        $ref = new \ReflectionFunction('activate_parallels');
        $params = $ref->getParameters();

        $this->assertTrue(
            $params[2]->isDefaultValueAvailable(),
            'The addons parameter should have a default value'
        );
        $this->assertSame(
            '',
            $params[2]->getDefaultValue(),
            'The addons parameter default should be an empty string'
        );
    }

    /**
     * Tests that activate_parallels requires at least 2 parameters.
     *
     * @return void
     */
    public function testActivateParallelsRequiredParameterCount(): void
    {
        $ref = new \ReflectionFunction('activate_parallels');
        $this->assertSame(
            2,
            $ref->getNumberOfRequiredParameters(),
            'activate_parallels should require exactly 2 parameters'
        );
    }

    /**
     * Tests that deactivate_parallels accepts exactly 1 parameter.
     *
     * @return void
     */
    public function testDeactivateParallelsParameterCount(): void
    {
        $ref = new \ReflectionFunction('deactivate_parallels');
        $params = $ref->getParameters();

        $this->assertCount(1, $params, 'deactivate_parallels should have 1 parameter');
        $this->assertSame('ipAddress', $params[0]->getName());
    }

    /**
     * Tests that deactivate_parallels_by_key accepts exactly 1 parameter.
     *
     * @return void
     */
    public function testDeactivateParallelsByKeyParameterCount(): void
    {
        $ref = new \ReflectionFunction('deactivate_parallels_by_key');
        $params = $ref->getParameters();

        $this->assertCount(1, $params, 'deactivate_parallels_by_key should have 1 parameter');
        $this->assertSame('key', $params[0]->getName());
    }

    /**
     * Tests that deactivate_parallels requires exactly 1 parameter.
     *
     * @return void
     */
    public function testDeactivateParallelsRequiredParameterCount(): void
    {
        $ref = new \ReflectionFunction('deactivate_parallels');
        $this->assertSame(
            1,
            $ref->getNumberOfRequiredParameters(),
            'deactivate_parallels should require exactly 1 parameter'
        );
    }

    /**
     * Tests that deactivate_parallels_by_key requires exactly 1 parameter.
     *
     * @return void
     */
    public function testDeactivateParallelsByKeyRequiredParameterCount(): void
    {
        $ref = new \ReflectionFunction('deactivate_parallels_by_key');
        $this->assertSame(
            1,
            $ref->getNumberOfRequiredParameters(),
            'deactivate_parallels_by_key should require exactly 1 parameter'
        );
    }

    /**
     * Tests that all three functions are defined in the expected source file.
     *
     * @return void
     */
    public function testFunctionsDefinedInCorrectFile(): void
    {
        $expectedFile = realpath(dirname(__DIR__) . '/src/parallels.inc.php');

        $functions = ['activate_parallels', 'deactivate_parallels', 'deactivate_parallels_by_key'];
        foreach ($functions as $functionName) {
            $ref = new \ReflectionFunction($functionName);
            $this->assertSame(
                $expectedFile,
                realpath($ref->getFileName()),
                "Function {$functionName}() should be defined in parallels.inc.php"
            );
        }
    }

    /**
     * Tests that activate_parallels has no type hints on parameters (legacy PHP style).
     *
     * @return void
     */
    public function testActivateParallelsParametersAreUntyped(): void
    {
        $ref = new \ReflectionFunction('activate_parallels');
        foreach ($ref->getParameters() as $param) {
            $this->assertNull(
                $param->getType(),
                "Parameter \${$param->getName()} should not have a type hint"
            );
        }
    }

    /**
     * Tests that activate_parallels does not have a return type declaration.
     *
     * @return void
     */
    public function testActivateParallelsNoReturnType(): void
    {
        $ref = new \ReflectionFunction('activate_parallels');
        $this->assertFalse($ref->hasReturnType());
    }

    /**
     * Tests that deactivate_parallels does not have a return type declaration.
     *
     * @return void
     */
    public function testDeactivateParallelsNoReturnType(): void
    {
        $ref = new \ReflectionFunction('deactivate_parallels');
        $this->assertFalse($ref->hasReturnType());
    }

    /**
     * Tests that deactivate_parallels_by_key does not have a return type declaration.
     *
     * @return void
     */
    public function testDeactivateParallelsByKeyNoReturnType(): void
    {
        $ref = new \ReflectionFunction('deactivate_parallels_by_key');
        $this->assertFalse($ref->hasReturnType());
    }

    /**
     * Tests that none of the functions are generators.
     *
     * @return void
     */
    public function testFunctionsAreNotGenerators(): void
    {
        $functions = ['activate_parallels', 'deactivate_parallels', 'deactivate_parallels_by_key'];
        foreach ($functions as $functionName) {
            $ref = new \ReflectionFunction($functionName);
            $this->assertFalse(
                $ref->isGenerator(),
                "Function {$functionName}() should not be a generator"
            );
        }
    }

    /**
     * Tests that none of the functions are variadic.
     *
     * @return void
     */
    public function testFunctionsAreNotVariadic(): void
    {
        $functions = ['activate_parallels', 'deactivate_parallels', 'deactivate_parallels_by_key'];
        foreach ($functions as $functionName) {
            $ref = new \ReflectionFunction($functionName);
            $this->assertFalse(
                $ref->isVariadic(),
                "Function {$functionName}() should not be variadic"
            );
        }
    }

    /**
     * Tests that none of the functions are closures.
     *
     * @return void
     */
    public function testFunctionsAreNotClosures(): void
    {
        $functions = ['activate_parallels', 'deactivate_parallels', 'deactivate_parallels_by_key'];
        foreach ($functions as $functionName) {
            $ref = new \ReflectionFunction($functionName);
            $this->assertFalse(
                $ref->isClosure(),
                "Function {$functionName}() should not be a closure"
            );
        }
    }

    /**
     * Tests that the include file references the correct Parallels class namespace.
     *
     * @return void
     */
    public function testIncFileReferencesCorrectNamespace(): void
    {
        $content = file_get_contents(dirname(__DIR__) . '/src/parallels.inc.php');
        $this->assertStringContainsString(
            'Detain\\Parallels\\Parallels',
            $content,
            'The include file should reference the Detain\\Parallels\\Parallels class'
        );
    }

    /**
     * Tests that the include file uses the Parallels class via use statement.
     *
     * @return void
     */
    public function testIncFileHasUseStatement(): void
    {
        $content = file_get_contents(dirname(__DIR__) . '/src/parallels.inc.php');
        $this->assertStringContainsString(
            'use \\Detain\\Parallels\\Parallels',
            $content,
            'The include file should have a use statement for Parallels'
        );
    }
}
