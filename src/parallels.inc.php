<?php
/**
 * Parallels Related Functionality
 * @author Joe Huss <detain@interserver.net>
 * @copyright 2019
 * @package MyAdmin
 * @category Licenses
 */

use \Detain\Parallels\Parallels;

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
function activate_parallels($ipAddress, $type, $addons = '')
{
    myadmin_log('licenses', 'info', "Parallels New License {$ipAddress} Type {$type} Addons {$addons} called", __LINE__, __FILE__);
    ini_set('max_execution_time', 1000); // just put a lot of time
    ini_set('default_socket_timeout', 1000); // same
    $parallels = new \Detain\Parallels\Parallels();
    if (trim($addons) == '') {
        $addonsArray = [];
    } else {
        $addonsArray = explode(',', $addons);
    }

    // check if already active
    myadmin_log('licenses', 'info', 'addonsArray:', __LINE__, __FILE__);
    myadmin_log('licenses', 'info', var_export($addonsArray, true), __LINE__, __FILE__);
    $request = [$type, $addonsArray, $ipAddress];
    try {
        $response = $parallels->createKey($type, $addonsArray, $ipAddress);
        request_log('licenses', false, __FUNCTION__, 'parallels', 'createKey', $request, $response);
        myadmin_log('licenses', 'info', "activate Parallels({$ipAddress}, {$type}, {$addons}) Response: ".json_encode($response), __LINE__, __FILE__);
    } catch (\XML_RPC2_CurlException  $e) {
        request_log('licenses', false, __FUNCTION__, 'parallels', 'createKey', $request, $e->getMessage());
        return false;
    }
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
function deactivate_parallels($ipAddress)
{
    myadmin_log('licenses', 'info', "Parallels Deactivation ({$ipAddress})", __LINE__, __FILE__);
    $parallels = new \Detain\Parallels\Parallels();
    try {
        $response = $parallels->getKeyNumbers($ipAddress);
        request_log('licenses', false, __FUNCTION__, 'parallels', 'getMainKeyFromIp', $ipAddress, $response);
        myadmin_log('licenses', 'info', "Parallels getMainKeyFromIp({$ipAddress}): ".json_encode($response), __LINE__, __FILE__);
    } catch (\XML_RPC2_CurlException  $e) {
        request_log('licenses', false, __FUNCTION__, 'parallels', 'getMainKeyFromIp', $ipAddress, $e->getMessage());
        return false;
    }
    if (isset($response['keyNumbers'])) {
        $keys = $response['keyNumbers'];
        foreach ($keys as $key) {
            deactivate_parallels_by_key($key);
        }
    } else {
        myadmin_log('licenses', 'info', 'Parallels No Key Found to Terminate', __LINE__, __FILE__);
    }
    return true;
}

/**
* @param $ipAddress
*/
function deactivate_parallels_by_key($key)
{
    myadmin_log('licenses', 'info', "Parallels Deactivation ({$key})", __LINE__, __FILE__);
    $parallels = new \Detain\Parallels\Parallels();
    $status = json_decode(file_get_contents(__DIR__.'/../../../../include/config/plesk.json'), true);
    if (count(explode('.', $key)) > 2) {
        $key = substr($key, 0, strrpos($key, '.'));
    }
    $response = $parallels->terminateKey($key);

    request_log('licenses', false, __FUNCTION__, 'parallels', 'terminateKey', $key, $response);
    myadmin_log('licenses', 'info', "Parallels TerminateKey({$key}) Response: ".json_encode($response), __LINE__, __FILE__);
    if (array_key_exists($key, $status)) {
        $status[$key]['terminated'] = true;
        file_put_contents(__DIR__.'/../../../../include/config/plesk.json', json_encode($status, JSON_PRETTY_PRINT));
    }
    if (array_key_exists(str_replace('0001', '0000', $key), $status)) {
        $status[str_replace('0001', '0000', $key)]['terminated'] = true;
        file_put_contents(__DIR__.'/../../../../include/config/plesk.json', json_encode($status, JSON_PRETTY_PRINT));
    }
    if (!isset($status[$key]['terminated'])  || !$status[$key]['terminated']) {
        $bodyRows = [];
        $bodyRows[] = 'License Key: '.$key.' unable to deactivate.';
        $bodyRows[] = 'Deactivation Response: .'.json_encode($response);
        $subject = 'Parallels License Deactivation Issue Key: '.$key;
        $smartyE = new TFSmarty();
        $smartyE->assign('h1', 'Parallels License Deactivation');
        $smartyE->assign('body_rows', $bodyRows);
        $msg = $smartyE->fetch('email/client/client_email.tpl');
        (new \MyAdmin\Mail())->adminMail($subject, $msg, false, 'client/client_email.tpl');
    }
    return true;
}
