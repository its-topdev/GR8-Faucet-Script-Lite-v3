<?php

## GR8 FAUCET SCRIPT LITE - ADMIN INDEX/REPORTS ##

## INCLUDE INI
include '../script/ini.php';

## Report
$report = ($_REQUEST['report'])? $_REQUEST['report'] : '';

## Date
$date = ($_REQUEST['date'])? $_REQUEST['date'] : dateNow('today');
$date = explode(' - ', $date);
$date = ($date['1'] && ($date['0'] != $date['1'])) ? $date : $date['0'];
$timestamp = (is_array($date)) ? '`timestamp` BETWEEN \''.$date['0'].' 00:00:00\' AND \''.$date['1'].' 23:59:59\'' : '`timestamp` LIKE \''.$date.'%\'';

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
        		<div class="container p-5">
                    <h3 class="text-center text-secondary"><i class="fas fa-chart-line"></i> Faucet Reports</h3>
    				<div class="container">
    				    <div class="row">
    						<form method="POST" class="form-inline col justify-content-center" accept-charset="utf-8" action="">
    							<select name="report" class="form-control mb-2 mr-sm-2">
    								<option value="" disabled selected>Select Report</option>
    								<option value="shortlink" <?= ($report == 'shortlink') ? 'selected' : '';?>>Shortlink Report</option>
    								<option value="payout" <?= ($report == 'payout') ? 'selected' : '';?>>Payout Report</option>
    								<option value="topuser" <?= ($report == 'topuser') ? 'selected' : '';?>>Top User Report</option>
    							</select>
    							<div class="input-group mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <input id="date" name="date" type="text" class="form-control" value="<?= dateNow('today'). ' - '.dateNow('today');?>">
                                </div>
    							<input type="submit" class="form-control btn-primary col-12 col-sm-2 mb-2 mx-0" value="Fetch">
    						</form> 
						</div>
					</div>
                </div>
            </div>
                    
            <!-- START ADMIN CONTAINER -->
            <div class="container flex-grow my-4">
        	    <div class="row p-0 m-0">
    		    
            		<!-- Main Container -->
            		<div class="col-12 text-left">
        			<?php 
        				switch($report){
        					case 'shortlink': 
        						
        					?>	
        					<div class="card shadow"> 
        						<div class="card-header">
        							<h4 class="m-0"><i class="fa fa-link" aria-hidden="true"></i> Shortlink Report for <?= (is_array($date)) ? $date['0'].'  - '.$date['1'] : $date; ?></h4>
        						</div>
        						<div class="card-body">
        						
        						<?php 
        						
        						## REPORT
        						$results = $db->query("SELECT COUNT(DISTINCT(`ip`)) AS IP, COUNT(DISTINCT(`address`)) AS ADDRESS, COUNT( * ) AS URL_VISITED, `slid`,  SUM(`reward`) AS TOTAL_PAID, SUM(`usd`) AS TOTAL_USD  FROM `payouts-".$faucetID."` WHERE `type` = 'claim' AND $timestamp GROUP BY `slid` ORDER BY `URL_VISITED` DESC");
        						if($results->num_rows){
        							
        							?>
        							<table id="reports" class="table table-sm table-striped table-hover compact responsive" width="100%">
        								<thead>
        									<tr>
        										<th class="text-center" scope="col"># IPs</th>
        										<th class="text-center" scope="col"># Address</th>
        										<th class="text-center" scope="col"># Visits</th>
        										<th scope="col">Shortlink</th>
        										<th class="text-center" scope="col">cpm</th>
        										<th scope="col">Total Paid</th>
        									</tr>
        								</thead>
        								<tbody>
        							
        							<?php 
        															
        								while($row = $results->fetch_assoc()){
        									$total['ips'] += $row['IP'];
        									$total['address'] += $row['ADDRESS'];
        									$total['visits'] += $row['URL_VISITED'];
        									$total['paid'] += $row['TOTAL_PAID'];
        									$total['usd'] += $row['TOTAL_USD'];
        									
        									echo '
        										<tr>
        											<td class="text-center" scope="row">'.$row['IP'].'</td>
        											<td class="text-center" scope="row">'.$row['ADDRESS'].'</td>
        											<td class="text-center" scope="row">'.$row['URL_VISITED'].'</td>
        											<td scope="row">'.(($row['slid'])? '<a href="'.str_replace('ref/AvalonRychmon','member/dashboard',$settings['sldata'][$row['slid']]['referral']).'" target="_blank">'.$settings['sldata'][$row['slid']]['name'].'</a>' : 'No Shortlink').'</td>
        											<td class="text-center" scope="row">'.$settings['sldata'][$row['slid']]['cpm'].'</td>
        											<td scope="row" data-filter="'.$row['TOTAL_PAID'].'">'.((strlen($row['TOTAL_PAID']) >= '7')? number_format(($row['TOTAL_PAID']/100000000),2).' '.$settings['currency'] : $row['TOTAL_PAID'].' satoshi').' ('.number_format($row['TOTAL_USD'], 5).' USD)</td>
        										</tr>';
        								}
        							?>
        									<tfoot>
        										<tr>
        											<td class="text-center"><b><?= $total['ips'];?></b></td>
        											<td class="text-center"><b><?= $total['address'];?></b></td>
        											<td class="text-center"><b><?= $total['visits'];?></b></td><td></td>
        											<td></td>
        											<td><b><?= ((strlen($total['paid']) >= '7')? number_format(($total['paid']/100000000),4).' '.$settings['currency'] : $total['paid'].' satoshi').'  ('.$total['usd'];?> USD)</b></td>
        										</tr>
        									</tfoot>
        								</tbody>
        							</table>
        								<?php 	
        							}
        							else { echo '<h5>Not enough data to display this report!</h5>';}
        							?>
        						</div>
        					</div>
        				<?php
        					break;
        					
        					case 'payout':
        				
        								
        				?>
        					<div class="card shadow"> 
        						<div class="card-header">
        							<h4 class="m-0"><i class="fa fa-money" aria-hidden="true"></i> Payouts Report for <?= (is_array($date)) ? $date['0'].'  - '.$date['1'] : $date; ?></h4>
        						</div>
        						<div class="card-body">
        						
        					
        						<?php 
        							
        							$results = $db->query("SELECT * FROM `payouts-".$faucetID."` WHERE $timestamp ORDER BY `id` DESC");
        							
        							if($results->num_rows){ ?>
        							
        								<table id="payouts" class="table table-sm table-striped table-hover compact responsive" width="100%">
        									<thead>
        										<tr>
        											<th>Date</th>
        											<th data-sortable="false">Address</th>
        											<th data-sortable="false">IP Address</th>
        											<th>Shortlink</th>
        											<th>Reward</th>
        										</tr>
        									</thead>
        									<tbody>
        							
        								<?php 
        								
        								while($row = $results->fetch_assoc()){
        									echo '
        										<tr>
        											<td>'.$row['timestamp'].'</td>
        											<td style="word-break:break-all"><a href="#'.$row['address'].'">'.$row['address'].'</a></td>
        											<td>'.(($row['ip'])? '<a href="http://iphub.info/?ip='.$row['ip'].'">'.$row['ip'].'</a>' : 'Referral Payout').'</td>
        											<td>'.(($row['slid'])? '<a href="'.str_replace('ref/AvalonRychmon','member/dashboard',$settings['sldata'][$row['slid']]['referral']).'" target="_blank">'.$settings['sldata'][$row['slid']]['name'].'</a>' : 'No Shortlink').'</td>
        											<td data-filter="'.$row['reward'].'">'.((strlen($row['reward']) >= '7')? number_format(($row['reward']/100000000),2).' '.$settings['currency'] : $row['reward'].' satoshi').'</td>
        										</tr>';
        								}
        								?>
        									</tbody>
        								</table>
        								<?php 	
        							}
        							else { echo '<h5>Not enough data to display this report!</h5>';}
        							?>
        						</div>
        					</div>
        				<?php
        					break;
        
                                  case 'topuser':
        				
        								
        				?>
        					<div class="card mb-4"> 
        						<div class="card-header">
        							<h4 class="m-0"><i class="fa fa-user" aria-hidden="true"></i> Top User Report for <?= (is_array($date)) ? $date['0'].'  - '.$date['1'] : $date; ?></h4>
        						</div>
        						<div class="card-body">
        						
        					
        						<?php 
        							
        							$results = $db->query("SELECT `address`, COUNT( * ) AS CLAIMS, SUM(`reward`) AS TOTAL_PAID, SUM(`usd`) AS TOTAL_USD FROM `payouts-".$faucetID."` WHERE `type` = 'claim' AND $timestamp GROUP BY `address` ORDER BY CLAIMS DESC LIMIT 10");
        							
        							if($results->num_rows){ ?>
        							
        								<table id="topuser" class="table table-sm table-striped table-hover compact responsive" width="100%">
        									<thead>
        										<tr>
        											<th>Address</th>
        											<th class="text-center">Claims</th>
        											<th>Total Paid</th>
        										</tr>
        									</thead>
        									<tbody>
        							
        								<?php 
        								
        								while($row = $results->fetch_assoc()){
        									echo '
        										<tr>
        											<td>'.$row['address'].'</td>
        											<td class="text-center">'.$row['CLAIMS'].'</td>
        											<td data-filter="'.$row['TOTAL_PAID'].'">'.((strlen($row['TOTAL_PAID']) >= '7')? number_format(($row['TOTAL_PAID']/100000000),2).' '.$settings['currency'] : $row['TOTAL_PAID'].' satoshi').' ('.number_format($row['TOTAL_USD'],5).' USD)</td>
        										</tr>';
        								}
        								?>
        									</tbody>
        								</table>
        								<?php 	
        							}
        							else { echo '<h5>Not enough data to display this report!</h5>';}
        							?>
        						</div>
        					</div>
        					
        					<div class="card shadow"> 
        						<div class="card-header">
        							<h4 class="m-0"><i class="fa fa-user-plus" aria-hidden="true"></i> Top Referrer Report for <?= (is_array($date)) ? $date['0'].'  - '.$date['1'] : $date; ?></h4>
        						</div>
        						<div class="card-body">
        						
        					
        						<?php 
        							
        							$results = $db->query("SELECT address, COUNT( * ) AS PAYOUTS, SUM(reward) AS TOTAL_PAID, SUM(usd) AS TOTAL_USD FROM `payouts-".$faucetID."` WHERE type = 'referral' AND $timestamp GROUP BY address ORDER BY PAYOUTS DESC LIMIT 10");
        							
        							if($results->num_rows){ ?>
        							
        								<table id="topuser" class="table table-sm table-striped table-hover compact responsive">
        									<thead>
        										<tr>
        											<th>Address</th>
        											<th class="text-center">Payouts</th>
        											<th>Total Paid</th>
        										</tr>
        									</thead>
        									<tbody>
        							
        								<?php 
        								
        								while($row = $results->fetch_assoc()){
        									echo '
        										<tr>
        											<td>'.$row['address'].'</td>
        											<td class="text-center">'.$row['PAYOUTS'].'</td>
        											<td data-filter="'.$row['TOTAL_PAID'].'">'.((strlen($row['TOTAL_PAID']) >= '7')? number_format(($row['TOTAL_PAID']/100000000),2).' '.$settings['currency'] : $row['TOTAL_PAID'].' satoshi').' ('.number_format($row['TOTAL_USD'],5).' USD)</td>
        										</tr>';
        								}
        								?>
        									</tbody>
        								</table>
        								<?php 	
        							}
        							else { echo '<h5>Not enough data to display this report!</h5>';}
        							?>
        						</div>
        					</div>
        					
        				<?php
        					break;
        					default: 
        					?>
        						<div class="card shadow"> 
        							<div class="card-header">
        								<h4 class="m-0"><i class="fa fa-money" aria-hidden="true"> </i> Recent Payouts</h4>
        							</div>
        							<div class="card-body">
        												
        								<?php 
        								$results = $db->query("SELECT `address`,`ip`,`slid`,`reward`,`timestamp` FROM `payouts-".$faucetID."` ORDER BY `timestamp` DESC, `ip` DESC LIMIT 100");
        														
        								if($results->num_rows){ ?>
        														
        									<table id="payouts" class="table table-sm table-striped table-hover compact responsive" width="100%">
        										<thead>
        											<tr>
        												<th scope="col">Date</th>
        												<th scope="col" data-sortable="false">Address</th>
        												<th scope="col" data-sortable="false" class="hidden-xs">IP Address</th>
        												<th scope="col" class="hidden-sm">Shortlink</th>
        												<th scope="col">Reward</th>
        											</tr>
        										</thead>
        										<tbody>
        														
        										<?php 
        															
        											while($row = $results->fetch_assoc()){
        												echo '
        													<tr>
        														<td scope="row">'.$row['timestamp'].'</td>
        														<td scope="row" class="text-break"><a href="#'.$row['address'].'">'.$row['address'].'</a></td>
        														<td scope="row">'.(($row['ip'])? '<a href="http://iphub.info/?ip='.$row['ip'].'">'.$row['ip'].'</a>' : 'Referral Payout').'</td>
        														<td scope="row">'.(($row['slid'])? '<a href="'.str_replace('ref/AvalonRychmon','member/dashboard',$settings['sldata'][$row['slid']]['referral']).'" target="_blank">'.$settings['sldata'][$row['slid']]['name'].'</a>' : 'No Shortlink').'</td>
        														<td scope="row" data-filter="'.$row['reward'].'">'.((strlen($row['reward']) >= '7')? number_format(($row['reward']/100000000),2).' '.$settings['currency'] : $row['reward'].' satoshi').'</td>
        													</tr>';
        											}
        											?>
        										</tbody>
        									</table>
        									<?php 	
        										}
        										else { echo '<h5>Not enough data to display this report!</h5>';}
        										?>
        							</div>
        						</div>	
        				<?php				
        				
        				}
        				?>
        				
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
	    <!-- DateRangePicker -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    	<script>
    	    $(function() {

                $('#date').daterangepicker({
                    "autoApply": true,
                    locale: {
                      format: 'YYYY-MM-DD'
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, function(start, end, label) {
                  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
                });
            
            });
    	    
    	  $(function() {

        	  $('#payouts').DataTable({
        			'pageLength': 10,
        			"order": []
        		});
        		$('#payouts tbody').on('click', 'a', function(e) {
        			e.preventDefault();
        			$('#payouts_filter input[type="search"]').val($(this).text()).keyup();
        			});
        			
        		 $('#reports').DataTable({
        			"order": [],
        			"paging":   false,
        			"searching": false
        		});
    			
    	  })
        </script>
    	
    </body>
</html>

<?php 
#print_pre($_SESSION);
$db->close;
