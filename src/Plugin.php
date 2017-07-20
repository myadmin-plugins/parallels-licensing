<?php

namespace Detain\MyAdminParallels;

//use Detain\Parallels\Parallels;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminParallels
 */
class Plugin {

	public static $name = 'Parallels Licensing';
	public static $description = 'Allows selling of Parallels Server and VPS License Types.  More info at https://parallels.com';
	public static $help = 'Professional control panel that gives web-designers, web-masters and website owners tools to manage their servers, sites and applications. The only hosting solution that will grow with your business from a single site and servers to a multi-server cloud solution and millions of users. The professionals choice for growing businesses.';
	public static $module = 'licenses';
	public static $type = 'service';

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return array
	 */
	public static function getHooks() {
		return [
			self::$module.'.settings' => [__CLASS__, 'getSettings'],
			self::$module.'.activate' => [__CLASS__, 'getActivate'],
			self::$module.'.reactivate' => [__CLASS__, 'getActivate'],
			self::$module.'.deactivate' => [__CLASS__, 'getDeactivate'],
			self::$module.'.deactivate_ip' => [__CLASS__, 'getDeactivate'],
			'function.requirements' => [__CLASS__, 'getRequirements']
		];
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getActivate(GenericEvent $event) {
		$serviceClass = $event->getSubject();
		if ($event['category'] == get_service_define('PARALLELS')) {
			myadmin_log(self::$module, 'info', 'Parallels Activation', __LINE__, __FILE__);
			function_requirements('activate_parallels');
			if (trim($event['field2']) != '') {
				$response = activate_parallels($serviceClass->getIp(), $event['field1'], $event['field2']);
			} else {
				$response = activate_parallels($serviceClass->getIp(), $event['field1']);
			}
			myadmin_log(self::$module, 'info', 'Response: '.json_encode($response), __LINE__, __FILE__);
			$serviceExtra = $response['mainKeyNumber'].','.$response['productKey'];
			$serviceClass->set_extra($serviceExtra)->save();
			$event->stopPropagation();
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getDeactivate(GenericEvent $event) {
		$serviceClass = $event->getSubject();
		if ($event['category'] == get_service_define('PARALLELS')) {
			myadmin_log(self::$module, 'info', 'Parallels Deactivation', __LINE__, __FILE__);
			function_requirements('deactivate_parallels');
			deactivate_parallels($serviceClass->getIp());
			$event->stopPropagation();
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getChangeIp(GenericEvent $event) {
		if ($event['category'] == get_service_define('PARALLELS')) {
			$serviceClass = $event->getSubject();
			$settings = get_module_settings(self::$module);
			$parallels = new \Parallels(FANTASTICO_USERNAME, FANTASTICO_PASSWORD);
			myadmin_log(self::$module, 'info', 'IP Change - (OLD:' .$serviceClass->getIp().") (NEW:{$event['newip']})", __LINE__, __FILE__);
			$result = $parallels->editIp($serviceClass->getIp(), $event['newip']);
			if (isset($result['faultcode'])) {
				myadmin_log(self::$module, 'error', 'Parallels editIp('.$serviceClass->getIp().', '.$event['newip'].') returned Fault '.$result['faultcode'].': '.$result['fault'], __LINE__, __FILE__);
				$event['status'] = 'error';
				$event['status_text'] = 'Error Code '.$result['faultcode'].': '.$result['fault'];
			} else {
				$GLOBALS['tf']->history->add($settings['TABLE'], 'change_ip', $event['newip'], $serviceClass->getIp());
				$serviceClass->set_ip($event['newip'])->save();
				$event['status'] = 'ok';
				$event['status_text'] = 'The IP Address has been changed.';
			}
			$event->stopPropagation();
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getRequirements(GenericEvent $event) {
		$loader = $event->getSubject();
		$loader->add_requirement('class.Parallels', '/../vendor/detain/parallels-licensing/src/Parallels.php');
		$loader->add_requirement('activate_parallels', '/../vendor/detain/myadmin-parallels-licensing/src/parallels.inc.php');
		$loader->add_requirement('deactivate_parallels', '/../vendor/detain/myadmin-parallels-licensing/src/parallels.inc.php');
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event) {
		$settings = $event->getSubject();
		$settings->add_text_setting(self::$module, 'Parallels', 'parallels_ka_client', 'Parallels KA Client:', 'Parallels KA Client', $settings->get_setting('PARALLELS_KA_CLIENT'));
		$settings->add_text_setting(self::$module, 'Parallels', 'parallels_ka_login', 'Parallels KA Login:', 'Parallels KA Login', $settings->get_setting('PARALLELS_KA_LOGIN'));
		$settings->add_text_setting(self::$module, 'Parallels', 'parallels_ka_password', 'Parallels KA Password:', 'Parallels KA Password', $settings->get_setting('PARALLELS_KA_PASSWORD'));
		$settings->add_text_setting(self::$module, 'Parallels', 'parallels_ka_url', 'Parallels KA URL:', 'Parallels KA URL', $settings->get_setting('PARALLELS_KA_URL'));
		$settings->add_dropdown_setting(self::$module, 'Parallels', 'outofstock_licenses_parallels', 'Out Of Stock Parallels Licenses', 'Enable/Disable Sales Of This Type', $settings->get_setting('OUTOFSTOCK_LICENSES_FANTASTICO'), ['0', '1'], ['No', 'Yes']);
	}

}
