<?php
/* TODO:
 - service type, category, and services  adding
 - dealing with the SERVICE_TYPES_parallels define
 - add way to call/hook into install/uninstall
*/
return [
	'name' => 'Parallels Licensing',
	'description' => 'Allows selling of Parallels Server and VPS License Types.  More info at https://www.netenberg.com/parallels.php',
	'help' => 'It provides more than one million end users the ability to quickly install dozens of the leading open source content management systems into their web space.  	Must have a pre-existing cPanel license with cPanelDirect to purchase a parallels license. Allow 10 minutes for activation.',
	'module' => 'licenses',
	'author' => 'detain@interserver.net',
	'home' => 'https://github.com/detain/myadmin-parallels',
	'repo' => 'https://github.com/detain/myadmin-parallels',
	'version' => '1.0.0',
	'type' => 'licenses',
	'hooks' => [
		'function.requirements' => ['Detain\MyAdminParallels\Plugin', 'Requirements'],
		'licenses.settings' => ['Detain\MyAdminParallels\Plugin', 'Settings'],
		'licenses.activate' => ['Detain\MyAdminParallels\Plugin', 'Activate'],
		'licenses.change_ip' => ['Detain\MyAdminParallels\Plugin', 'ChangeIp'],
		'ui.menu' => ['Detain\MyAdminParallels\Plugin', 'Menu']
	],
];
