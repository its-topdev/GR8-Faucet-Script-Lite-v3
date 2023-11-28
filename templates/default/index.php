<!--
    * Script: GR8 Faucet Script Lite v<?= $settings['version'];?>
    *
    * Functions: v<?= $fv;?>
    * Core: v<?= $cv;?>
    * Template: default
    *
    * Download this script at https://gr8.cc
-->

<!DOCTYPE html>
<html lang="en">
    <head><meta charset="windows-1252">
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        
        <noscript><meta http-equiv="refresh" content="0; url=<?= $settings['domain'];?>?e=nojs"></noscript>
        <title><?= $settings['name'];?> | Free <?= $currencies[$settings['currency']]; ?> Faucet</title>
        <meta name="description" content="<?= ($settings['description'])?: getReward($settings['reward'],'list').' at '.$settings['name'].' a free '.$currencies[$settings['currency']].' faucet';?>">
    	<meta name="keywords" content="">
        <link rel="canonical" href="<?= $settings['domain'];?>">
    	<?= ( $_GET['r'] || $_GET['theme'])? '<meta name="robots" content="noindex,nofollow">' : ''; ?>
    	
    	<!-- Favicon -->
        <link rel="icon" href="https://gr8.cc/assets/coins/<?= strtolower($settings['currency']);?>.webp">
        <!-- Bootswatch Themes -->
        <link rel="stylesheet" href="<?= (($settings['theme']) && $settings['theme'] != 'default')? 'https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/'.$settings['theme'].'/bootstrap.min.css': 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css';?>">
        <!-- Font Awesome -->
    		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
        <!-- Base CSS -->
        <link rel="stylesheet" href="libs/css/base.css">
        <style><?= $settings['css'];?></style>
    </head>

    <!-- START BODY -->
    <body class="d-flex flex-column">

    	<!-- Navbar - -->
    	<nav class="navbar navbar-expand-md <?= (in_array($settings['theme'], array('cyborg', 'solar','superhero'))) ? 'navbar-dark bg-dark' : (in_array($settings['theme'], array('litera','simplex','spacelab','default'))? 'navbar-light bg-light' : 'navbar-dark bg-primary');?>">
    	    <div class="container">
                <a class="navbar-brand" href="<?= $settings['domain'];?>"><?= $settings['name'];?></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav mr-auto">

                        <?php // Add Navlinks from DB
    						if($settings['navlinks']){
    							$navlinks ="<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    							if(preg_match_all("/$navlinks/siU", $settings['navlinks'], $matches, PREG_SET_ORDER)) {
    								foreach($matches as $match) {
    									echo str_replace('<a', '<a class="nav-item nav-link"', trim($match['0']));
    								}
    							}
    						}
    					?>

                    </div>

                    <?php if(!$settings['disable_balance']){ ?>
                            <span class="navbar-text text-nowrap">Balance: <?= ($settings['currency'] == 'DOGE')? number_format(($settings['balance']/100000000),2) : number_format(($settings['balance']/100000000),8);?> <span class="badge badge-secondary align-text-top"><?= ($settings['currency']);?></span></span>

					<?php } ?>
                </div>
            </div>
        </nav>

        <!-- START FAUCET CONTAINER -->
        <div class="container flex-grow my-4">

        	<!-- TOP ADSPACE -->
        	<div class="row my-4" id="top_adspace">
        		<div class="col-12 text-center p-0" style="overflow:hidden;">
        			<?= ($settings['top_ads'])?: '<img src="https://via.placeholder.com/728x90.png">'; ?>
        		</div>
        	</div>


    		<!-- CLAIM FORM -->
    		<div class="row my-2">
			    <div class="col-12 col-md-8 col-lg-6 order-md-2 mb-4 text-center">


    			<?php

    			switch(($status)?: $_SESSION[$faucetID]['status']){
    			    // LOGIN
    				case "login":
    				?>
    				    <?= $error;?>
        				<?= alert('This faucet requires a <a class="alert-link" href="'.$microwallets[$settings['microwallet']]['url'].'" target="_blank">'.$microwallets[$settings['microwallet']]['name'].' account</a> to claim.', 'warning');?>
    					<?= alert(getReward($settings['reward'],'list'), 'info');?>
        				<form class="form" method="POST" action="<?= $_SERVER['REQUEST_URI'];?>">
        				    <input type="hidden" name="session-token" value="<?= getToken();?>">

        				    <div class="form-group">
								<input type="text" class="form-control text-center" id="address" name="address" value="<?= ($_COOKIE[$faucetID.'-address'])?: (($_SESSION[$faucetID]['user']['address'])?: '');?>" placeholder="<?= ($microwallets[$settings['microwallet']]['placeholder'])?: 'Enter Your '.$currencies[$settings['currency']].' Address';?>" maxlength="110" pattern="[a-zA-Z0-9- ]{11,110}" required>
							</div>

        					<div class="form-group" id="captcha-adspace">
        						<?= ($settings['middle_ads'])?: '<img src="https://via.placeholder.com/300x250.png">';?>
        					</div>
							<div class="form-group">
							    <button type="button" class="btn btn-block btn-primary my-2" data-toggle="modal" data-target="#captchaModal">Login</button>
        					</div>

        					<!-- Captcha Modal -->
                            <div class="modal fade" id="captchaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header alert alert-info">
                                        <div class="modal-title w-100 text-center">
                                        <?php $antibotlinks = new antibotlinks(true, 'ttf,otf', array('abl_light_colors'=> (($settings['antibot_theme'])?:'dark')) );
                                            if(!$settings['disable_antibot']){
                        						$antibotlinks->generate(4, false);
                        						$antibotClass = randHash(12);
                                            }
                        					echo $_SESSION[$faucetID]['antibotlinks']['info'];
                    					?>
                    					</div>
                                    </div>
                                    <div class="modal-body">
                    					<div class="form-group row">
                    						<input type="hidden" name="antibotlinks" id="antibotlinks" value="">
                    						<div class="<?= $antibotClass;?> mr-auto float-left ml-4"></div>
                    						<div class="<?= $antibotClass;?> ml-auto float-right mr-4"></div>
                    					</div>
                    					<div class="form-group text-center">
                    					    <input type="hidden" name="captcha" id="captcha" value="<?= $settings['primary_captcha'];?>">
                    						<div class="mt-2">
                    						    <div class="captcha" id="<?= $settings['primary_captcha'];?>">
                    								<?= getCaptcha($settings['primary_captcha']);?>
                    							</div>
                    							<?php if($settings['secondary_captcha']){?>
                        							<div class="captcha d-none" id="<?= $settings['secondary_captcha'];?>">
                        								<?= getCaptcha($settings['secondary_captcha']);?>
                        							</div>
                        						    <a href="javascript:void(0);" id="switch">Switch to <?= $captchas[$settings['secondary_captcha']];?></a>
                        					    <?php } ?>
                        				    </div>
                        				</div>
                    					<div class="form-group" id="captcha-adspace">
                    						<?= ($settings['middle_ads'])?: '<img src="https://via.placeholder.com/300x250.png">';?>
                    					</div>
                    					<div class="form-group row">
                    						<div class="<?= $antibotClass;?> mr-auto float-left ml-4"></div>
                    						<div class="<?= $antibotClass;?> ml-auto float-right mr-4"></div>
                    					</div>
                    					<div class="form-group">
                    						<input type="submit" name="login" id="login" class="btn btn-block btn-primary my-2 <?= (!$settings['disable_antibot'])? 'd-none' : '';?>" value="Verify Captcha">
                    					</div>
                    				</div>
                                </div>
                              </div>
                            </div>
        				</form>
    			    <?php
    			    break;
    				// SHORTLINK
    				case "shortlink":
    				?>
    					<div class="form">
    					    <?= $error;?>
    					    <?php $_SESSION[$faucetID]['hash'] = randHash('12'); ?>
    					    <?= alert(getReward($settings['reward'],'list'), 'info');?>
            				<?= alert('Visit our <b>Sponsor\'s link</b> below to continue.', 'warning');?>
    						<div class="form-group" id="middle-adspace">
    							<?= ($settings['middle_ads'])?: '<img src="https://via.placeholder.com/300x250.png">';?>
    						</div>
    						<a href="<?= $settings['domain'];?>" onclick="$(location).attr('href','<?= ($_SESSION[$faucetID]['shortlink']['link'])?: '?hash='.$_SESSION[$faucetID]['hash'];?>');return false;" class="btn btn-block btn-primary my-2">Go to Sponsor's Link</a>
    					</div>
    				<?
    				break;
    				// PAID
    				case "paid":
    				?>
    					<div class="form">
        					<?= $error; ?>
        					<div class="form-group" id="middle-adspace">
        					   <?= ($settings['paid_box'])?: (($settings['middle_ads'])?: '<img src="https://via.placeholder.com/300x250.png">');?>
        					</div>
        				</div>
    			    <?
    				break;
    				// DEFAULT
    				default:
    				?>
    					<div class="form">
    					    <?= ($error)?: alert('Unknown Error','danger'); ?>
    						<div class="form-group" id="middle-adspace">
    						    <?= ($settings['middle_ads'])?: '<img src="https://via.placeholder.com/300x250.png">';?>
    						</div>
    					</div>

    			    <?php } ?>

    			    <!-- Ref Info -->
    			    <?php if($settings['referral'] > 0 && $_SESSION[$faucetID]['status'] != 'shortlink'){ ?>
            			    <div class="msg">
                                <b>Share this link with your friends to earn <?= $settings['referral'];?>% commission.</b><br>
                                <input type="text" onclick="this.select();" class="form-control text-center ref" value="<?= $settings['domain'];?>?r=<?= ($_COOKIE[$faucetID.'-address'])?: (($_SESSION[$faucetID]['user']['address'])?: 'your-address');?>">
                            </div>
                    <?php } ?>

    			</div>

    			<!-- LEFT ADSPACE -->
    			<div class="col-6 col-md-2 col-lg-3 order-md-1 p-0 text-center">
    				<div class="float-sm-right text-sm-right mr-1 sticky-top">
    					<?= ($settings['left_ads'])?: '<img src="https://via.placeholder.com/160x600.png">';?>
    				</div>
    			</div>

    			<!-- RIGHT ADSPACE -->
    			<div class="col-6 col-md-2 col-lg-3 order-md-3 p-0 text-center">
    				<div class="float-sm-left text-sm-left ml-1 sticky-top">
    					<?= ($settings['right_ads'])?: '<img src="https://via.placeholder.com/160x600.png">';?>
    				</div>
    			</div>

        	</div>

            <!-- Bottom ADSPACE -->
            <div class="row my-4">
            	<div class="col-12 text-center p-0" style="overflow:hidden;">
            		<?= ($settings['bottom_ads'])?: '<img src="https://via.placeholder.com/728x90.png">'; ?>
            	</div>
            </div>

            <!-- Recent Payouts -->
            <?php if($_SESSION[$faucetID]['status'] == 'login'){ ?>
                <div class="container my-4">
                	<div class="col-12">
                	    <div class="card border-info">
                            <div class="card-header bg-info text-center"><h2 class="font-weight-bold text-light m-0">Recent Payouts</h2></div>
                            <div class="card-body">
                            		<?php
                            		$recent = $db->query("SELECT `address`,`reward`,`timestamp` FROM `payouts-".$faucetID."` WHERE `type` = 'claim' ORDER BY `id` DESC LIMIT 10");
                            		if($recent){ ?>
                            		    <table class="table table-sm table-striped mb-2 text-center">
                                           <thead class="">
                                                <tr>
                                                    <th scope="col">User</th>
                                                    <th scope="col">Reward</th>
                                                    <th scope="col">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                while($row = $recent->fetch_assoc()){
                                                    echo '<tr>';
                                                    echo '<td scope="row" class="text-break">'.$row['address'].'</td>';
                                                    echo '<td scope="row">'.$row['reward'].' satoshi</td>';
                                                    echo '<td scope="row">'.$row['timestamp'].'</td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                		    </tbody>
                                	    </table>
                            	    <?php } ?>
                        	</div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        <!-- END CONTAINER -->
        </div>

        <!-- Footer -->
        <footer class="py-3">
            <div class="container text-center">
                <div class="col-12 col-md-6 col-lg-7 float-md-left">
                    <div class="text-center text-md-left">
                        Copyright&copy; <?= date('Y');?> <a href="<?= $settings['domain'];?>"><?= $settings['name'];?></a>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-5 float-md-right">
                    <div class="text-center text-md-right">
                        Powered by <a href="https://gr8.cc" target="_blank"><b>GR8</b> Scripts</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- JQUERY -->
    	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    	<!-- BOOTSTRAP -->
    	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
        <!-- AntiBot -->
        <?php if($_SESSION[$faucetID]['status'] == 'login'){ $antibotlinks->get_js($antibotClass); } ?>

        <!-- Start Adblock check -->
        <script>
            var show_ads_gr8_lite = false;
        </script>
        <script type="text/javascript" src="libs/show_ads.js"></script>

        <!-- Misc JS -->
        <script type="text/javascript" charset="utf-8">

            // Check Adblocker
            if(!show_ads_gr8_lite) {
            	$('div.flex-grow').html('<div class="row m-2"><div class="col-12 alert alert-danger py-5 text-center"><h1 class="display-4 font-weight-bold">Please disable your AdBlocker</h1><p class="lead">Advertisements help fund <?= $settings['name'];?>, so we can reward users like you!</p></div></div>');
            }

            // Disable Enter
            $(function() {
                $("form").keypress(function(e) {
                    if (e.which == 13) {
                        return false;
                    }
                });
            });

        	// Switch Captchas
        	$('#switch').on('click', function() {
        		var captcha = $('#captcha').val();
        		var captchas = ['solvemedia','recaptcha'];
                if (captcha == captchas[0]) {
                    $('#'+captchas[0]).addClass('d-none');
                    $('#'+captchas[1]).removeClass('d-none');
                    $('#captcha').val('recaptcha');
                    $('#switch').text('Switch to SolveMedia');
                }
                else {
                    $('#'+captchas[1]).addClass('d-none');
                    $('#'+captchas[0]).removeClass('d-none');
                    $('#captcha').val('solvemedia');
                    $('#switch').text('Switch to reCaptcha');
                }
            });


            console.log('%cScript: GR8 Faucet Script Lite v<?= $settings['version'];?>','font: 1.5em roboto; color: #5bc0de;');
            console.log('%cFunctions: v<?= $fv;?>','font: 1.5em roboto; color: #5bc0de;');
            console.log('%cCore: v<?= $cv;?>','font: 1.5em roboto; color: #5bc0de;');
            console.log('%cDownload this script at https://gr8.cc','font: 1.5em roboto; color: #5bc0de;');
            console.log('%cThanks for using GR8 Faucet Script Lite! 😊','font: 2em roboto; color: #5bc0de;');
        </script>

    </body>
</html>
