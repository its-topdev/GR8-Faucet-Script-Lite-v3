<?php

/*
 * GR8 Faucet Script Lite
 * https://gr8.cc
 *
 * Copyright 2019 GR8 script, AvalonRychmon
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

## VERSION
$version = '1';

## SET TIMEZONE
date_default_timezone_set('UTC');

## START SESSIONS
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

## ROOTPATH
define("ROOTPATH", dirname(dirname(__FILE__)) . "/");

## INCLUDE CONFIG
require ROOTPATH.'config.php';

## ERROR REPORTING
if($show_errors){
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
}
else {
	error_reporting(30719 & ~8);
}

## CONNECT TO DATABASE
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $db = new mysqli($host, $username, $password, $database);
    $db->set_charset("utf8mb4");
    #$db->query("SET time_zone = '+00:00'");
} catch(Exception $e) {
    error_log($e->getMessage());
    exit('<h1 style="margin: 20px auto;text-align: center;">We\'re having an issue connecting to our database.<br><small>Don\'t worry, it will be fixed soon.</small></h1>');
}

## START PHPFASTCACHE
if(!is_writable(ROOTPATH.'libs/cache/cache/')){ chmod(ROOTPATH.'libs/cache/cache/', 0755);}
include ROOTPATH.'libs/cache/phpfastcache.php';
phpFastCache::setup('storage', 'files');
phpFastCache::setup('path', ROOTPATH.'libs/cache/cache/');
$cache = phpFastCache();

## INCLUDE CORE
include ROOTPATH.'script/core.php';

## INCLUDE ANTIBOT
include ROOTPATH.'libs/antibot.php';

## INCLUDE MICROWALLETS
include ROOTPATH.'script/microwallets.php';

## INCLUDE FUNCTIONS
include ROOTPATH.'script/functions.php';

## Get Faucet Settings
$settings = getSettings();

## Check if installed
if(!strpos(getCurrentURL(),'admin') && $db && !$settings){
    include ROOTPATH.'script/install.php';
    exit;
}

## Check if current version
if(strpos(getCurrentURL(),'admin') && $settings['version']<$version) {
    include ROOTPATH.'script/install.php';
    exit;
}

## SET HTTP REFERRER
if($_SERVER['HTTP_REFERER'] || $_SERVER['HTTP_REFERRER'] ) {
    $_SESSION[$faucetID]['referrer'] = ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : (($_SERVER['HTTP_REFERRER']) ? $_SERVER['HTTP_REFERRER'] : '');
}

## SET REF ADDRESS
$_SESSION[$faucetID]['ref'] = (trim($_GET['r']))?: null;

## IF DISABLE IFRAME
if ($settings['disable_iframes']) {
    header("X-Frame-Options: SAMEORIGIN");
}

header('X-XSS-Protection:0');
