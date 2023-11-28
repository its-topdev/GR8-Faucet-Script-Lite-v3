<?php

## GR8 FAUCET SCRIPT LITE - ADMIN LOGS ##

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
			select.form-control{
			        padding: 0.175rem 0.75rem 0.375rem;
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
                    <h3 class="text-center text-secondary"><i class="far fa-list-alt"></i> Faucet Logs</h3>
    				<div class="container">
    				    <div class="row">
    						<form method="POST" class="form-inline col justify-content-center" accept-charset="utf-8" action="">
    							<select name="log" class="form-control mb-2 mr-sm-2">
    								<option value="" disabled selected>Select Log File</option>
    								<option value="action" >Actions Log</option>
    								<option value="error" >Error Log</option>
    							</select>
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
        					<div class="card"> 
        						<div class="card-header">
        							<h4 class="m-0"><i class="fas fa-mouse-pointer"></i> <?= ($_POST['log']) ? ucwords(str_replace('-',' ',$_POST['log'])) : 'Action';?> Log</h4>
        						</div>
        						<div class="card-body">
                        	        <table id="logs" class="table table-sm table-striped table-hover compact responsive" width="100%">
                            			<thead>
                            				<th scope="col">Date</th>
                            				<th scope="col">Address</th>
                            				<th scope="col">IP</th>
                            				<th scope="col">Status</th>
                            				<th scope="col" data-sortable="false">Notes</th>
                            			</thead>
                            
                            			<tbody>
                            
                            				<?php $logs = $db->query("SELECT * FROM `logs-".$faucetID."` WHERE `type` = '".(($_POST['log'])?: 'action')."' ORDER BY `id` DESC"); 
                            				
                            				while($row = $logs->fetch_assoc()){
                            				   echo '<tr>';
                            						echo '<td scope="row">'.$row['timestamp'].'</td>'; #date
                            						echo '<td scope="row" class="text-break"><a href="#">'.$row['address'].'</a></td>'; #address
                            						echo '<td scope="row" class="text-break"><a href="http://iphub.info/?ip='.$row['ip'].'" target="_blank">'.$row['ip'].'</a></td>'; #ip
                            						echo '<td scope="row">'.$row['status'].'</td>'; #status
                            						echo '<td scope="row" class="text-break">'.$row['notes'].'</td>'; #notes
                            				   echo '</tr>';
                            				}
                            
                            				?>
                            			</tbody>
                            		</table>
                            	</div>
                            </div>
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
    	    $(function() {

        	  $('#logs').DataTable();
        		$('#logs tbody').on('click', 'a', function(e) {
        			e.preventDefault();
        			$('#logs_filter input[type="search"]').val($(this).text()).keyup();
        			});
    			
    	  })
        </script>
    	
    </body>
</html>

<?php 
#print_pre($_SESSION);
$db->close();
