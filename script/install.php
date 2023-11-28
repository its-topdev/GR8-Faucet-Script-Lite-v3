<?php 

## FAUCET URL
$url = rtrim(getCurrentURL(),'/');

## MISC VARIABLES
$version = '3';
$installed = ($settings['version'])?: 0;

$updates = array( 
    1 => array(
        "CREATE TABLE `logs-".$faucetID."` (
            `id` int(100) NOT NULL AUTO_INCREMENT,
            `address` varchar(255) NOT NULL,
            `ip` varchar(255) NOT NULL,
            `type` varchar(255) NOT NULL,
            `status` varchar(255) NOT NULL,
            `notes` varchar(255) NOT NULL,
            `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    	    PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
        
        "create table if not exists `payouts-".$faucetID."` (
    		`id` int(100) NOT NULL AUTO_INCREMENT,
    		`address` varchar(255) NOT NULL,
    		`ip` varchar(255) NOT NULL,
    		`reward` varchar(255) NOT NULL,
    		`usd` varchar(255) NOT NULL,
    		`currency` varchar(255) NOT NULL,
    		`type` varchar(255) NOT NULL,
    		`token` varchar(255) NOT NULL,
    		`slid` varchar(10) DEFAULT NULL,
    		`shortlink` varchar(255) DEFAULT NULL,
    		`asn` varchar(255) DEFAULT NULL,
    		`country` varchar(255) NOT NULL DEFAULT 'other',
    		`os` varchar(255) NOT NULL DEFAULT 'other',
    		`device` varchar(255) NOT NULL DEFAULT 'other',
    		`browser` varchar(255) NOT NULL DEFAULT 'other',
    		`user_agent` longtext DEFAULT NULL,
    		`referrer` varchar(255) DEFAULT NULL,
    		`json` longtext NOT NULL,
    		`timestamp` timestamp NULL DEFAULT NULL,
    		PRIMARY KEY (`id`),
    		KEY `address` (`address`),
    		KEY `ip` (`ip`),
    		KEY `slid` (`slid`)
    	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
    	
        "create table if not exists `settings-".$faucetID."` (
    		`name` varchar(64) not null,
    		`value` longtext not null,
    		PRIMARY KEY (`name`)
    	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",

        "create table if not exists `users-".$faucetID."` (
          `id` int(100) NOT NULL AUTO_INCREMENT,
          `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `ref` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `status` enum('active','locked','banned') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
          `last_action` timestamp NULL DEFAULT NULL,
          `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `address` (`address`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
        
        "INSERT IGNORE INTO `settings-".$faucetID."` (`name`, `value`) VALUES
        ('name', 'GR8 Faucet Lite'),
        ('domain', '".$url."'),
        ('description',''),
        ('microwallet', ''),
        ('api_key', ''),
        ('user_token', ''),
        ('currency', ''),
        ('timer', ''),
        ('referral',''),
        ('reward', ''),
        ('balance','0'),
        
        ('max_claims',''),
        ('primary_captcha','solvemedia'),
        ('secondary_captcha','none'),
        ('solvemedia_keys','{\"challenge_key\":\"\",\"verification_key\":\"\",\"hash_key\":\"\"}'),
        ('recaptcha_keys','{\"site_key\":\"\",\"secret_key\":\"\"}'),
        ('shortlink_timer','8'),
        ('iphub_api',''),
        ('proxycheck_api',''),
        ('disable_balance',''),
        ('disable_antibot',''),
        ('disable_iframes',''),
        
        ('top_ads',''),
        ('left_ads',''),
        ('middle_ads',''),
        ('right_ads',''),
        ('bottom_ads',''),
        ('paid_box',''),
        
        ('theme','default'),
        ('antibot_theme','dark'),
        ('css',''),
        ('navlinks',''),
        
        ('shortlinks',''),
        
        ('version', '1');"
    )
);

## CHECK DB SETTINGS
if(!$host || !$database || !$username || !$password){
    $title = 'Check required extensions:';
    $progress = '10';
	$error = alert('Database login details required in the config.php file', 'danger');
	goto install;
}

## CHECK LOADED EXTENTIONS
if(!$error){
    $ext['php'] = (bool)(phpversion() >= '7.1');
    foreach(array('curl','gd','json','mysqli','session') as $e){
    	$ext[$e] = extension_loaded($e);
    }
    $required = array_filter($ext, function($x) { return (is_null($x) || $x === false); });
    if($required) {
        $title = 'Check required extensions:';
    	foreach($required as $ext => $value){
    		if($ext == 'php'){
    			$error[] = 'PHP 7.1';
    		}
    		else {
    			$error[] = $ext;
    		}
    		
    		$progress = '20';
    		$error = alert('ERROR: '.implode(', ',$error).' are Required! Please fix to continue', 'danger');
    	}
    	goto install;
    }
}

## INSTALL/UPDATE DB
if(!$error){
    // Update Database
    foreach ($updates as $v => $update) {
        if ($v <= $installed) continue;
		foreach($update as $query){
            if(!$db->query($query)){
        	    $title = 'Database install:';
		        $progress = '40';
        	    $error = alert($db->error, 'danger');
        		goto install;
			}
			usleep(2500);
        }
    }
	// Update Version		
	if($version > $installed){
		if(!$db->query("UPDATE `settings-".$faucetID."` SET `value` = '".$version."' WHERE `settings-".$faucetID."`.`name` = 'version'")){
			$title = 'Database install:';
		    $progress = '40';
        	$error = alert($db->error, 'danger');
        	goto install;
		}
	}	
	
	// Clear $db
    $db->next_result();
    
	// Update Settings
	getSettings('update'); 
}	

## CLEANUP INSTALL FILES
if(!$error){
	
	// Delete Zip Files
	foreach (glob(ROOTPATH."GR8 Faucet Script Lite v*.zip") as $file) {  
		unlink($file);
	}
	// Delete Instructions.html
	unlink(ROOTPATH.'Instructions.html');
	
	// Finished - Redirect to admin
	redirect($url.'/admin/settings.php');
	exit;
}


// Start Install template
install:
?>

<!-- HTML START -->
<!DOCTYPE html>
<html lang="en"> 
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <noscript><meta http-equiv="refresh" content="0; url=<?= $settings['domain'];?>?e=nojs"></noscript>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GR8 Faucet Script (Lite) Install</title>
        <meta name="description" content="">
    	<meta name="keywords" content="">
    	<meta name="robots" content="noindex,nofollow">
    	<!-- FAUCET FAVICON -->
		<link href="data:image/x-icon;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAQAABMLAAATCwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAASkpKAEpKSgBKSkqBSkpK/kpKStxKSkokSkpKAAAAAAAAAAAAAAAAAAAAAAAAAAAASkpKAEpKSgBKSkoVSkpKHkpKSgFKSkoUSkpKvEpKSv9KSkr7SkpKb0pKSgxKSkolSkpKaEpKSiBKSkoASkpKAEpKSgBKSkoaSkpKr0pKStdKSkqQSkpKvEpKSvlKSkr/SkpK/0pKSvJKSkrFSkpK2EpKSv5KSkq2SkpKFUpKSgBKSkoASkpKWkpKSvpKSkr/SkpK/0pKSv9KSkr/SkpK/0pKSv9KSkr/SkpK/0pKSv9KSkr/SkpKzUpKShhKSkoASkpKAEpKShxKSkrNSkpK/0pKSv9KSkr/SkpK/0pKSvxKSkr8SkpK/0pKSv9KSkr/SkpK/0pKSolKSkoASkpKAEpKSgBKSkoOSkpKwEpKSv9KSkr/SkpK+0pKSrBKSkpaSkpKWUpKSq5KSkr7SkpK/0pKSv9KSkrASkpKGEpKSgBKSkojSkpKcUpKSvJKSkr/SkpK/0pKSrBKSkoSSkpKAEpKSgBKSkoRSkpKrUpKSv9KSkr/SkpK+kpKSsVKSkqKSkpK3UpKSvtKSkr/SkpK/0pKSvxKSkpaSkpKAEpKSgBKSkoASkpKAEpKSlZKSkr7SkpK/0pKSv9KSkr/SkpK+kpKSvZKSkr/SkpK/0pKSv9KSkr8SkpKWUpKSgBKSkoASkpKAEpKSgBKSkpWSkpK+0pKSv9KSkr/SkpK90pKStJKSkpzSkpKtEpKSvhKSkr/SkpK/0pKSq5KSkoRSkpKAEpKSgBKSkoPSkpKq0pKSv9KSkr/SkpK80pKSmZKSkoYSkpKAEpKShJKSkq9SkpK/0pKSv9KSkr7SkpKrUpKSlZKSkpVSkpKq0pKSvpKSkr/SkpK/0pKSsZKSkoRSkpKAEpKSgBKSkoASkpKi0pKSv9KSkr/SkpK/0pKSv9KSkr7SkpK+0pKSv9KSkr/SkpK/0pKSv9KSkrXSkpKJUpKSgBKSkoASkpKGUpKStBKSkr/SkpK/0pKSv9KSkr/SkpK/0pKSv9KSkr/SkpK/0pKSv9KSkr/SkpK/EpKSmRKSkoASkpKAEpKShFKSkqoSkpK+0pKSs1KSkrDSkpK8kpKSv9KSkr/SkpK+UpKSsFKSkqFSkpKx0pKSqxKSkoaSkpKAEpKSgBKSkoASkpKGEpKSldKSkocSkpKDUpKSmhKSkr4SkpK/0pKSrxKSkoVSkpKAEpKShRKSkoSSkpKAEpKSgAAAAAAAAAAAAAAAAAAAAAAAAAAAEpKSgBKSkodSkpK00pKSvpKSkp9SkpKAEpKSgAAAAAAAAAAAAAAAAAAAAAA/D8AAMADAACAAQAAgAEAAIADAACAAQAAAYAAAAPAAAADwAAAAYAAAIABAADAAQAAgAEAAIABAADAEwAA/D8AAA==" rel="icon" type="image/x-icon" />
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/v4-shims.css">
            
    	<style>
    	    body{ min-height:100vh }
            .flex-grow { flex: 1 }
			.card { margin: 60px auto;max-width: 600px; }
		
		</style>
	</head>
	
	<!-- START BODY -->
    <body class="d-flex flex-column">
	    <!-- START MAIN CONTAINER -->
        <div class="container flex-grow my-4">
            <div class="row p-0 m-0">
    		    
            	<!-- Main Container -->
            	<div class="col-12">
        			<div class="card">
        				<h3 class="card-header text-center">GR8 Faucet Script Lite Installation</h3>
        				<div class="progress" style="border-radius:0">
        					<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progress;?>%;background-color:#000000!important" aria-valuenow="<?= $progress;?>" aria-valuemin="0" aria-valuemax="100"><?= $progress;?>%</div>
        				</div>
        				<div class="card-body">
        					<h4 class="card-title"><?= $title;?></h4>
        					<div class="card-text"><?= $error;?></div>
        				</div>
        			</div>
        		</div>
        	</div>	
		</div>
			
		<!-- Footer -->
        <footer class="py-3">
            <div class=" text-center">
                <div class="col-12">
                    Copyright© 2016-<?= date('Y');?> <b>GR8 Faucet Script Lite <span class="align-text-bottom"><small>v<?= $version;?></small></span></b>
                </div>
            </div>
        </footer>
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <!-- Popper -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <!-- Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        
   </body>
</html>		