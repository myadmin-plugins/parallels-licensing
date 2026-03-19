<?php

declare(strict_types=1);

namespace Detain\MyAdminParallels\Tests;

use Detain\MyAdminParallels\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Tests for the Plugin class.
 *
 * @covers \Detain\MyAdminParallels\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * Tests that the Plugin class can be instantiated.
     *
     * @return void
     */
    public function testPluginCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    /**
     * Tests that the Plugin class exists and is in the correct namespace.
     *
     * @return void
     */
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Plugin::class));
    }

    /**
     * Tests that the static $name property is set correctly.
     *
     * @return void
     */
    public function testNameProperty(): void
    {
        $this->assertSame('Parallels Licensing', Plugin::$name);
    }

    /**
     * Tests that the static $description property is set correctly.
     *
     * @return void
     */
    public function testDescriptionProperty(): void
    {
        $this->assertIsString(Plugin::$description);
        $this->assertStringContainsString('Parallels', Plugin::$description);
        $this->assertStringContainsString('parallels.com', Plugin::$description);
    }

    /**
     * Tests that the static $help property is a non-empty string.
     *
     * @return void
     */
    public function testHelpProperty(): void
    {
        $this->assertIsString(Plugin::$help);
        $this->assertNotEmpty(Plugin::$help);
    }

    /**
     * Tests that the static $module property is set to 'licenses'.
     *
     * @return void
     */
    public function testModuleProperty(): void
    {
        $this->assertSame('licenses', Plugin::$module);
    }

    /**
     * Tests that the static $type property is set to 'service'.
     *
     * @return void
     */
    public function testTypeProperty(): void
    {
        $this->assertSame('service', Plugin::$type);
    }

    /**
     * Tests that getHooks returns an array with the expected keys.
     *
     * @return void
     */
    public function testGetHooksReturnsArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertIsArray($hooks);
        $this->assertNotEmpty($hooks);
    }

    /**
     * Tests that getHooks contains the expected event hook keys.
     *
     * @return void
     */
    public function testGetHooksContainsExpectedKeys(): void
    {
        $hooks = Plugin::getHooks();

        $expectedKeys = [
            'licenses.settings',
            'licenses.activate',
            'licenses.reactivate',
            'licenses.deactivate',
            'licenses.deactivate_ip',
            'licenses.deactivate_key',
            'function.requirements',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $hooks, "Missing hook key: {$key}");
        }
    }

    /**
     * Tests that getHooks values are valid callable arrays.
     *
     * @return void
     */
    public function testGetHooksValuesAreCallableArrays(): void
    {
        $hooks = Plugin::getHooks();

        foreach ($hooks as $key => $value) {
            $this->assertIsArray($value, "Hook '{$key}' value should be an array");
            $this->assertCount(2, $value, "Hook '{$key}' should have exactly 2 elements");
            $this->assertSame(Plugin::class, $value[0], "Hook '{$key}' class should be Plugin");
            $this->assertIsString($value[1], "Hook '{$key}' method name should be a string");
        }
    }

    /**
     * Tests that getHooks maps activate and reactivate to the same handler.
     *
     * @return void
     */
    public function testActivateAndReactivateShareSameHandler(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame($hooks['licenses.activate'], $hooks['licenses.reactivate']);
    }

    /**
     * Tests that getHooks maps deactivate and deactivate_ip to the same handler.
     *
     * @return void
     */
    public function testDeactivateAndDeactivateIpShareSameHandler(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame($hooks['licenses.deactivate'], $hooks['licenses.deactivate_ip']);
    }

    /**
     * Tests that each hook method referenced in getHooks actually exists.
     *
     * @return void
     */
    public function testAllHookMethodsExist(): void
    {
        $hooks = Plugin::getHooks();

        foreach ($hooks as $key => $value) {
            $this->assertTrue(
                method_exists(Plugin::class, $value[1]),
                "Method Plugin::{$value[1]}() does not exist (hook: {$key})"
            );
        }
    }

    /**
     * Tests that hook handler methods are public and static.
     *
     * @return void
     */
    public function testHookMethodsArePublicAndStatic(): void
    {
        $hooks = Plugin::getHooks();
        $reflection = new ReflectionClass(Plugin::class);

        $checkedMethods = [];
        foreach ($hooks as $key => $value) {
            $methodName = $value[1];
            if (in_array($methodName, $checkedMethods, true)) {
                continue;
            }
            $checkedMethods[] = $methodName;

            $method = $reflection->getMethod($methodName);
            $this->assertTrue(
                $method->isPublic(),
                "Method Plugin::{$methodName}() should be public"
            );
            $this->assertTrue(
                $method->isStatic(),
                "Method Plugin::{$methodName}() should be static"
            );
        }
    }

    /**
     * Tests that event handler methods accept a GenericEvent parameter.
     *
     * @return void
     */
    public function testEventHandlerMethodSignatures(): void
    {
        $reflection = new ReflectionClass(Plugin::class);

        $eventMethods = [
            'getActivate',
            'getDeactivate',
            'getDeactivateKey',
            'getChangeIp',
            'getRequirements',
            'getSettings',
        ];

        foreach ($eventMethods as $methodName) {
            $method = $reflection->getMethod($methodName);
            $params = $method->getParameters();
            $this->assertCount(
                1,
                $params,
                "Method Plugin::{$methodName}() should accept exactly 1 parameter"
            );
            $paramType = $params[0]->getType();
            $this->assertNotNull(
                $paramType,
                "Method Plugin::{$methodName}() parameter should be type-hinted"
            );
            $this->assertSame(
                GenericEvent::class,
                $paramType->getName(),
                "Method Plugin::{$methodName}() parameter should be GenericEvent"
            );
        }
    }

    /**
     * Tests that the constructor takes no parameters.
     *
     * @return void
     */
    public function testConstructorHasNoParameters(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertCount(0, $constructor->getParameters());
    }

    /**
     * Tests that all static properties are accessible and have correct types.
     *
     * @return void
     */
    public function testStaticPropertiesTypes(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $expectedProperties = [
            'name' => 'string',
            'description' => 'string',
            'help' => 'string',
            'module' => 'string',
            'type' => 'string',
        ];

        foreach ($expectedProperties as $propName => $expectedType) {
            $this->assertTrue(
                $reflection->hasProperty($propName),
                "Plugin should have static property \${$propName}"
            );
            $prop = $reflection->getProperty($propName);
            $this->assertTrue(
                $prop->isStatic(),
                "Property \${$propName} should be static"
            );
            $this->assertTrue(
                $prop->isPublic(),
                "Property \${$propName} should be public"
            );
            $value = $prop->getValue();
            $this->assertSame(
                $expectedType,
                gettype($value),
                "Property \${$propName} should be of type {$expectedType}"
            );
        }
    }

    /**
     * Tests that getHooks uses the module property for key prefixes.
     *
     * @return void
     */
    public function testGetHooksUsesModulePropertyForPrefix(): void
    {
        $hooks = Plugin::getHooks();
        $module = Plugin::$module;

        $moduleKeys = array_filter(
            array_keys($hooks),
            function ($key) use ($module) {
                return strpos($key, $module . '.') === 0;
            }
        );

        // All keys except 'function.requirements' should use the module prefix
        $this->assertCount(count($hooks) - 1, $moduleKeys);
        $this->assertArrayHasKey('function.requirements', $hooks);
    }

    /**
     * Tests that getActivate method references the correct handler.
     *
     * @return void
     */
    public function testGetHooksActivateHandlerMethodName(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame('getActivate', $hooks['licenses.activate'][1]);
    }

    /**
     * Tests that getDeactivate method references the correct handler.
     *
     * @return void
     */
    public function testGetHooksDeactivateHandlerMethodName(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame('getDeactivate', $hooks['licenses.deactivate'][1]);
    }

    /**
     * Tests that getDeactivateKey method references the correct handler.
     *
     * @return void
     */
    public function testGetHooksDeactivateKeyHandlerMethodName(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame('getDeactivateKey', $hooks['licenses.deactivate_key'][1]);
    }

    /**
     * Tests that getSettings method references the correct handler.
     *
     * @return void
     */
    public function testGetHooksSettingsHandlerMethodName(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame('getSettings', $hooks['licenses.settings'][1]);
    }

    /**
     * Tests that getRequirements method references the correct handler.
     *
     * @return void
     */
    public function testGetHooksRequirementsHandlerMethodName(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertSame('getRequirements', $hooks['function.requirements'][1]);
    }

    /**
     * Tests that the class does not extend any other class.
     *
     * @return void
     */
    public function testClassDoesNotExtendAnything(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertFalse($reflection->getParentClass());
    }

    /**
     * Tests that the class does not implement any interfaces.
     *
     * @return void
     */
    public function testClassDoesNotImplementInterfaces(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertEmpty($reflection->getInterfaceNames());
    }

    /**
     * Tests that the class has exactly the expected number of public static methods.
     *
     * @return void
     */
    public function testClassMethodCount(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $staticMethods = array_filter(
            $reflection->getMethods(\ReflectionMethod::IS_STATIC),
            function ($method) {
                return $method->isPublic() && $method->getDeclaringClass()->getName() === Plugin::class;
            }
        );

        // getHooks, getActivate, getDeactivate, getDeactivateKey, getChangeIp, getRequirements, getSettings
        $this->assertCount(7, $staticMethods);
    }

    /**
     * Tests that the description references the parallels.com URL.
     *
     * @return void
     */
    public function testDescriptionContainsUrl(): void
    {
        $this->assertStringContainsString('https://parallels.com', Plugin::$description);
    }

    /**
     * Tests getChangeIp method exists and has the correct signature.
     *
     * @return void
     */
    public function testGetChangeIpMethodExists(): void
    {
        $reflection = new ReflectionClass(Plugin::class);
        $this->assertTrue($reflection->hasMethod('getChangeIp'));
        $method = $reflection->getMethod('getChangeIp');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame(GenericEvent::class, $params[0]->getType()->getName());
    }
}
