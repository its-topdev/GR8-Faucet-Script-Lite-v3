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
 * https://gr8.cc/discord
 * 
 * I personally wish you great success on your journey! -AvalonRychmon
 *
 */
    
## Functions version
$fv = '3';

## Captchas
$captchas = array('solvemedia' => 'SolveMedia', 'recaptcha' => 'reCaptcha');

## Get Faucet Settings
function getSettings($update=''){
	 
	global $cache, $db, $faucetID;
	
	$settings = $cache->get('settings-'.$faucetID);
    
    try{
    	if(!$settings || $update) {
    	    $s = $db->query("SELECT * FROM `settings-".$faucetID."`");
        	while ($row = $s->fetch_assoc()) {
        		$settings[$row['name']] = $row['value'];
        	}
        		
        	if($settings){
        	    // Check for Update
        	    $update = getCURL('https://gr8.cc/_data/version.php', true, array('name' => $settings['name'], 'domain' => $settings['domain'], 'script' => 'gr8lite', 'version' => $settings['version']));
        	    if($update['version'] > $settings['version']){
        	        $settings['update'] = $update;
        	    }
        	    // Cache Settings
        	    $cache->set('settings-'.$faucetID, $settings, 86400);
        	}
    	}
    	elseif(!$settings['disable_balance']){
    		$settings['balance'] = @$db->query("SELECT `value` FROM `settings-".$faucetID."` WHERE `name` = 'balance'")->fetch_assoc()['value'];
    	}
    } catch(Exception $e) {
        error_log($e->getMessage()); 
    }

	return $settings;
}

## Get Shortlink List
function getShortlinks(){
	
	global $cache, $settings;
	
	$sldata = $cache->get("sldata");
	
	if(!$sldata || $sldata['updated']<time()-7200){
		$sldata = getCURL('https://gr8.cc/_data/shortlinks.php', true, array('domain' => $settings['domain'], 'script' => 'gr8lite'));
		if($sldata['data']){
			$cache->set("sldata", $sldata, 86400);
		}
	}
	return $sldata;
}

## Get Currency USD Rate
function getRate($value, $usd=''){
	
	global $cache, $currencies, $settings;
	
	// Set coinGecko Currency ID
	switch($settings['currency']){
	    case 'BNB': 
	        $currency = 'binancecoin';
	        break;
	   default:
	       $currency = strtolower(str_replace(' ','-',$currencies[$settings['currency']]));
	}

    // Get Cached Rate
    $rate = $cache->get($currency.'-rate');
    
    // Update Cache Rate if doesnt exist or older than 5 minutes
	if(!$rate || $rate['updated']<time()-300){
		$coingecko = getCURL('https://api.coingecko.com/api/v3/coins/'.$currency.'?localization=false&tickers=false&market_data=true&community_data=false&developer_data=false&sparkline=false', true);
		
		if($coingecko['market_data']['current_price']['usd']){
		    $rate['name'] = $coingecko['name'];
		    $rate['usd_price'] = $coingecko['market_data']['current_price']['usd'];
		    $rate['sat_price'] = ($currency == 'dogecoin')? 1/$rate['usd_price'] : 100000000 / $rate['usd_price'];
		    $rate['updated'] = time();
		
		    $cache->set($currency.'-rate', $rate, 86400);
		}
	}
	
	// Return satoshi to usd rate
	if($usd){ 
	    $rate['usd'] = number_format($rate['usd_price']/100000000 * $value , 5, '.', ''); 
	    return $rate; 
	}
	
	// Return usd to satoshi
    $rate['value'] = ($currency == 'dogecoin')? intval(($value * $rate['sat_price'])*100000000) : intval($rate['sat_price']*$value);
    $rate['value'] = (round($rate['value']) >= 1)? round($rate['value']) : '1';
    
    return $rate;
}

## Get Rewards
function getReward($rewards, $list=''){
    
	global $settings;
	
	// Percentage Chance Reward
	if(stristr($rewards, '*')){
		$reward = explode(',', trim($rewards));
		$reward = array_filter($reward);
		foreach($reward as $r){
			$r = explode('*', $r);
			$new_reward[floatval($r['1'])] = floatval($r['0']);
		}
		krsort($new_reward);
		
		// Show Reward List	
		if($list){
			foreach($new_reward as $k=>$v){
				if(strlen($v) >= '7'){
					$rlist[] = ($v/100000000).' ('.$k.'%)';
					$abrv = 'true';

				} else {
					$rlist[] = $v.' ('.$k.'%)';
				}
			}

			$list = implode(', ',$rlist);
			if($abrv == 'true'){
				return 'Claim '.$list.' '.$settings['currency'].' every '.(($settings['timer'] > '1')? $settings['timer'].' minutes' : 'minute'); 
			} else {
				return 'Claim '.$list.' satoshi every '.(($settings['timer'] > '1')? $settings['timer'].' minutes' : 'minute'); 
			} 
		}
		else {
    		// Return reward
    		$reward_array = array();
			foreach($new_reward as $k=>$v){
				for($i=0; $i<$k; $i++)  
				$reward_array[] = $v;
			}
    		$reward = $reward_array[mt_rand(0,count($reward_array)-1)];
    		return $reward;
	    }
	}	

	// Random Min - Max Reward
	elseif(stristr($rewards, '-')){
		$r = explode('-', trim($rewards));
		$r = array_map('trim',$r);
		asort($r);
		$r = explode('-',implode('-',$r));
		$reward = mt_rand(floatval($r[0]),floatval($r[1]));
        
        // Show reward list
		if($list){
			if(strlen(floatval($r[0])) >= '7'){
				return 'Claim between '.(floatval($r[0])/100000000).' and '.(floatval($r[1])/100000000).' '.$settings['currency'].' every '.(($settings['timer'] > '1')? $settings['timer'].' minutes' : 'minute'); 
			} else {
				return 'Claim between '.floatval($r[0]).' and '.floatval($r[1]).' satoshi every '.(($settings['timer'] > '1')? $settings['timer'].' minutes' : 'minute'); 
			}
		}
		else {
		    // Return reward
		    return $reward;
		}
	}
	
	// Single USD Based Reward
	elseif(stristr($rewards, '.')){
	
		$reward = getRate($rewards);
		// Show reward list
		if($list){
			if(strlen(floatval($reward['value'])) >= '7'){
    		    $reward['value'] = ($reward['value']/100000000);
                $reward['value'] = ($reward['value'] > 0.09)? number_format($reward['value'], 2, '.', '') : (($reward['value'] > 0.0009)? number_format($reward['value'], 4, '.', '') : number_format($reward['value'], 6, '.', ''));
				return 'Claim '.$reward['value'].' '.$settings['currency'].' ('.$rewards.' USD) every '.(($settings['timer'] > '1')? $settings['timer'].' minutes' : 'minute'); 
			} else {
				return 'Claim '.$reward['value'].' satoshi ('.$rewards.' USD) every '.(($settings['timer'] > '1')? $settings['timer'].' minutes' : 'minute'); 
			}
		}
		// Return reward
		else {
		    return $reward['value'];
		}		
	}

	// Single satoshi reward
	else {
		$reward = floatval($rewards);
		// Show reward list
		if($list){
			if(strlen($reward) >= '7'){
				return 'Claim '.($reward/100000000).' '.$settings['currency'].' every '.(($settings['timer'] > '1')? $settings['timer'].' minutes' : 'minute'); 
			} else {
				return 'Claim '.$reward.' satoshi every '.(($settings['timer'] > '1')? $settings['timer'].' minutes' : 'minute'); 
		    }
		}
		// Return reward
		else {
		    return $reward;
		}
	}
}

## Get Captcha
function getCaptcha($captcha){
	
	global $solvemedia, $recaptcha, $settings;
	
	switch($captcha){
		case 'solvemedia':
			require_once(ROOTPATH.'libs/solvemedia.php');
			$solvemedia = ($settings['solvemedia_keys'])? json_decode($settings['solvemedia_keys'],true) : $solvemedia;
			$captcha = solvemedia_get_html($solvemedia['challenge_key'], null, isSSL());
			break;
		case 'recaptcha': 
			$recaptcha = ($settings['recaptcha_keys'])? json_decode($settings['recaptcha_keys'],true) : $recaptcha;
			$captcha = '<div class="g-recaptcha" data-sitekey="'.$recaptcha['site_key'].'" data-callback="enableBtn" style="width:304px;margin:0 auto 4px;"></div>';
			$captcha .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
			break;
	} 
	return $captcha;
}

## Check AntiBot
function checkAntibot(){
	global $faucetID, $settings, $_POST, $_SESSION;
	
	if(!$settings['disable_antibot']){
	    $antibotlinks = new antibotlinks(true, 'ttf,otf', array('abl_light_colors'=> ($settings['antibot_theme'])?:'dark') );
    	if($antibotlinks->check() == 'false'){
    	    $_SESSION[$faucetID]['attempts'] = $_SESSION[$faucetID]['attempts']+1;
    	    userLog('action','antibot', 'Failed '.$_SESSION[$faucetID]['attempts'].' times');	
    		unset($_SESSION[$faucetID]['antibotlinks']);	
    		$antibotlinks->generate(4, true);	
    		return false;
    	}
	}
	
	unset($_SESSION[$faucetID]['antibotlinks']);
	return true;
}

## Check Captcha
function checkCaptcha(){
    
	global $settings, $_POST, $_SESSION;
	
	switch($_POST['captcha']){
			case 'solvemedia':
				$solvemedia = json_decode($settings['solvemedia_keys'],true);
				require_once(ROOTPATH.'libs/solvemedia.php');
				$verify = solvemedia_check_answer($solvemedia['verification_key'], getIP(), $_POST["adcopy_challenge"],$_POST["adcopy_response"],$solvemedia['hash_key']);
				$verify = $verify->is_valid;
				break;
			case 'recaptcha': 
				$recaptcha = json_decode($settings['recaptcha_keys'],true);
				$verify = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$recaptcha["secret_key"].'&response='.$_POST['g-recaptcha-response'].'&remoteip='.getIP()), true);
				$verify = $verify['success'];
				break;
		}
		
	if(!$verify){
	    $_SESSION[$faucetID]['attempts'] = $_SESSION[$faucetID]['attempts']+1;
    	userLog('action', 'captcha', 'Failed '.$_SESSION[$faucetID]['attempts'].' times');	
    	return false;
	}
	
   return true;
}	

## Check Microwallet User/Address   
function checkAddress(&$message = null) {
       
    global $db, $faucetID, $microwallets, $settings, $_POST, $_SESSION;
    
    $address = sanitize(trim($_POST['address']));
    
    $user = $db->query("SELECT `id`, `address`, `ref`, `status` FROM `users-".$faucetID."` WHERE `address` = '$address' LIMIT 1");
    $user = ($user)? $user->fetch_assoc() : '';
    
    if($user['id']){
        if($user['status'] == 'active'){
            // Set User
            $_SESSION[$faucetID]['user'] = $user;
            $_SESSION[$faucetID]['user']['ip'] = getIP();
            setcookie($faucetID.'-address', $address, time()+2678400, '/', explode('/',$settings['domain'])[2]);
            
            // Check Last Claim
            if(!checkLastClaim($mins)){
                $message = 'You have to wait '.(($mins > '1')? $mins.' minutes' : ' a minute').' to claim again';
                $_SESSION[$faucetID]['status'] = 'paid';
                userLog('action', 'login', $message);
                return false;
            }
            
            // Check Max Claims
            if(!checkMaxClaims()){
        		$message = 'Your daily claim limit has been reached. Please come back in tomorrow.';
                userLog('action', 'login', $message);
        		return false;
        	}
            
            userLog('action', 'login', 'Login Successful');
            return true;
        }
        else {
            $message = 'Your account is '.$user['status'];
            userLog('action', 'login', $message);
            return false;
        }
    }
    else {
        
        $settings['api_key'] = ($microwallets[$settings['microwallet']]['key']) ? : $settings['api_key'];
        
        switch($settings['microwallet']) {
            case 'cryptoo': // NO UserCheck
                if( !validAddress($address) ){ $message = $address.' does not appear to be an vaild Bitcoin Address'; }
            break;
            case 'cedsonhub': // check-user: api_key, username
                $parms =  array('api_key' => $settings['api_key'], 'username' => $address );
                $response = getCURL($microwallets['cedsonhub']['api_base'].'check-user?'.http_build_query($parms), true);
                // response: details
                $response['status'] = $response['httpcode'];
                if($response['detail'] && strpos($response['detail'], 'Username does not belong to CedsonHub account') ){ 
                    $message = $address.' does not belong to a <a class="alert-link"  href="'.$microwallets['cedsonhub']['url'].'" target="_blank">CedsonHub account</a>.'; }
            break;
            case 'expresscrypto': // checkUserHash: api_key, userId, user_token, ip_user
                $parms =  array('api_key' => $settings['api_key'], 'userId' => $address, 'user_token' => $settings['user_token'], 'ip_user' => getIP() );
                $response = getCURL($microwallets['expresscrypto']['api_base'].'checkUserHash', true, $parms );
                // response: status, message
                if($response['status'] && ($response['status'] == 404 || $response['status'] == 400) ){
                    $message =  $address.' does not belong to a <a class="alert-link" href="'.$microwallets['expresscrypto']['url'].'" target="_blank">ExpressCrypto account</a>.'; }
            break;
            case 'faucetfly':  // NO UserCheck
                if( !validAddress($address) ){ $message = $address.' doesnt appear to be an vaild '.$currencies[$settings['currency']].' Address'; }
            break;
            default:
                $parms =  array('api_key' => $settings['api_key'], 'address' => $address, 'currency' => $settings['currency'] );
                $response = getCURL($microwallets[$settings['microwallet']]['api_base'].'checkaddress', true, $parms );
                // response: status, message, payout_user_hash
                if($response['status'] && $response['status'] == 456){
                    $message =  $address.' does not belong to a <a class="alert-link"  href="'.$microwallets[$settings['microwallet']]['url'].'" target="_blank">'.$microwallets[$settings['microwallet']]['name'].' account</a>.'; }
            break;
        }
        
        if($message){
            userLog('action',(($response['status'])?: '400'), $message);
            return false;
        }
        else{ 
            if($response['payout_user_hash']){ $_SESSION[$faucetID]['user']['userhash'] = $response['payout_user_hash']; }
            $_SESSION[$faucetID]['user']['address'] = $address;
            $_SESSION[$faucetID]['user']['ip'] = getIP();
            $_SESSION[$faucetID]['user']['ref'] = (trim($_SESSION[$faucetID]['ref']))?: null; 
            unset($_SESSION[$faucetID]['ref']);
			setcookie($faucetID.'-address', $address, time()+2678400, '/', explode('/',$settings['domain'])[2]);
            userLog('action', 'login', 'Login Successful');
			return true;
        }
    }
    
    
}

## Check LastClaim
function checkLastClaim(&$mins = null){
    
    global $db, $faucetID, $settings, $_SESSION;
    
    if( $_SESSION[$faucetID]['user']['id'] ){
    
        $check = $db->query("
					SELECT `timestamp` 
					FROM `payouts-".$faucetID."` 
					WHERE (`address` = '".$_SESSION[$faucetID]['user']['address']."' OR `ip` = '".getIP()."') AND `type` = 'claim' 
					ORDER BY `id` DESC LIMIT 1
					");
        $check = ($check) ? $check->fetch_assoc() : '';
        if($check['timestamp']){
            $minsince = (intval(round((time() - strtotime($check['timestamp'].'+00:00')) / 60)))?: 0;
            if($minsince < $settings['timer']){
                $mins = $settings['timer']-$minsince;
                return false;
            }
        }
    }
    return true;
}

## Check Max Claims
function checkMaxClaims(){
	
	global $db, $faucetID, $settings, $_SESSION; 
	
	if( $_SESSION[$faucetID]['user']['id'] ){
	    
		$shortlinks = json_decode($settings['shortlinks'],true);
		
		$check = $db->query("
					SELECT COUNT(*) as `claims`  
					FROM `payouts-".$faucetID."` 
					WHERE (`address` = '".$_SESSION[$faucetID]['user']['address']."' OR `ip` = '".getIP()."') AND `type` = 'claim' AND  `timestamp` LIKE '".dateNow('today')."%'
					");
		$check = ($check) ? $check->fetch_assoc() : ['claims' => '0'];
		
		## USE MAX CLAIMS
		if( is_numeric($settings['max_claims']) && $settings['max_claims'] != 0){
		    return ( $check['claims'] >= $settings['max_claims'] ) ? false : true;
		}
		## USE SHORTLINKS
		elseif($settings['shortlinks'] && @array_sum(array_column($shortlinks, 'views')) ){
		    $settings['max_claims'] =  @array_sum(array_column($shortlinks, 'views'));
		    return ( ($check['claims'] >= $settings['max_claims']) && is_numeric($settings['max_claims']) ) ? false : true;
		}
		else {
			$settings['max_claims'] =  ceil(1440/$settings['timer']);
			return ( $check['claims'] >= $settings['max_claims'] ) ? false : true;
		}
		
	}
	return true;
}

## Check Session
function checkSession() {
    
	global $faucetID, $settings, $_SESSION;
	
	if( $_SESSION[$faucetID]['timeout'] && ($_SESSION[$faucetID]['timeout']<time()-($settings['timer']*60)) ){
		$_SESSION[$faucetID]['status'];
		return false;
	}
	return true;
}

## Get Shortlink
function getShortlink(){
	
	global $db, $faucetID, $settings, $_REQUEST, $_SERVER, $_SESSION;
	
	// Unset SL Hash
	unset($_SESSION[$faucetID]['hash']);	
	
	// Check Shortlink views
	$s = $db->query("
			SELECT `slid`, COUNT(*) AS `views`, MAX(`timestamp`) as `lastclaim` 
			FROM `payouts-".$faucetID."` 
			WHERE  (`address` = '".$_SESSION[$faucetID]['user']['address']."' OR `ip` = '".getIP()."') AND `timestamp` LIKE '".dateNow('today')."%' 
			GROUP BY `slid` ORDER BY `lastclaim` DESC
			");
	if($s){
		while ($row = $s->fetch_assoc()) {
			$v[$row['slid']] = $row;
		}
	};
		
	// Get Shortlink Array
	$sl = json_decode($settings['shortlinks'], true);
	if($sl){
	    
		// Sort Shortlinks by Priority, then views
		usort($sl, function($a,$b) {
			return $a['priority'] <=> $b['priority'] ?: $b['views'] <=> $a['views'];
		});
		 
		// Remove viewed shortlinks save link priority
		foreach($sl as $key => $value){
			if($v[$sl[$key]['id']]['views'] >= $sl[$key]['views']){
				unset($sl[$key]);
			}
			elseif($value['priority']){
				$new[$sl[$key]['id']] = $value;
				unset($sl[$key]);
			}
		}
			
		// Shuffle Remaining shortlinks
		$keys = ($sl)? array_keys($sl) : ''; 
		if($keys){
			shuffle($keys); 
			foreach ($keys as $key) { 
				$new[$sl[$key]['id']] = $sl[$key]; 
			}
		}
			
		// Get a shortlink
		if($new){
		    
		    foreach($new as $new_link){ 
			    // Recycle User Shortlinks 
			    $links = $db->query("SELECT `token`,`slid`,`shortlink` FROM `payouts-".$faucetID."` WHERE  (`address` != '".$_SESSION[$faucetID]['user']['address']."' OR `ip` != '".getIP()."') AND `slid` = '".$new_link['id']."' AND `timestamp` > DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY `token` ORDER BY RAND()");
			    if($links->num_rows >= 10){
                    while($row = $links->fetch_assoc()){
                        $recycled[] = $row;
                    }
                    $recycled = $recycled[mt_rand(0,($links->num_rows-1))];
                    $_SESSION[$faucetID]['shortlink']['token'] = $recycled['token'];
			        $_SESSION[$faucetID]['shortlink']['id'] = $recycled['slid'];
    				$_SESSION[$faucetID]['shortlink']['link'] = $recycled['shortlink'];
    				$_SESSION[$faucetID]['shortlink']['time'] = time();
    				userLog('action', 'shortlink', 'Went to '.$recycled['shortlink'].' [Recycled]');
    				redirect($recycled['shortlink']);
    				exit;
			    }
			    // Create New Shortlink
			    else {
			        // Generate Token
		            $token = $new_link['id'].'.'.substr(md5(time().$_SESSION[$faucetID]['user']['address']),0,25);
		            // Format API Link
		            $apilink = str_replace('?api={apikey}&url={url}', '?api={apikey}&alias={alias}&url={url}',$new_link['apilink']);
		            $apilink = str_replace(array('{apikey}', '{url}', '{alias}'), array($new_link['apikey'], $settings['domain'].'?token='.$token, 'GR8'.randHash(8)), $apilink);
            		
            		// Get Shortlink
            		$shortlink = getCURL($apilink);
            		$shortlink = (strstr($shortlink,'":"'))? current(preg_grep('~^http~',json_decode($shortlink,true))) : trim($shortlink); 
            		
            		// Verify Good Shorlink
            		if(!strpos($shortlink, 'api/') && !strpos($shortlink, 'api.php')  && filter_var($shortlink, FILTER_VALIDATE_URL)){
    					$_SESSION[$faucetID]['shortlink']['id'] = $new_link['id'];
    					$_SESSION[$faucetID]['shortlink']['token'] = $token;
    					$_SESSION[$faucetID]['shortlink']['link'] = preg_replace("/\s\s+/", " ", $shortlink);
    					$_SESSION[$faucetID]['shortlink']['time'] = time();
    					userLog('action', 'shortlink', 'Went to '.$shortlink);
    					redirect($shortlink);
    					exit;
    				    break;
    				}
    				else{
    					userLog('error', 'link-error', $apilink);
    					return false;
    				}
			    }
			    
			} // foreach
		} // if $new
			
	} // if $sl
	
	// If no Shortlinks	
	if(!$_SESSION[$faucetID]['shortlink']){
		#$_SESSION[$faucetID]['shortlink']['id'] = '0';
		#$_SESSION[$faucetID]['status']  = 'payout-ready';
		return;
	}
}

## Check Shortlink
function checkShortlink(){
    
    global $db, $faucetID, $settings, $_GET, $_SESSION;
    
    if($_SESSION[$faucetID]['shortlink']){
        
        // Get Shortlink timer
    	$settings['shortlink_timer'] = ($settings['shortlink_timer'])?: '8';
    		
    	// Check if Shortlink ByPassed
    	if($_SESSION[$faucetID]['shortlink']['time'] > time()-$settings['shortlink_timer']){		
    		userLog('action', 'shortlink', 'Shortlink Speeder '.$_SESSION[$faucetID]['shortlink']['link'].' Shorttime: '.(time() - $_SESSION[$faucetID]['shortlink']['time']).' sec');
    		unset($_SESSION[$faucetID]['shortlink']);
    		return false;
    	}
    	## Check if Token Macthes
    	elseif($_SESSION[$faucetID]['shortlink']['token'] == $_GET['token']){
    	    
    	   // Get Link ID
    		$id = explode('.', sanitize($_GET['token']))[0];	
    			
    		// Get Shortlink Info
    		$link = json_decode($settings['shortlinks'], true)[$id];
    		
    		// If Valid Shortlink
    		if($link['id']){
    				
    			// Check Userviews for link
    			$check = $db->query("SELECT `slid` FROM `payouts-".$faucetID."` WHERE (`address` = '".$_SESSION[$faucetID]['user']['address']."' OR `ip` = '".getIP()."') AND `slid` = '".$link['id']."' AND `timestamp` LIKE '".dateNow('today')."%'");
    			$check = ($check) ? $check->num_rows : '0';
    			
    			// If Views > Userviews
    			if($link['views'] > $check){
    				$_SESSION[$faucetID]['status']  = 'payout-ready';
    				redirect(getCurrentURL());
    				exit;
    			}
    			// Reset
    			else {
    			    unset($_SESSION[$faucetID]['shortlink']);
    			    return false;
    			}
    		}
    	}
    	## Verification Failed
    	else {
    	    // Log failure
    		userLog('action', 'shortlink', 'Verification failed: '.$_SESSION[$faucetID]['shortlink']['link'].' Shorttime: '.(time() - $_SESSION[$faucetID]['shortlink']['time']).' sec');
    		$_SESSION[$faucetID]['shortlink']['time'] = time();
    		
    		// If not online unset shortlink	
    		if(!isOnline($_SESSION[$faucetID]['shortlink']['link']) ){
    			unset($_SESSION[$faucetID]['shortlink']);
    		}
    		return false;
    	}
    }
    return true;
}

## Send Microwallet Payout  
function send($to, $amount, $referral = false, $currency = '') {
       
    global $microwallets, $settings;
        
    switch($settings['microwallet']) {
        case 'cedsonhub': 
            $amount_in_satoshis = ltrim(substr($amount, -8), '0');
            $amount_in_coins = (strlen($amount) > 8)? ($amount-$amount_in_satoshis)/100000000 : 0;
            // payout: api_key, to, currency, amount_in_coins, amount_in_satoshis
            $parms =  array('api_key' => $settings['api_key'], 'to' => $to, 'currency' => (($currency)?:$settings['currency']), 'amount_in_coins' => $amount_in_coins, 'amount_in_satoshis' => $amount_in_satoshis );
            $response = getCURL($microwallets['cedsonhub']['api_base'].'payout', true, $parms );
            // response: currency, amount_in_coins, amount_in_satoshis, date, time, to, balance, payout_id
            $response['status'] = $response['httpcode'];
        break;
        case 'cryptoo': 
            // send: api_key, to, amount, referral, ip
            $parms =  array('api_key' => $settings['api_key'], 'to' => $to, 'amount' => $amount, 'referral' => $referral, 'ip' => (($referral)? getIP() : '') );
            $response = getCURL($microwallets['cryptoo']['api_base'].'send', true, $parms );
            // response: status, balance, balance_bitcoin, message
        break;
        case 'expresscrypto': 
			if(!$referral){
				// sendPayment: api_key, user_token, userId, currency, amount, ip_user
				$parms =  ['api_key' => $settings['api_key'],'user_token' => $settings['user_token'],'userId' => $to,'currency' => (($currency)?:$settings['currency']),'amount' => $amount, 'ip_user' => getIP()];
				$response = getCURL($microwallets['expresscrypto']['api_base'].'sendPayment', true, $parms );
				// response: status, message, balance, txid 
			} else {
				// sendReferralCommission: api_key, user_token, userId, currency, amount, ip_user
				$parms =  ['api_key' => $settings['api_key'],'user_token' => $settings['user_token'],'userId' => $to,'currency' => (($currency)?:$settings['currency']),'amount' => $amount, 'ip_user' => ''];
				$response = getCURL($microwallets['expresscrypto']['api_base'].'sendReferralCommission', true, $parms );
				// response: status, message, balance, txid 
			}
        break;
        case 'faucetfly': 
            // send: api_key, to, amount, referral, currency
            $parms =  array('api_key' => $settings['api_key'], 'to' => $to, 'amount' => $amount, 'referral' => $referral, 'currency' => (($currency)?:$settings['currency']) );
            $response = getCURL($microwallets['faucetfly']['api_base'].'send', true, $parms );
            // response: status, balance, balance_bitcoin, message
        break;
        default: 
            // send: api_key, to, amount, currency, referral, ip_address
            $parms =  array('api_key' => $settings['api_key'], 'to' => $to, 'amount' => $amount, 'currency' => (($currency)?:$settings['currency']), 'referral' => $referral, 'ip_address' => (($referral)? getIP() : '') );
            $response = getCURL($microwallets[$settings['microwallet']]['api_base'].'send', true, $parms );
            // response: status, message, currency, balance, balance_bitcoin, payout_id, payout_user_hash
        break;
    }
    
    if(!$response['status']){
        $response['error'] = '999';
        $response['message'] = $microwallets[$settings['microwallet']]['name'].' appears offline, try later';
    }
    elseif($response['status'] != 200){
        $response['error'] = $response['status'];
        $response['message'] = ($response['message2'])?: (($response['message'])?: (($response['detail']?: 'Unknown error')) );
    }
    else {
        $response['message'] = ($response['message2'])?: (($response['message'])?: (($response['detail']?: '')) );
        $response['balance'] = ($response['balance'])?: 0;
    }
    
    return $response;
}

## Send User Payout
function sendPayout(&$message = null){
    
	global $db, $faucetID, $microwallets, $settings, $_SERVER, $_SESSION;
	
	// Address
	$address = $_SESSION[$faucetID]['user']['address'];
	// Get reward
	$amount = getReward($settings['reward']);						
	
	// Send user payout					
	$sendPayout = send($address, $amount, "false");
	$ramount = ($settings['referral'])? ((($amount/100)*$settings['referral'] > '0.99')? intval(ceil(($amount/100)*$settings['referral'])) : '1') : '';
	
	// If Error
	if(!$sendPayout['error']){
		
		$_SESSION[$faucetID]['status'] = 'paid';
		$_SESSION[$faucetID]['$message'] = '<i class="fas fa-money-bill-wave"></i> '.$amount.' satoshi was sent to your <a href="'.str_replace(array('{currency}','{address}'),array($settings['currency'],$address),$microwallets[$settings['microwallet']]['check']).'" target="_blank">'.$microwallets[$settings['microwallet']]['name'].' Account</a>';
		
		// Update faucet balance
		$db->query("UPDATE `settings-".$faucetID."` SET `value` = '".$sendPayout['balance']."' WHERE `name` = 'balance'");
	    
	    // Update User last action
	    if($_SESSION[$faucetID]['user']['id']){
	        $db->query("UPDATE `users-".$faucetID."` SET `last_action` = '".dateNow()."' WHERE `id` = '".$_SESSION[$faucetID]['user']['id']."'");
	    }
	    // Insert new user
	    else{
	        $db->query("INSERT INTO `users-".$faucetID."` VALUES (null, '$address', '".$_SESSION[$faucetID]['user']['ip']."', '".$_SESSION[$faucetID]['user']['ref']."', 'active', '".dateNow()."', null)");
	        if($db->insert_id){ $_SESSION[$faucetID]['user']['id'] = $db->insert_id; }
	    }
		// Insert payout data
		$db->query("INSERT INTO `payouts-".$faucetID."` VALUES (null, '".$address."', '".getIP()."', '".$amount."', '".((strstr($settings['reward'],'.'))? number_format($settings['reward'],5): getRate($amount,'usd')['usd'])."', '".$settings['currency']."', 'claim', '".$_SESSION[$faucetID]['shortlink']['token']."', '".$_SESSION[$faucetID]['shortlink']['id']."', '".$_SESSION[$faucetID]['shortlink']['link']."', '".$_SESSION[$faucetID]['ip_check'][getIP()]['data']['asn']."', '".$_SESSION[$faucetID]['ip_check'][getIP()]['data']['countryCode']."', '".$_SESSION[$faucetID]['ip_check'][getIP()]['data']['os']."', '".$_SESSION[$faucetID]['ip_check'][getIP()]['data']['device']."', '".$_SESSION[$faucetID]['ip_check'][getIP()]['data']['browser']."', '".addslashes($_SERVER['HTTP_USER_AGENT'])."', '".addslashes($_SERVER['HTTP_REFERER'])."', '".json_encode($sendPayout)."', '".dateNow()."')");

		userLog('action',$sendPayout['status'], 'Paid '.$amount.' satoshi '.(($_SESSION[$faucetID]['shortlink']['link']) ?: '').(($_SESSION[$faucetID]['ref']) ? '['.$_SESSION[$faucetID]['ref'].']' : '').' Shorttime: '.(time() - $_SESSION[$faucetID]['shortlink']['time']).' sec');
		
	    ## PAY REF
	    if($_SESSION[$faucetID]['user']['ref'] && $ramount){
			sendRefPayout($_SESSION[$faucetID]['user']['ref'], $ramount);
		}
		
	    redirect(getCurrentURL());
	} 
	
	else {
	    $message = $sendPayout['message'];
		userLog('action',$sendPayout['error'], $sendPayout['message']);
		return false;
	}
	## END PAYOUT
	return $sendPayout;
} 
 

// Send Ref Payout
function sendRefPayout($ref, $amount){
	
	global $db, $faucetID, $settings, $_SESSION;
			
	// Remove Self Referred
	if($ref == $_SESSION[$faucetID]['user']['address']){
		$db->query("UPDATE `users-".$faucetID."` SET `ref` = '' WHERE `id` = '".$_SESSION[$faucetID]['user']['id']."'");
		userLog('action', 'self_ref', $_SESSION[$faucetID]['user']['ref'].' Self Referral Removed');
	}
	// Send Ref Payout
	else{
			
		// Send
		$sendPayout = send($ref, $amount, true);
		
		// If Send error		
		if($sendPayout['error']){
		    
		    ## REMOVE INVALID REF ADDRESS
		    // cedsonhub
            if($settings['microwallet'] == 'cedsonhub' && !$sendPayout['payout_id']){
		        $sendPayout['error'] = '999';
		        $db->query("UPDATE `users-".$faucetID."` SET `ref` = '' WHERE `id` = '".$_SESSION[$faucetID]['user']['id']."'");
            }
		    // Cryptoo - 412 Invalid 'to' address
		    elseif($settings['microwallet'] == 'cyptoo' && $sendPayout['error'] == '412'){
		        $db->query("UPDATE `users-".$faucetID."` SET `ref` = '' WHERE `id` = '".$_SESSION[$faucetID]['user']['id']."'");
		    }
		    // ExpressCrypto - 404 No account exist under this Id in ExpressCrypto.io
		    elseif($settings['microwallet'] == 'expresscrypto' && $sendPayout['error'] == '404'){
		        $db->query("UPDATE `users-".$faucetID."` SET `ref` = '' WHERE `id` = '".$_SESSION[$faucetID]['user']['id']."'");
		    }
		    // FaucetFly - no detection
		    elseif($settings['microwallet'] == 'faucetfly' && !validAddress($ref)){
		        $sendPayout['error'] = '999';
		        $db->query("UPDATE `users-".$faucetID."` SET `ref` = '' WHERE `id` = '".$_SESSION[$faucetID]['user']['id']."'");
		    }
		    // FaucetPay - 456	The address does not belong to any user.
		    elseif($settings['microwallet'] == 'faucetpay' && $sendPayout['error'] == '456'){
		        $db->query("UPDATE `users-".$faucetID."` SET `ref` = '' WHERE `id` = '".$_SESSION[$faucetID]['user']['id']."'");
		    }
		    // Microwallet - 456	The address does not belong to any user.
		    elseif($settings['microwallet'] == 'microwallet' && $sendPayout['error'] == '456'){
		        $db->query("UPDATE `users-".$faucetID."` SET `ref` = '' WHERE `id` = '".$_SESSION[$faucetID]['user']['id']."'");
		    }
		    
			userLog('action', $sendPayout['error'], 'Ref Payout: '.$_SESSION[$faucetID]['user']['ref'].' '.$sendPayout['message']);
		}
		// Insert Payout Data
		else {
			userLog('action', $sendPayout['status'], 'Ref Paid '.$_SESSION[$faucetID]['user']['ref'].' '.$amount.' satoshi');
			$q = "INSERT INTO `payouts-".$faucetID."` (`address`, `ip`, `reward`, `usd`, `currency`, `type`, `json`, `timestamp`) 
						VALUES ('".$ref."', 'Referral Payout', '".$amount."', '".((strstr($settings['reward'],'.'))? number_format($settings['reward'],5): getRate($amount,'usd')['usd'])."', '".$settings['currency']."', 'referral', '".json_encode($sendPayout)."', '".dateNow()."')";
			if(!$db->query($q)){
				@file_put_contents('ref.txt', $q);
			}
		} 
	}	
	return $sendPayout;
}

// Create Log files
function userLog($type, $status, $note){
    
    global $db, $faucetID, $_POST, $_SESSION;
    
    // Insert Log
    $db->query("INSERT INTO `logs-".$faucetID."` VALUES (null, '".(($_SESSION[$faucetID]['user']['address'])?:(sanitize($_POST['address'])?: 'unknown'))."', '".getIP()."','$type','$status','$note','".dateNow()."')");
    // Delete older than 30 days
    $db->query("DELETE FROM `logs-".$faucetID."` WHERE `timestamp` < (NOW() - INTERVAL 30 DAY)");
    
}


