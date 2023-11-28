<?php

/*
 * GR8 Faucet Script Lite
 * https://gr8.cc
 * 
 * Copyright 2019 GR8 Scripts, AvalonRychmon
 * 
 * GR8 Faucet Script Lite is free bare bones version of the GR8 Faucet Script.
 * It was released so that anyone interested in operating a cryptocurrecy faucet
 * would have an equal opportunity regardless of their financial position or
 * personal knowledge of coding. 
 *
 * If you need assistance with this script, then please join us on Discord at
 * https://discordapp.com/invite/DeExBQJ
 * 
 * I personally wish you great success on your journey! -AvalonRychmon
 *
 */

## Core Version
$cv = '1'; 

## CHECK SSL
function isSSL(){
	return ((strtolower($_SERVER['HTTPS']) == 'on')|| 
		($_SERVER['HTTPS'] == '1')||
		(strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')||
		($_SERVER['SERVER_PORT'] == 443)||
		(substr($_SERVER['SCRIPT_URI'], 0, 5) === "https")) ? 'yes' : 'no';
}

## GET DOMAIN
function getDomain($url='') {
	
	$url = ($url) ? $url : getCurrentURL();
    $url = trim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url = explode('/', $url);
    $scheme = isset($url[0]) ? $url[0] : null;
    $domain = isset($url[2]) ? $url[2] : null;
	$url = $scheme.'//'.$domain;

	return $url;
}

## GET CURRENT URL
function getCurrentURL(){
	
	$url = (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'). $_SERVER['SERVER_NAME'] . explode('?',$_SERVER['REQUEST_URI'])[0];
	return $url;
}                

## GET IP ADDRESS 
function getIP(){
	global $_SERVER;
	static $ip;
	
	if ($ip) {
		return $ip;
	}
		
	$keys = array('HTTP_TRUE_CLIENT_IP', 'HTTP_CF_CONNECTING_IP','HTTP_INCAP_CLIENT_IP','HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP','HTTP_FORWARDED_FOR', 'HTTP_FORWARDED','REMOTE_ADDR');

	foreach ($keys as $key){
        if (array_key_exists($key, $_SERVER) === true){
            $ip = trim($_SERVER[$key]);
			if (strstr($ip, ',')) {
				$tmp = explode(',', $ip);
				$ip = trim($tmp[0]);
			}
			
			if((filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
				&&($ip != $_SERVER['SERVER_ADDR'])){
				return $ip;
			}
        }
    }
}

## GET DEVICE INFO
function getDevice(){
    
	require_once ROOTPATH.'libs/UserAgentParser.php';
	return parse_user_agent();
}

## GET IP DATA
function getIPdata($ip=''){
	
	#ip-api.com
	$ipData = getCURL('http://ip-api.com/json/'.$ip.'?fields=status,country,countryCode,city,region,isp,as', true);
		if($ipData['status'] == 'success'){
		    $ipData['ip'] = $ip;
			$ipData['data-source'] = 'ip-api.com';
			$ipData['asn'] = explode(' ',$ipData['as'])['0'];
			return json_encode($ipData);
		}	
	
	#ipapi.co
	$ipData = getCURL('https://ipapi.co/'.$ip.'/json/', true);
	if($ipData['country_name']){
		    $ipData['ip'] = $ip;
			$ipData['data-source'] = 'ipapi.co';
			$ipData['region'] = $ipData['region_code'];
			$ipData['countryCode'] = $ipData['country'];
			$ipData['country'] = $ipData['country_name'];
			$ipData['isp'] = $ipData['org'];
			return json_encode($ipData);
	}	
}

## CHECK PROXY
function isProxy(){

	global $cache, $faucetID, $settings, $_SESSION;	

	## SET IP 
	$ip = getIP();
	
	## EMPTY IP
	if($ip == ''){  return false; }
	
	## SKIP SAFE IPS
	if($safe && (in_array($ip, $safe))){ return false; }
	
	## IF SESSION CACHE
	if(@array_key_exists($ip, $_SESSION[$faucetID]['ip_check'])){
		return $_SESSION[$faucetID]['ip_check'][$ip]['data']['proxy'];
	}
	
	## IF IP CACHE RETURN STATUS
	elseif($cache->get($ip)){
		$data = $cache->get($ip);
		$_SESSION[$faucetID]['ip_check'][$ip]['data'] = $data;
		return $data['proxy'];
	}
	## ELSE CHECK IP
	else {		
		
		// User IP
		$data['ip'] = $ip;
		
		// User Device info
		$device = getDevice();
		$data['os'] = $device['os'];
		$data['device'] = $device['device'];
		$data['browser'] = $device['browser'];
		
		## IPHUB - http://iphub.info
		if($settings['iphub_api']){	
			
			$check = getCURL('http://v2.api.iphub.info/ip/'.$ip, true, '', array("X-Key: ".$settings['iphub_api']));
			
			// IP Data
			$data['asn'] = 'AS'.$check['asn'];
			$data['countryCode'] = $check['countryCode'];
			
			// IP Proxy/VPN
			if($check['block'] === 1){
			    $data['source'] = 'iphub.info';	
				$data['proxy'] = true;
				$cache->set($ip, $data, 604800);
				$_SESSION[$faucetID]['ip_check'][$ip]['data'] = $data;
				getCURL('https://gr8.cc/_data/proxycheck.php', '', array('ip' => $ip, 'domain' => $settings['domain'], 'source' => $data['source'], 'data' => getIPdata($ip)));
				userLog('action', 'proxy', getIP().' blocked by '.$data['source']);
				return true;			
			}
		}
			
		## PROXYCHECK.IO - http://proxycheck.io
		if($settings['proxycheck_api']){		
			
			$check = getCURL('http://proxycheck.io/v2/'.$ip.'?key='.$settings['proxycheck_api'].'&vpn=1&asn=1&node=1&time=1&inf=0&port=1&seen=1&days=7&tag=msg', true);	
			
			// IP Data
			$data['asn'] = ($data['asn'])?: $check[$ip]['asn'];
			$data['countryCode'] = ($data['countryCode'])?: $check[$ip]['isocode'];
			
			// IP Proxy/VPN
			if($check[$ip]['proxy'] == 'yes'){
			    $data['source'] = 'proxycheck.io';	
				$data['proxy'] = true;
				$cache->set($ip, $data, 604800);
				$_SESSION[$faucetID]['ip_check'][$ip]['data'] = $data;
				getCURL('https://gr8.cc/_data/proxycheck.php', '', array('ip' => $ip, 'domain' => $settings['domain'], 'source' => $data['source'], 'data' => getIPdata($ip)));
				userLog('action', 'proxy', getIP().' blocked by '.$data['source']);
				return true;							
			}
		}
		
		## IP-API - https://ip-api.com/
	    $check = getCURL('http://ip-api.com/json/'.$ip.'?fields=status,countryCode,as,proxy', true);
	    
	    // IP Data
		$data['asn'] = ($data['asn'])?: explode(' ',$check['as'])['0'];
		$data['countryCode'] = ($data['countryCode'])?: $check['countryCode'];
		
		// IP Proxy/VPN
		if($check['proxy'] == true){
			$data['source'] = 'ip-api.com';	
			$data['proxy'] = true;
			$cache->set($ip, $data, 604800);
			$_SESSION[$faucetID]['ip_check'][$ip]['data'] = $data;
			getCURL('https://gr8.cc/_data/proxycheck.php', '', array('ip' => $ip, 'domain' => $settings['domain'], 'source' => $data['source'], 'data' => getIPdata($ip)));
			userLog('action', 'proxy', getIP().' blocked by '.$data['source']);
			return true;			
		}
		
    	// Set Cache/Return false
    	$data['proxy'] = false;
    	$cache->set($ip, $data, 604800); 
		$_SESSION[$faucetID]['ip_check'][$ip]['data'] = $data;
    	return false;	
		
	}
}

## CHECK IP CHANGED
function IPchange(&$message = null){
	global $faucetID, $_SESSION;
	
	if( $_SESSION[$faucetID]['user']['ip'] && ($_SESSION[$faucetID]['user']['ip'] != getIP()) && ($_SESSION[$faucetID]['status'] != 'login') ){
		userLog('action', 'ip-chance', 'IP Changed ('.$_SESSION[$faucetID]['user']['ip'].' '.getIP().')');
		$message = 'IP changed not allowed, Use same IP to Claim';
		$_SESSION[$faucetID]['status'] = 'login';
		return false;
	}
	return true;
}

## GET SESSION TOKEN
function getToken() {
    global $faucetID, $_SESSION;
	
	$token = bin2hex(random_bytes(32));
    $_SESSION[$faucetID]['session-token'] = $token;
	
    return $token;
}

## VERIFY SESSION TOKEN
function checkToken() {

	global $faucetID, $_POST, $_SESSION;
	
	if($_SESSION[$faucetID]['session-token'] != $_POST['session-token']){
		$message = 'Session token did not match';
	    userLog('action', 'session-token', $message);
		return false;
	}
	return true;
}

## SANATIZE STRING
function sanitize($str){
	
	global $db;
		
		$search = array(
				'@<script[^>]*?>.*?</script>@si',
				'@<[\/\!]*?[^<>]*?>@si',
				'@<style[^>]*?>.*?</style>@siU',
				'@<![\s\S]*?--[ \t\n\r]*>@siU',
				"/\\\\n/"
			);

	$str = ini_get( 'magic_quotes_gpc' ) ? stripslashes( $str ) : $str;
	$str = strip_tags(preg_replace($search, '', $str));
	$str = trim( $str );
	$str = htmlspecialchars( $str );
	$str = mysqli_real_escape_string($db, $str);
	$str = stripslashes( $str );
                   	
	return $str;
}

## GET cURL
function getCURL($url, $decode = '', $data = '', $headers = '', $timeout='') {

	global $_SERVER;
	
	$ch = curl_init();
	
	if($timeout){
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	}
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:64.0) Gecko/20100101 Firefox/64.0');
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	
	if(is_array($data)){
		curl_setopt($ch, CURLOPT_POST, count($data));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	
	if(is_array($headers)){
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}
	
	$response = @curl_exec($ch);
    $response = ($decode == true)? json_decode($response,true) : $response;
	
	if($decode == true){ $response['httpcode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE); }
	
    curl_close($ch);

    return $response;
}

## CHECK WEBSITE ONLINE
function isOnline($url){
    
	if((stristr($url, 'http'))&&(!stristr($url,"api/"))&&(!stristr($url,"api.php")) ){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$http_respond = curl_exec($ch);
		$http_respond = trim( strip_tags( $http_respond ) );
		$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );
		
		if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {
			return true;
		} else {
			return false;
		}
	}
	
	return false;
} 

## datetime Now
function dateNow($var=''){
	if($var == 'today'){
		return date('Y-m-d');
	}
	elseif($var == 'yesterday'){
		return date('Y-m-d', strtotime( '-1 days' ) );
	}
	elseif($var){
		return date('Y-m-d', strtotime( $var. ' days' ) );
	}
	else {
		return date('Y-m-d H:i:s');
	}
}

## Redirect
function redirect($url=''){
	
	$url = ($url) ?: getCurrentUrl();
	 if (!headers_sent()){    
        header('Location: '.$url);
        exit;
        }
    else{  
		echo '<noscript><meta http-equiv="refresh" content="0;url='.$url.'" /></noscript>';
		echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
	}
}

## VALIDATE ADDRESS
function validAddress($address) {
	
	if(ctype_alnum($address) && (preg_match("/^[0-9A-Za-z]{26,110}$/", $address) === 1) && (count(array_count_values(str_split($address))) >= '15')){
		return true;
	}
	return false;
}

## PRE ARARY
function print_pre($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

## RANDOM HASH
function randHash($length) {
    $hash = '';
    $alphanumeric = str_split('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890');
    for($i = 0; $i < $length; $i++) {
        $hash .= $alphanumeric[array_rand($alphanumeric)];
    }
    return $hash;
}

## ALERT MESSAGE
function alert($msg, $style='success', $close=''){
    $alert = '
		<div class="alert alert-'.$style.(($close)? ' alert-dismissible':'').' fade show" role="alert">
			'.$msg.'
			<button type="button" class="close'.(($close)? '': ' d-none').'" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>';
	return $alert;
}

## CLEAN CACHE
function cleanCache($max_age=''){
	
	$list = array();
	$dir = 'libs/cache/cache/cache.storage.'.explode('://',getDomain())['1'];
    $dir = realpath($dir);
	
	$structure = glob(rtrim($dir, "/").'/*');
	foreach($structure as $file) {
		if (filemtime($file)<time()-(($max_age)?: (7 * 86400))) {
			if(is_dir($file)) {
			    $list[] = $file;
				array_map('unlink', glob("$file/*.*"));
				
			}
			rmdir($file);
		}
	}
	
	return $list;
}

