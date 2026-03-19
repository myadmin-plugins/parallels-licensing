# MyAdmin Parallels Licensing Plugin

A MyAdmin plugin that integrates Parallels Plesk and Virtuozzo license management into the MyAdmin hosting control panel. Provides automated license provisioning, activation, deactivation, IP change handling, and key-based termination through the Parallels KA (Key Administrator) API.

[![Build Status](https://github.com/detain/myadmin-parallels-licensing/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-parallels-licensing/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-parallels-licensing/version)](https://packagist.org/packages/detain/myadmin-parallels-licensing)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-parallels-licensing/downloads)](https://packagist.org/packages/detain/myadmin-parallels-licensing)
[![License](https://poser.pugx.org/detain/myadmin-parallels-licensing/license)](https://packagist.org/packages/detain/myadmin-parallels-licensing)

## Features

- License activation and reactivation with addon support
- License deactivation by IP address or key number
- IP address change handling with fault reporting
- Configurable KA client, login, password, and URL settings
- Event-driven architecture via Symfony EventDispatcher
- Automatic requirement loading through the MyAdmin plugin system

## Installation

```sh
composer require detain/myadmin-parallels-licensing
```

## Requirements

- PHP 8.2 or later
- ext-soap
- Symfony EventDispatcher 5.x
- detain/parallels-licensing

## Running Tests

```sh
composer install
vendor/bin/phpunit
```

## License

This package is licensed under the [LGPL-2.1](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html) license.
