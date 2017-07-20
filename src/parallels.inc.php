<?php
/**
 * Parallels Related Functionality
 * Last Changed: $LastChangedDate: 2017-05-25 20:32:57 -0400 (Thu, 25 May 2017) $
 * @author detain
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
 * @param mixed  $ipAddress
 * @param mixed  $type
 * @param string $addons
 * @return mixed
 */
function activate_parallels($ipAddress, $type, $addons = '') {
	function_requirements('class.Parallels');
	myadmin_log('licenses', 'info', "Parallels New License $ipAddress Type $type Addons $addons called", __LINE__, __FILE__);
	ini_set('max_execution_time', 1000); // just put a lot of time
	ini_set('default_socket_timeout', 1000); // same
	$parallels = new \Detain\Parallels\Parallels();
	if (trim($addons) == '')
		$addonsArray = [];
	else
		$addonsArray = explode(',', $addons);

	// check if already active
	myadmin_log('licenses', 'info', 'addonsArray:', __LINE__, __FILE__);
	myadmin_log('licenses', 'info', var_export($addonsArray, TRUE), __LINE__, __FILE__);
	$request = [$type, $addonsArray, $ipAddress];
	$response = $parallels->createKey($type, $addonsArray, $ipAddress);
	request_log('licenses', FALSE, __FUNCTION__, 'parallels', 'createKey', $request, $response);
	myadmin_log('licenses', 'info', "activate Parallels({$ipAddress}, {$type}, {$addons}) Response: ".json_encode($response), __LINE__, __FILE__);
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
* @param $ipAddress
*/
function deactivate_parallels($ipAddress) {
	myadmin_log('licenses', 'info', "Parallels Deactivation ({$ipAddress})", __LINE__, __FILE__);
	function_requirements('class.Parallels');
	$parallels = new \Detain\Parallels\Parallels();
	$key = $parallels->getMainKeyFromIp($ipAddress);
	request_log('licenses', FALSE, __FUNCTION__, 'parallels', 'getMainKeyFromIp', $ipAddress, $key);
	myadmin_log('licenses', 'info', "Parallels getMainKeyFromIp({$ipAddress}) = {$key} Raw Response: ".json_encode($parallels->response), __LINE__, __FILE__);
	if ($key !== FALSE) {
		$response = $parallels->terminateKey($key);
		request_log('licenses', FALSE, __FUNCTION__, 'parallels', 'terminateKey', $key, $response);
		myadmin_log('licenses', 'info', "Parallels TerminateKey({$key}) Response: ".json_encode($response), __LINE__, __FILE__);
	} else {
		myadmin_log('licenses', 'info', 'Parallels No Key Found to Terminate', __LINE__, __FILE__);
	}

}
