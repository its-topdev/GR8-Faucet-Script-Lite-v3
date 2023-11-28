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


## Initiate Script Requirements
include 'script/ini.php';

## Check Required Settings
if( !$settings['api_key'] || !$settings['reward']  || !$settings['timer'] ){
	$error = alert('<i class="fas fa-exclamation-triangle"></i> A GR8 Faucet is being made, come back soon!', 'warning');
	goto faucet;
}

## Check Faucet Disabled
if($settings['status'] == 'N'){
	$error = alert('<i class="fas fa-exclamation-triangle"></i> Sorry our faucet is not active, please come back later.', 'danger');
	goto faucet;
}

## Check JS Enabled
if($_GET['e'] == 'nojs'){
    exit('<h1 style="margin: 20px auto;text-align: center;">Sorry <a href="'.$settings['domain'].'">'.$settings['name'].'</a> requires Javascript<br><small>Please enabled it and <a href="'.$settings['domain'].'">try again</a>.</small></h1>');
}


## Check if User is logged in
if(!$error && !$_SESSION[$faucetID]['user'] && !$_POST){
   $_SESSION[$faucetID]['status'] = 'login';
   goto faucet;
}

## User Login
if(!$error && $_POST['address']){

    ## Check Proxy
    if( isProxy() ){
    	$error = alert('<i class="fas fa-exclamation-triangle"></i> Access Denied: '.getIP(). ' blocked by Proxy Detection','danger');
    	goto faucet;
    }

    // Verify Session
    if(!checkToken()){
        $error = alert('<i class="fas fa-exclamation-triangle"></i> Session invalid, try again','danger');
        goto faucet;
    }

    // Check AntiBot
    if(!checkAntibot()){
        $error = alert('<i class="fas fa-exclamation-triangle"></i> Antibotlinks were not in correct order, try again','danger');
        goto faucet;
    }

    // Check Captcha
    if(!checkCaptcha()){
        $error = alert('<i class="fas fa-exclamation-triangle"></i> Captcha was invaild, try again','danger');
        goto faucet;
    }

    // Check UserAddress
    if(!checkAddress($message)){
        $error = alert('<i class="fas fa-exclamation-triangle"></i> '.$message,'danger');
        goto faucet;
    }

    // Change Claim Status
	if(!$error){
		$_SESSION[$faucetID]['status'] = ($settings['shortlinks'] && $settings['shortlinks'] != 'null')? 'shortlink' : 'payout-ready';
		redirect(getCurrentURL());
	}

}

## CHECK/SET SHORT LINK
if(!$error && ($_SESSION[$faucetID]['status'] == 'shortlink')){

	// Check IP Changed
	if(!IPchange($message)){
	    $error = alert('<i class="fas fa-exclamation-triangle"></i> '.$message,'danger');
		goto faucet;
	}

	// Check Shortlink Verification
	if(!checkShortlink()){
		$error = alert('<i class="fas fa-exclamation-triangle"></i> Sponsored link verification failed, please try again.','danger');
		goto faucet;
	}
	## Get Shortlink
	elseif($_GET['hash'] && $_SESSION[$faucetID]['hash'] == $_GET['hash']){
		if(!getShortlink()){
    		$error = alert('<i class="fas fa-exclamation-triangle"></i> Failed to get a Sponsor\'s link, please try again.','danger');
    		goto faucet;
		}
	}
}

## PAY USER
if(!$error && ($_SESSION[$faucetID]['status'] == 'payout-ready')){

    // Send Payout
	if(!sendPayout($message)){
	    #$_SESSION[$faucetID]['status']  = 'login';
		$error = alert('<i class="fas fa-exclamation-triangle"></i> '.$message,'danger');
		goto faucet;
	}
}

## USER PAID
if(!$error && ($_SESSION[$faucetID]['status'] == 'paid')){

	// If paid
	if($_SESSION[$faucetID]['$message']){
		$error = alert($_SESSION[$faucetID]['$message'],'success');
		unset($_SESSION[$faucetID]['$message']);
		goto faucet;
	}
	// Check Last Claim
    elseif(!checkLastClaim($mins)){
        $error = alert('<i class="fas fa-exclamation-triangle"></i> You have to wait '.(($mins > '1')? $mins.' minutes' : $mins.' minute'),'danger');
        $_SESSION[$faucetID]['status'] = 'paid';
        goto faucet;
    }
    // Reset Claim Process
	else{
		$_SESSION[$faucetID]['status'] = 'login';
		redirect(getCurrentURL());
	}
}


## Start Faucet template
faucet:
$settings['theme'] = ($_GET['theme']) ?: $settings['theme'];
$template = ($_GET['template']) ?: 'default';
if(file_exists('templates/'.$template.'/index.php')){
	include 'templates/'.$template.'/index.php';
}
else { die('Missing "'.$template.'" Faucet Template'); }

$db->close();
