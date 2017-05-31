<?php
/**
 * Parallels Related Functionality
 * Last Changed: $LastChangedDate: 2017-05-25 20:32:57 -0400 (Thu, 25 May 2017) $
 * @author detain
 * @version $Revision: 24796 $
 * @copyright 2017
 * @package MyAdmin
 * @category Licenses
 */

/**
 * get_parallels_licenses()
 * simple wrapper to get all the parallels licenses.
 *
 * @return array array of licenses. {@link Parallels.getIpListDetailed}
 */

/**
 * activate_parallels()
 * @param mixed  $ip
 * @param mixed  $type
 * @param string $addons
 * @return mixed
 */
function activate_parallels($ip, $type, $addons = '') {
	function_requirements('class.Parallels');
	myadmin_log('licenses', 'info', "Parallels New License $ip Type $type Addons $addons called", __LINE__, __FILE__);
	ini_set('max_execution_time', 1000); // just put a lot of time
	ini_set('default_socket_timeout', 1000); // same
	$db = get_module_db('licenses');
	$settings = get_module_settings('licenses');
	$parallels = new Parallels();
	if (trim($addons) == '')
		$a_addons = [];
	else
		$a_addons = explode(',', $addons);

	// TODO
	// check if already active
	myadmin_log('licenses', 'info', 'a_addons:', __LINE__, __FILE__);
	myadmin_log('licenses', 'info', var_export($a_addons, true), __LINE__, __FILE__);
	$request = array($type, $a_addons, $ip);
	$response = $parallels->createKey($type, $a_addons, $ip);
	request_log('licenses', false, __FUNCTION__, 'parallels', 'createKey', $request, $response);
	myadmin_log('licenses', 'info', "Activate Parallels({$ip}, {$type}, {$addons}) Response: " . json_encode($response), __LINE__, __FILE__);
	/* example response:
	Array(
	[mainKeyNumber] => PLSK.00007677.0000
	[expirationDate] => stdClass Object
	(
	[scalar] => 20131211T00:00:00
	[xmlrpc_type] => datetime
	[timestamp] => 1386720000
	)

	[productKey] => A00300-K4KT02-JHE757-B1FE76-JD2N77
	[additionalKeysNumbers] => Array
	(
	)

	[resultCode] => 100
	[resultDesc] => PLSK.00007677.0000 has been successfully created.
	[updateDate] => stdClass Object
	(
	[scalar] => 20131201T00:00:00
	[xmlrpc_type] => datetime
	[timestamp] => 1385856000
	)

	)
	*/
	return $response;
}

/**
* @param $ip
*/
function deactivate_parallels($ip) {
	myadmin_log('licenses', 'info', "Parallels Deactivation ({$ip})", __LINE__, __FILE__);
	function_requirements('class.Parallels');
	$parallels = new Parallels();
	$key = $parallels->getMainKeyFromIp($ip);
	request_log('licenses', false, __FUNCTION__, 'parallels', 'getMainKeyFromIp', $ip, $key);
	myadmin_log('licenses', 'info', "Parallels getMainKeyFromIp({$ip}) = {$key} Raw Response: " . json_encode($parallels->response), __LINE__, __FILE__);
	if ($key !== false) {
		$response = $parallels->terminateKey($key);
		request_log('licenses', false, __FUNCTION__, 'parallels', 'terminateKey', $key, $response);
		myadmin_log('licenses', 'info', "Parallels TerminateKey({$key}) Response: " . json_encode($response), __LINE__, __FILE__);
	} else {
		myadmin_log('licenses', 'info', 'Parallels No Key Found to Terminate', __LINE__, __FILE__);
	}

}
