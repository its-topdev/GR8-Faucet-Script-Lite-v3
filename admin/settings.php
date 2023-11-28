<?php 

## GR8 FAUCET SCRIPT LITE - ADMIN SETTINGS ##

## Initiate Script Requirements
include '../script/ini.php';

## Login
if($_POST['password']){ 
    if($_POST['password'] == $password){ 
    	$_SESSION[$faucetID.'-admin'] = 'logged';
    }
    else {
        $error = alert('Invalid Password!', 'danger');
    }
}
elseif($_GET['a'] == 'logout'){
    unset($_SESSION[$faucetID.'-admin'] );
}
## Update Settings
elseif(isset($_POST['update'])){
    
    // Fix Captcha Keys
    foreach(array_keys($captchas) as $captcha){
		if(isset($_POST[$captcha])){ 
			$_POST[$captcha.'_keys'] = json_encode($_POST[$captcha]);
			unset($_POST[$captcha]);
		}
	}
	
	// Fix Shortlinks
	if($_POST['shortlinks']){
    	foreach($_POST['shortlinks'] as $id => $data){
    		if($data['apikey'] && $data['views'] && ($data['enabled'] == 'Y')){
    			$s['shortlinks'][$id] = $data;
    		}
    	}
    	unset($_POST['shortlinks']);
    	$_POST['shortlinks'] = json_encode($s['shortlinks']);
	}
	
    // Update Query
	foreach($_POST as $key => $value){
	    if(!in_array($key, array('update','tab'))){
	        
	        $update = $db->prepare("UPDATE `settings-".$faucetID."` SET value = ? WHERE name = ? LIMIT 1");
	        $type = ((is_numeric($value)&& floor( $value ) != $value)? 'ds' : (is_numeric($value)? 'is' : 'ss'));
	        if( $update &&
	            $update->bind_param($type, $value, $key) &&
	            $update->execute() 
	        ) { 
	            $result = alert('Setting updated successfully!', 'success', 'close');
	            $settings = getSettings('update');
	        }
	        else {
	            $result = alert('Execute Statement Error: '.$update->error, 'danger','close');
	        }
		}
	}
	$update->close;
}

## Get SL Data
$settings['sldata'] = getShortlinks()['data'];

## Include custom shortlinks
include '../script/shortlinks.php';

?>


<!DOCTYPE html>
<html lang="en"> 
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= ($settings['name'])?: 'GR8 Faucet Script (Lite)';?> Admin Panel</title>
        <meta name="description" content="">
    	<meta name="keywords" content="">
    	<meta name="robots" content="noindex,nofollow">
    	<!-- FAUCET FAVICON -->
		<link rel="shortcut icon" href="data:image/x-icon;base64,AAABAAEAEBAAAAEAGABoAwAAFgAAACgAAAAQAAAAIAAAAAEAGAAAAAAAAAMAAAAAAAAAAAAAAAAAAAAAAAAAAAC8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTwAAAC8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy3ejO3ejO8jTy3ejO3ejO8jTy3ejO3ejO8jTy3ejO3ejO3ejO8jTy8jTy3ejO3ejP///////+8jTy3ejP///+8jTy3ejP///+8jTy3ejP///////+8jTy8jTy3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+8jTy3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+8jTy3ejP///+8jTy3ejP///+3ejP///+8jTz///+8jTy3ejP///+8jTy3ejP///+8jTy3ejP///+8jTz///////+3ejP///////+8jTy8jTy3ejP///+8jTy3ejP///+8jTy3ejP///+8jTy8jTy8jTy3ejP///////////+8jTy3ejO3ejP///////+8jTy8jTy3ejP///+8jTy8jTy8jTy3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+8jTy3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+8jTy3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+3ejP///+8jTy3ejP///+8jTy8jTy8jTz///////+8jTy3ejP///////////+8jTy8jTy8jTz///////+8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTwAAAC8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTy8jTwAAACAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACAAQAA">
		<!-- Bootstrap
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous"> -->
		<!-- Theme CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/flatly/bootstrap.min.css" integrity="sha384-qF/QmIAj5ZaYFAeQcrQ6bfVMAh4zZlrGwTPY7T/M+iTTLJqJBJjwwnsE5Y0mV7QK" crossorigin="anonymous">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
		<!-- Google Font -->
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Niramit:wght@400;500;600;700&family=Russo+One&display=swap" rel="stylesheet">
		<!-- DataTables -->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/fh-3.1.8/r-2.2.7/datatables.min.css"/>
    	<!-- Date Range Picker -->
    	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
        <!-- Base CSS -->
        <link rel="stylesheet" href="../libs/css/base.css">	
            
    	<style type="text/css">
			*{font-family: 'Niramit', sans-serif;}
			.navbar-brand,h1,h2,h3,h4,h5,h6 {font-family: 'Russo One', sans-serif;}
			.navbar-brand{font-size:20px}
			.navbar-dark .navbar-brand:focus,.navbar-dark .navbar-brand:hover{color:#18bc9c}
    	    .nav-tabs { border-bottom:none; }
    	    .nav-tabs .nav-link:focus, .nav-tabs .nav-link:hover {
                border-color: #e9ecef #e9ecef transparent #dee2e6;
            }
            .nav-tabs .nav-link {
                color: #495057;
                background-color: #dee2e6;
                border-color: #dee2e6 #dee2e6 #fff;
            }
            .nav-tabs .nav-link:hover {
                color: #fff;
                background-color: #adb5bd;
                border-color: #dee2e6 #dee2e6 #fff;
            }
            .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
                color: #495057;
                background-color: #fff;
                border-color: #dee2e6 #dee2e6 #fff;
            }
    		.custom-checkbox-lg .custom-control-label::before{ width: 62px;height: calc(1.5em + 0.5rem);border-radius: .25rem; }
            .custom-checkbox-lg .custom-control-label::after {width: 12px;height: calc(1.5em + 0.5rem - 3px);border-radius: .25rem; }
            .custom-checkbox-lg .custom-control-label { margin: -4px 0 0; }
            .custom-control-input:checked ~ .custom-control-label::before {color: #fff;border-color: var(--success);background-color: var(--success);}
            .custom-checkbox-lg .custom-control-input:checked~.custom-control-label::after { -webkit-transform: translateX(2.9rem);transform: translateX(2.9rem); }
            
            .noselect {
    		  -webkit-touch-callout: none; /* iOS Safari */
    			-webkit-user-select: none; /* Safari */
    			 -khtml-user-select: none; /* Konqueror HTML */
    			   -moz-user-select: none; /* Firefox */
    				-ms-user-select: none; /* Internet Explorer/Edge */
    					user-select: none; /* Non-prefixed version, currently
    										  supported by Chrome and Opera */
    		}
			select.form-control{padding: 0.175rem 0.75rem 0.375rem;}
    		select option:disabled {
                display:none;
            }
        </style>
    </head>
    
    <!-- START BODY -->
    <body class="d-flex flex-column">
        
    	<!-- Navbar - -->
    	<nav class="navbar navbar-expand-md navbar-dark bg-primary p-2">
			<div class="container">
                <a class="navbar-brand" href="<?= $settings['domain'];?>/admin/"><?= ($settings['name'])?: 'GR8 Faucet Script (Lite)';?> Admin</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav ml-auto">
                        <a class="nav-item nav-link" href="settings.php">Settings</a>
                        <a class="nav-item nav-link" href="<?= rtrim($settings['domain'],'/');?>/admin/index.php">Reports</a>
                        <a class="nav-item nav-link" href="logs.php">Logs</a>
                        <a class="nav-item nav-link" href="<?= $settings['domain'];?>" target="_blank">View Faucet</a>
                        <?php if($_SESSION[$faucetID.'-admin'] == 'logged'){ ?><a class="nav-item nav-link" href="?a=logout">Logout</a><?php } ?>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Update available Message -->
        <?php if($settings['update']){ ?>
            <div class="alert alert-success w-100 text-center m-0">
                <b>Update: </b><?= $settings['update']['name'];?> <span class="align-text-bottom"><small>v<?= $settings['update']['version'];?></small></span> is available to <a class="alert-link" href="<?= $settings['update']['download'];?>">download</a>!
            </div>
        <?php } ?>
        
        <!-- If Logged in -->
        <?php if($_SESSION[$faucetID.'-admin'] == 'logged'){ ?>
            <div class="container-fluid bg-light">
        		<div class="container">
                    <h3 class="p-5 text-center text-secondary"><i class="fas fa-cogs"></i> Faucet Settings</h3>
    				<?php $tabs = array('basic','security','template','shortlinks'); ?>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <?php foreach($tabs as $tab){
                        	echo '  <li class="nav-item">
                    				    <a class="nav-link mr-1'.((($_POST['tab'] == $tab)||((empty($_POST['tab']))&&($tab == 'basic')))? ' show active' : '').'" id="'.$tab.'-tab" data-toggle="tab" href="#'.$tab.'" role="tab" aria-controls="'.$tab.'" aria-selected="'.((($_POST['tab'] == $tab)||((empty($_POST['tab']))&&($tab == 'rewards')))? 'true' : 'false').'">'.ucwords($tab).'</a>
                        			</li>';
                           } ?>
                    </ul>
                </div>
            </div>
                    
            <!-- START ADMIN CONTAINER -->
            <div class="container flex-grow my-4">
        	    <div class="row p-0 m-0">
    		    
            		<!-- Main Container -->
            		<div class="col-12 text-left">
                            <div class="tab-content text-left" id="myTabContent">
        						<?= $result; ?>
        						<div class="tab-pane fade<?= ((($_POST['tab'] == 'basic')||(empty($_POST['tab'])))? ' show active' : '');?>" id="basic"  role="tabpanel" aria-labelledby="basic-tab">
        				            <form action="" class="form p-2" method="post">
        				                <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label" for="name">Name</label>
                                                    <input class="form-control" type="text" value="<?= $settings['name'];?>" name="name" id="name">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="domain">Faucet URL</label>
                                                    <input class="form-control" type="text" value="<?= $settings['domain'];?>" id="domain" name="domain">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="description">Description <small>(Optional)</small></label>
                                                    <textarea rows="3" class="form-control" id="description" name="description"><?= $settings['description'];?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="microwallet">MicroWallet</label>
                                                    <select class="form-control" id="microwallet" name="microwallet">
                                                        <option value="" disabled selected></option>
                                                        <?php foreach($microwallets as $name => $data){
                                                            echo '<option value="'.$name.'" '.(($settings['microwallet'] == $name)? 'selected': '').'>'.$data['name'].'</option>';
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="api_key">API Key</label>
                                                    <input class="form-control" type="text" value="<?= $settings['api_key'];?>" id="api_key" name="api_key">
                                                </div>
                                                <div class="form-group user_token <?= ($settings['microwallet'] != 'expresscrypto')? 'd-none':'';?>">
                                                    <label class="control-label" for="api_key">User Token</label>
                                                    <input class="form-control" type="text" value="<?= $settings['user_token'];?>" id="user_token" name="user_token">
                                                </div>
                                                <div class="form-group site_currency">
                                                    <label class="control-label" for="currency">Currency</label>
                                                    <select class="form-control" id="currency" name="currency">
                                                        <option value="" disabled selected></option>
                                                        <?php foreach($microwallets as $name => $data){
                                                            foreach($data['currencies'] as $currency){
                                                                echo '<option class="'.$name.'" value="'.$currency.'"'.(($settings['microwallet'] != $name) ? ' style="display: none;"' : '').((($settings['microwallet'] == $name) && ($settings['currency'] == $currency))? ' selected': '').'>'.$currencies[$currency].'</option>';
                                                            }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label" for="timer">Timer <small>(Number of minutes between claims)</small></label>
                                                    <input class="form-control" type="number" min="1" max="1440" step="1" value="<?= $settings['timer'];?>" id="timer" name="timer">
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="referal">Referral <small>(Leave as 0 to have no referal reward)</small></label>
                                                    <div class="input-group mb-2">
                                                        <input class="form-control" type="number" min="0" max="900" step="1" value="<?= $settings['referral'];?>" id="referral" name="referral">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text">
                                                                <i class="fa fa-percent"></i>
                                                            </div>    
                                                        </div>   
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="reward">Rewards</label>
                                                    <input class="form-control" type="text" value="<?= $settings['reward'];?>" id="reward" name="reward">
                                                </div>
                                                <p class="text-left">
            										<i><u>Probability Reward:</u></i><br>
            										    Example: <strong style="color:red">100*50, 200*20, 300*30</strong> means there is a 50% chance the user will receive 100 satoshi, 20% chance the user will receive 200 satoshi and 30% chance the user will receive 300 satoshi.<br>
            										<i><u>Random Reward:</u></i><br>
            										    Example: <strong style="color:red">100-200</strong> means there is a random chance of user receiving between 100 and 200 satoshi.<br>
            										<i><u>Single Reward:</u></i><br>
            										    Example: <strong style="color:red">100</strong> means the user will always recieve 100 satoshi.<br>
            										<i><u>Single USD Based Reward:</u></i><br>
            											Example: <strong style="color:red">0.002</strong> means the user will always recieve 2/10th of a US Cent in satoshi based on the currencies <a href="https://www.coingecko.com/" target="_blank">current price</a>.
            									</p>
                                            </div>
                                            
            								<div class="col-12 my-4 pt-3 text-left flex-grow" style="border-top: 1px solid #dee2e6;">
                                                <input type="submit" name="update" class="btn btn-info" value="Update Basic Settings">
                                            </div>
                                        </div>
                                    </form>
                                 </div> <!-- ./ tab-pane -->
                        
                                <!-- SECURITY -->
                                <div class="tab-pane fade<?= ((($_POST['tab'] == 'security'))? ' show active' : '');?>" id="security" role="tabpanel" aria-labelledby="security-tab">
                                    <form action="" class="form p-2" method="post">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
            									<div class="form-group">
            											<label style="float: left;">Captcha:</label>
            											<select class="form-control" name="primary_captcha" id="primary_captcha">
            											<?php foreach($captchas as $key => $value){ 
            												echo '<option data-keys=\''.$settings[$key.'_keys'].'\' value=\''.$key.'\''.(($settings['primary_captcha'] == $key)? ' selected': '').'>'.$value.'</option>';} ?>
            											</select>
            											<div id="primary_keys" class="card ml-3 mt-3 p-3 bg-light">
            											<?php $primary_keys = json_decode($settings[$settings['primary_captcha'].'_keys'], true);
            												foreach($primary_keys as $key => $value){
            												    echo '<div class="form-group">';
            													echo '<label class="keys">'.ucwords(str_replace('_',' ',$key)).'</label>';
            													echo '<input type="text" class="form-control" name="'.$settings['primary_captcha'].'['.$key.']" value="'.$value.'">';
            													echo '</div>';
            												}
            												?>					
            											</div>
            									</div>
            									<div class="form-group">
            											<label style="float: left;">Secondary Captcha: <small>(Optional)</small></label>
            											<select class="form-control" name="secondary_captcha" id="secondary_captcha">
            											<option value="">None</option>
            											<?php foreach($captchas as $key => $value){
            												echo '<option data-keys=\''.$settings[$key.'_keys'].'\' value=\''.$key.'\''.(($settings['secondary_captcha'] == $key)? ' selected': '').(($settings['primary_captcha'] == $key)? '': '').'>'.$value.'</option>';} ?>
            											</select>
            											<div id="secondary_keys" class="card ml-3 mt-3 p-3 bg-light">
            											<?php 
            												if($settings['secondary_captcha']){
                												$secondary_keys = json_decode($settings[$settings['secondary_captcha'].'_keys'], true);
                												foreach($secondary_keys as $key => $value){
                												    echo '<div class="form-group">';
                													echo '<label class="keys">'.ucwords(str_replace('_',' ',$key)).'</label>';
                													echo '<input type="text" class="form-control" name="'.$settings['secondary_captcha'].'['.$key.']" value="'.$value.'">';
                													echo '</div>';
            												    }
            												}
            												?>					
            											</div>
            									</div>
                                            </div>
                                            <div class="col-12 col-md-6">
            									<div class="form-group">
            											<label style="float: left;">Max Claims per 24 hours: <small>(If leave blank, max will be total shortlinks views)</small></label>
            											<input type="text" class="form-control" name="max_claims" id="max_claims" value="<?=  $settings['max_claims'];?>">
            									</div>
                                                <div class="form-group">
            										<label for="name">Shortlink Timer <small>(How long should it take a user to complete shortlinks? Default: 8 sec)</small></label>
            										<input type="text" class="form-control" name="shortlink_timer" value="<?= $settings['shortlink_timer'];?>">
            									</div>
            									<div class="form-group">
            										<label for="name">IP Hub API Key <small>(<a href="https://iphub.info" target="_blank">Get Key</a>)</small></label>
            										<input type="text" class="form-control" name="iphub_api" value="<?= $settings['iphub_api'];?>">
            									</div>
            									<div class="form-group">
            										<label for="name">ProxyCheck API Key <small>(<a href="https://proxycheck.io" target="_blank">Get Key</a>)</small></label>
            										<input type="text" class="form-control" name="proxycheck_api" value="<?= $settings['proxycheck_api'];?>">
            									</div>
            									<div class="form-group">
            									    <div class="custom-control custom-switch">
            											<input type="checkbox" class="custom-control-input sub" id="disable_balance" name="disable_balance" <?= (($settings['disable_balance'])? 'value="Y" checked': '"');?>>
            											<label class="custom-control-label" for="disable_balance">Disable Faucet Balance</label>
            										</div>
            										<input type="hidden" id="disable_balance" name="disable_balance" <?= (($settings['disable_balance'])? 'value="Y" checked': '');?>>
            									</div>
            									<div class="form-group">
            									    <div class="custom-control custom-switch">
            											<input type="checkbox" class="custom-control-input sub" id="disable_antibot" name="disable_antibot" <?= (($settings['disable_antibot'])? 'value="Y" checked': '');?>>
            											<label class="custom-control-label" for="disable_antibot">Disable Antibot Links</label>
            										</div>
            										<input type="hidden" id="disable_antibot" name="disable_antibot" <?= (($settings['disable_antibot'])? 'value="Y" checked': '');?>>
            										</div>
            									<div class="form-group">
            									    <div class="custom-control custom-switch">
            											<input type="checkbox" class="custom-control-input sub" id="disable_iframes" name="disable_iframes" <?= (($settings['disable_iframes'])? 'value="Y" checked': '');?>>
            											<label class="custom-control-label" for="disable_iframes">Disable Open in IFRAME</label>
            										</div>
            										<input type="hidden" id="disable_iframes" name="disable_iframes" <?= (($settings['disable_iframes'])? 'value="Y"': '');?>>
            									</div>
                                            </div>
                                            
            								<div class="col-12 my-4 pt-3 text-left" style="border-top: 1px solid #dee2e6;">
                                                <input type="submit" name="update" class="btn btn-info" value="Update Security Settings">
                                            </div>
                                        </div>
                                    </form>
                                 </div> <!-- ./ tab-pane -->
                                            
                                <!-- Advertisements -->
                                <div class="tab-pane fade<?= ((($_POST['tab'] == 'template'))? ' show active' : '');?>" id="template" role="tabpanel" aria-labelledby="template-tab">
                                    <form action="" class="form p-2" method="post">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label" for="theme">Theme</label>
                                                    <select class="form-control" id="theme" name="theme">
                                                        <?php $themes = array('default','cerulean','cosmo','cyborg','darkly','flatly','journal','litera','lumen','lux',
                                                                'materia','minty','pulse','sandstone','simplex','sketchy','slate','solar','spacelab','superhero','united','yeti');
                                                                
                                                        foreach($themes as $theme){
                                                            echo '<option value="'.$theme.'" '.(($settings['theme'] == $theme)? 'selected' : '').'>'.ucwords($theme).'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="" id="preview">
                                                        <?php if($settings['theme'] != 'default'){ ?><div class="form-group"><label class="control-label" for="theme">Theme Preview</label><br><img src="//bootswatch.com/<?= $settings['theme'];?>/thumbnail.png" style="width:100%;max-width:400px"></div><?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label class="control-label" for="antibot_theme">Antibot Theme</label>
                                                    <select class="form-control" id="antibot_theme" name="antibot_theme">
                                                        <option value="light" <?= (($settings['antibot_theme'] == 'light')? 'selected' : '');?>>Light</option>
                                                        <option value="dark" <?= (($settings['antibot_theme'] == 'dark')? 'selected' : '');?>>Dark</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="navlinks">Nav Links <small>(Use <a href="https://www.w3schools.com/tags/tag_a.asp" target="_blank">HTML &lt;a&gt; Tag</a>, one per line)</small></label>
                                                    <textarea rows="4" class="form-control" id="navlinks" name="navlinks"><?= $settings['navlinks'];?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label" for="css">Custom CSS <small>(Do not wrap css with &lt;style&gt;&lt;/style&gt;)</small></label>
                                                    <textarea rows="4" class="form-control" id="css" name="css"><?= $settings['css'];?></textarea>
                                                </div>
                                               <div class="form-group">
                    								<label for="top_ads">Top Ad Space <small>(Recommended Size: 728px by 90px Banners)</small></label>
                    								<textarea class="form-control" rows="6" id="top_ads" name="top_ads"><?= $settings['top_ads'];?></textarea>
                    							</div>
                    							<div class="form-group">
                    								<label for="left_ads">Left Ad Space <small>(Recommended Size: 160px by 600px Banners)</small></label>
                    								<textarea class="form-control" rows="6" id="left_ads" name="left_ads"><?= $settings['left_ads'];?></textarea>
                    							</div>
                    							<div class="form-group">
                    								<label for="middle_ads">Middle Ad Space <small>(Recommended Size: 300px by 250px Banners)</small></label>
                    								<textarea class="form-control" rows="6" id="middle_ads" name="middle_ads"><?= $settings['middle_ads'];?></textarea>
                    							</div>
                    							<div class="form-group">
                    								<label for="right_ads">Right Ad Space <small>(Recommended Size: 160px by 600px Banners)</small></label>
                    								<textarea class="form-control" rows="6" id="right_ads" name="right_ads"><?= $settings['right_ads'];?></textarea>
                    							</div>
                    							<div class="form-group">
                    								<label for="bottom_ads">Bottom Ad Space <small>(Recommended Size: 728px by 90px Banners)</small></label>
                    								<textarea class="form-control" rows="6" id="bottom_ads" name="bottom_ads"><?= $settings['bottom_ads'];?></textarea>
                    							</div>
                    							<div class="form-group">
                    								<label for="paid_box">Paid/Wait Ad Space <small>(Recommended to add other faucets links or ref links)</small></label>
                    								<textarea class="form-control" rows="6" id="paid_box" name="paid_box"><?= $settings['paid_box'];?></textarea>
                    							</div>
                                                <div class="col-12 my-4 pt-3 text-left" style="border-top: 1px solid #dee2e6;">
                                                    <input type="submit" name="update" class="btn btn-info" value="Update Template">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                 </div> <!-- ./ tab-pane -->
                                
                                <!-- Shortlinks -->
                                <div class="tab-pane fade<?= ((($_POST['tab'] == 'shortlinks'))? ' show active' : '');?>" id="shortlinks" role="tabpanel" aria-labelledby="shortlinks-tab">
                                    <form action="" class="form" method="post" id="shortlinksForm">
                                    <input type="hidden" name="tab" value="shortlinks">					
        							<?php 
        							
        							
        							if($settings['sldata']){ ?>
        							    <?php $settings['shortlinks'] = json_decode($settings['shortlinks'],true); ?>
        								<?= alert('THIS LIST IS FOR REFERRENCE ONLY! You should do your own research as some shorteners may no longer pay or accept faucets.', 'danger');?>
        								<?= alert('<b>API Key</b> and <b>Views</b> are required fields if shortlink is enabled! Leave <b>Priority</b> blank to show random shortlink, otherwise put them in order 1,2,3, etc.', 'info');?>
        									<table id="shortlinksTable" class="table table-sm table-striped table-hover compact responsive" style="width:100%">
        										<thead>
        											<tr>
        												<th>Name</th>
        												<th data-priority="10001">CPM</th>
        												<th data-sortable="false">API Key</th>
        												<th>Priority</th>
        												<th>Views</th>
        												<th>Enabled</th>
        												<th class="d-none">Active</th>
        											</tr>
        										</thead>
        										<tbody>
        										
        										<?php 
            										foreach($settings['sldata'] as $key => $value){ 
            										    
            										
            											    
            												echo '<tr id="'.$key.'" '.(($value['status'] == 'N') ? 'class="text-danger"' : '').'>';
            												echo '<td><a href="'.(($settings['shortlinks'][$key]['enabled'])? str_replace('ref/AvalonRychmon','member/dashboard',$value['referral']) : $value['referral']).'" target="_blank" class="noselect'.(($value['status'] == 'Y') ? '': ' inactive').'" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="<b><i>Withdraw Threshold</i></b><br> '.str_replace('"',"'",$value['withdraw']).'">'.$value['name'].'</a></td>';
            												echo '<td data-toggle="tooltip" data-placement="bottom" data-original-title="Minimium CPM">$'.$value['cpm'].'</td>';
            												echo '<td>
            														<input type="hidden" name="shortlinks['.$key.'][id]" value="'.$value['id'].'">
            														<input type="hidden" name="shortlinks['.$key.'][apilink]" value="'.$value['apilink'].'">
            														<input type="text" class="form-control form-control-sm"  id="apikey" name="shortlinks['.$key.'][apikey]" style="width:100%" value="'.$settings['shortlinks'][$key]['apikey'].'">
            													</td>';
            												echo '<td data-order="'.(($settings['shortlinks'][$key]['priority'])?: '9999999').'"><input type="text" class="form-control form-control-sm text-center" name="shortlinks['.$key.'][priority]" size="2"  style="width:100%" value="'.$settings['shortlinks'][$key]['priority'].'" oninput="this.value = this.value.replace(/[^0-9.]/g, \'\'); this.value = this.value.replace(/(\..*)\./g, \'$1\');"></td>';
            												echo '<td data-order="'.(($settings['shortlinks'][$key]['views'])?:$value['views']).'">
            														<input type="text" class="form-control form-control-sm text-center" id="views-'.$key.'" name="shortlinks['.$key.'][views]" data-toggle="tooltip" data-placement="bottom" data-original-title="Counts '.$value['views'].' view per 24hr" size="2" style="width:100%" value="'.$settings['shortlinks'][$key]['views'].'" onkeyup="$(this).toggleClass(\'alert-danger\',$(this).val()==\'\');"  oninput="this.value = this.value.replace(/[^0-9.]/g, \'\'); this.value = this.value.replace(/(\..*)\./g, \'$1\');">
            													</td>';
            												echo '<td data-order="'.$settings['shortlinks'][$key]['enabled'].'">
            															<div class="custom-control custom-switch custom-checkbox-lg">
            																<input type="checkbox" class="custom-control-input" id="enable-'.$key.'" name="shortlinks['.$key.'][enabled]" '.(($settings['shortlinks'][$key]['enabled'])? 'value="Y" checked': 'value="N"').'>
            																<label class="custom-control-label" for="enable-'.$key.'"> </label>
            															</div>
            													</td>';
            												echo '<td class="d-none" data-order="'.$value['status'].'">'.$value['status'].'</td>';
            												echo '</tr>';
            										}
        										?>
        										</tbody>
        									</table>
        								<div class="p-0 my-2 text-left">
                                            <input type="submit" name="update" class="btn btn-info" value="Update Shortlinks">
                                        </div>
                                    </form>
        							<?php }
        								else { echo '<h4>Error connecting to shortlinks database, please try back later.</h4>'; }
        							?>
                                </div> <!-- ./ Shortlinks -->
                                
                            </div> <!-- ./ tab-content -->
                    </div>
                </div>      
            </div> <!-- ./ ADMIN CONTAINER -->
        
        <?php } else { ?>
            <!-- START ADMIN LOGIN CONTAINER -->
            <div class="container flex-grow my-4">
        	    <div class="row p-0 m-0">
    		        <!-- Main Container -->
            		<div class="mx-auto mt-5 text-center">
            			<?= $error;?>
            			<div class="card shadow">
            				<div class="card-header text-center">
            					<h4 class="mb-0">Admin Login</h4>
            				</div>
            				<div class="card-body text-center">
            					<form action="" method="post">
            						<div class="input-group">
            							<div class="input-group-prepend">
            							    <span class="input-group-text"><i class="fa fa-lock"></i></span>
            							</div>
            							<input type="password" class="form-control" name="password" placeholder="Password" id="password" <?= (($error)? 'value="'.$_POST['password'].'"' : '');?> pattern=".{8,}" required>
            						</div>
            						<div class="form-group my-2">
            							<input type="submit" class="btn btn-success btn-block">
            						</div>
            							
            					</form>
            				</div>
            			</div>
            		</div>
            	</div>
            </div> <!-- ./ ADMIN LOGIN CONTAINER -->
        <?php } ?>
        
        <!-- Footer -->
        <footer class="py-3">
            <div class=" text-center">
                <div class="col-12">
                    Copyright© 2016-<?= date('Y');?> <b>GR8 Faucet Script Lite</b><br>
                    Purchase the <a href="https://gr8.cc" target="_blank">GR8 Faucet Script</a> for more features and improved security!<br>
                    <small>Server Time: <?= dateNow();?></small>
                </div>
            </div>
        </footer>
        
       
        
        <!-- JQUERY -->
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<!-- BOOTSTRAP -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
		<!-- DataTables -->
		<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.23/fh-3.1.8/r-2.2.7/datatables.min.js"></script>
	    
        <script>
            
            // Update MicroWallet & Theme Change
            $(function () {
                $('#microwallet').change(walletSelect);
                $('#theme').change(themeSelect);
            });
            
            // Initial Tooltip
            $(function () { $('[data-toggle="tooltip"]').tooltip({boundary: 'window'}) });
            
            // walletSelect Function
            function walletSelect(){
                var walletClass = $('#microwallet').find('option:selected').attr('value');
                if(walletClass != 'expresscrypto'){
                    $('.user_token').addClass('d-none');
                }else{
                    $('.user_token').removeClass('d-none');
                }
                $('#currency option[value=\'\']').prop('selected', true);
                $('#currency option').each(function () {
                    var self = $(this);
                    if (self.hasClass(walletClass) || typeof(walletClass) == "undefined") {
                        self.show();
                    } else {
                        self.hide();
                   }
                });
            }
            
            // Theme Select Function
            function themeSelect(){
                var theme = $('#theme').find('option:selected').attr('value');
                if(theme != 'default'){
                    $('#preview').html('<div class="form-group"><label class="control-label" for="theme">Theme Preview</label><br><img src="//bootswatch.com/'+theme+'/thumbnail.png" style="width:100%;max-width:400px"></div>');
                }else {
                    $('#preview').html('');
                }
            };
            
            // Show Captcha keys   
        	$(function() {
        		function getKeys(id,div) {
            		opt = $('#'+id).val();
            		data = $('option:selected', $('#'+id)).attr("data-keys");
            		
            		//$('#primary_captcha, #secondary_captcha').not(id).children('option[value=' + id + ']').attr('disabled', true).siblings().removeAttr('disabled');
            		
            		if(data){	
                		keys = jQuery.parseJSON( data);
                		$('#'+div).html('');
                		$('#'+div).css('display', 'block');
                		$.each( keys, function( key, value ) {
                			$('#'+div).append('<div class="form-group"><label class="keys">'+key.replace('_',' ')+'</label> <input type="text" class="form-control" name="'+opt+'['+key+']" value="'+value+'"></div>');
                			$('.keys').css('text-transform', 'capitalize');		
                		});    	   
            	    }
            		else {
            	        $('#'+div).css('display', 'none');	
            	    }
            	    
        		};
        		
        		getKeys('secondary_captcha','secondary_keys');
            	$("#primary_captcha").on("change", function() {getKeys('primary_captcha','primary_keys')});
            	$("#secondary_captcha").on("change", function() {getKeys('secondary_captcha','secondary_keys')});
            });
        	
        	// Load Shortlinks DataTable 
            $(function() {
                $('#shortlinksTable').DataTable({
            	    "order": [[ 5, "desc" ],[ 6, "desc" ],[ 1, "desc" ]],
            		"paging":   false,
            		"bInfo" : false,
            		"searching" : false,
    				"columnDefs": [{
    				    "targets": [ 6 ],
    					"visible": false
    				}]
                });     
        	});
        	
        	// Misc Shortlink Form Functions
        	function add(){
        		id = $(this).closest('tr').attr('id');
		
		        $('#views-'+id).toggleClass("alert-danger",$('#views-'+id).val()=='');
        		
        		$('#enable-'+id).prop('checked', true);
        		$('#enable-'+id).val('Y');
        	};
        	
        	$('tr input[id=apikey]').change(add).keydown(add).keyup(add).keypress(add).bind('paste',add).on('drop',add);
        	
        	// Remove empty fields
			$("#shortlinksForm").submit(function() {
				$(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
				return true;
			});
			
			// Update Checkbox value
			$("input[type='checkbox']").on('change', function(){
			    $('input[name='+$(this).attr('id')+']').val(this.checked ? "Y" : "");
            });
            
            // Remember Current Tab
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
            });
            
            var activeTab = localStorage.getItem('activeTab');
            if(activeTab){
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }
  
        </script>
    	
    </body>
</html>

<?php 
#print_pre($_SESSION);
$db->close();
	
	
	