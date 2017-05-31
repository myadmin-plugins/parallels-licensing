<?php
/* TODO:
 - service type, category, and services  adding
 - dealing with the SERVICE_TYPES_parallels define
 - add way to call/hook into install/uninstall
*/
return [
	'name' => 'Parallels Licensing',
	'description' => 'Allows selling of Parallels Server and VPS License Types.  More info at https://parallels.com',
	'help' => 'Professional control panel that gives web-designers, web-masters and website owners tools to manage their servers, sites and applications. The only hosting solution that will grow with your business from a single site and servers to a multi-server cloud solution and millions of users. The professionals choice for growing businesses.',
	'module' => 'licenses',
	'author' => 'detain@interserver.net',
	'home' => 'https://github.com/detain/myadmin-parallels-licensing',
	'repo' => 'https://github.com/detain/myadmin-parallels-licensing',
	'version' => '1.0.0',
	'type' => 'licenses',
	'hooks' => [
		'licenses.settings' => ['Detain\MyAdminParallels\Plugin', 'Settings'],
		'licenses.activate' => ['Detain\MyAdminParallels\Plugin', 'Activate'],
		'licenses.deactivate' => ['Detain\MyAdminParallels\Plugin', 'Deactivate'],
		'function.requirements' => ['Detain\MyAdminParallels\Plugin', 'Requirements'],
		'licenses.change_ip' => ['Detain\MyAdminParallels\Plugin', 'ChangeIp'],
		/* 'ui.menu' => ['Detain\MyAdminParallels\Plugin', 'Menu'] */
	],
];
