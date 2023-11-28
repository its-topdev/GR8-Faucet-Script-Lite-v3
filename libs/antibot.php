<?php 

class antibotlinks {
	
    var $link_count=3;
    var $links_data=array();
    var $use_gd=true;
    var $fonts=array();
    var $abl_settings=array('abl_light_colors'=>'off', 'abl_universe'=>'');

    public function __construct($use_gd=true, $font_type='', $abl_settings=array('abl_light_colors'=>'off')) {

        $this->abl_settings=$abl_settings;
        
        $this->use_gd=$use_gd;
        if (!empty($font_type)) {
            $font_type=str_replace(' ', '', $font_type);
            $font_type_array=explode(',', $font_type);
            $font_files_array = scandir('libs/fonts');
            foreach ($font_files_array as $font_file) {
                $ext=pathinfo($font_file, PATHINFO_EXTENSION);
                if (in_array($ext, $font_type_array)) {
                    $this->fonts[]=$font_file;
                }
            }
        }
    }

    public function generate($link_count=4, $force_regeneration=false) {
        
		global $_SESSION, $_POST, $faucetID, $settings;
			
        $this->link_count=$link_count;
        if ((!$force_regeneration)&&
                (isset($_SESSION[$faucetID]['antibotlinks']))&&
                (is_array($_SESSION[$faucetID]['antibotlinks']))&&
                ((isset($_POST['antibotlinks']))||($_SESSION[$faucetID]['antibotlinks']['time']>time()-60))) {
            return true;
        }
        
        // Animals
        $wordlist[] = array('giraffe' => 'g!r@ff3', 'hippo' => 'h!pp0', 'ostrich' => '05tr!ch', 'rhino' => 'rh!n0', 'zebra' => 'z3br@', 'mouse' => 'm0u53', 'tiger' => 't!g3r', 'camel' => 'c@m3l', 'horse' => 'h0rs3', 'monkey' => 'm0nk3y');
        // Colors
        $wordlist[] = array('purple' => 'purpl3', 'black' => 'bl@ck', 'orange' => '0r@ng3', 'green' => 'gr33n', 'yellow' => 'y3ll0w', 'brown' => 'br0wn', 'white' => 'wh!t3', 'beige' => 'b3!g3', 'silver' => 's!lv3r', 'ivory' => '!v0ry');
        // Food
        $wordlist[] = array('popcorn' => 'p0pc0rn', 'cookie' => 'c00k!3', 'bread' => 'br3@d', 'candy' => 'c@ndy', 'cheese' => 'ch33s3', 'sandwich' => 's@ndw!ch', 'cupcake' => 'cupc@k3', 'hotdog' => 'h0td0g', 'steak' => '5t3@k', 'honey' => 'h0n3y');
        // Occupations
        $wordlist[] = array('doctor' => 'd0ct0r', 'dentist' => 'd3nt!st', 'waiter' => 'w@!t3r', 'police' => 'p0l!c3', 'butcher' => 'butch3r', 'florist' => 'fl0r!st', 'teacher' => 't3@ch3r', 'pilot' => 'p!l0t', 'nurse' => 'nur53', 'driver' => 'dr!v3r');
        // Places
        $wordlist[] = array('store' => 'st0r3', 'house' => 'h0u53', 'beach' => 'b3@ch', 'school' => '5ch00l', 'range' => 'r@ng3', 'barber' => 'b@rb3r', 'bakery' => 'b@k3ry', 'museum' => 'mu53um', 'hospital' => 'h0sp!t@l', 'airport' => '@!rp0rt');

        
        $universe_number=mt_rand(0, count($wordlist)-1);
        $universe=$wordlist[$universe_number];

        $antibotlinks_solution='';

        $used_keywords_array=array();

        $antibotlinks_array=array();
        $antibotlinks_array['links']=array();
        $background_item=mt_rand(1, 3);
        for ($z=0;$z<$this->link_count;$z++) {
            $random_number=mt_rand(1000, 9999);
            $antibotlinks_solution.=$random_number.' ';

            // Choose the keyword
            do {
                $keyword=array_rand($universe, 1);
            } while (isset($used_keywords_array[$keyword]));
            $used_keywords_array[$keyword]=1;

            if (count($this->fonts)>0) {
                ob_start();
                // use ttf/otf
                $info_font=$this->fonts[mt_rand(0, count($this->fonts)-1)];
                $angle=mt_rand(-7, 7);

                // get dimension
                $infostring_length=(strlen($universe[$keyword])+1)*14;
                $imx = imagecreate($infostring_length, 40);
                $fontcolor = imagecolorallocate($imx, mt_rand(5, 50), mt_rand(5, 50), mt_rand(5, 50));
                $fontbackcolor = imagecolorallocate($imx, mt_rand(5, 50), mt_rand(5, 50), mt_rand(5, 50));
                $imageinfo=imagefttext($imx, 18, $angle, 1, 28, $fontcolor, 'libs/fonts/'.$info_font, $universe[$keyword]);

                // draw the image
                $infostring_length=$imageinfo[2]+16;//4
                $im = imagecreatetruecolor($infostring_length, 40);
                imagealphablending($im, true);
                $background = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagefill($im, 0, 0, $background);

                if ($this->abl_settings['abl_light_colors']=='light') {
                    $fontcolor = imagecolorallocatealpha($im, mt_rand(174, 254), mt_rand(174, 254), mt_rand(174, 254), mt_rand(0, 32));
                    $fontbackcolor = imagecolorallocatealpha($im, mt_rand(1, 80), mt_rand(1, 80), mt_rand(1, 80), mt_rand(0, 32));
                } else {
                    $fontcolor = imagecolorallocatealpha($im, mt_rand(1, 80), mt_rand(1, 80), mt_rand(1, 80), mt_rand(0, 32));
                    $fontbackcolor = imagecolorallocatealpha($im, mt_rand(174, 254), mt_rand(174, 254), mt_rand(174, 254), mt_rand(0, 32));
                }

                // draw some noise
                $noise_dots=$infostring_length/2;
                for ($zz=0;$zz<$noise_dots;$zz++) {
                    $noisex=mt_rand(1, $infostring_length-3);
                    $noisey=mt_rand(1, 40-3);
                    $noise_plus_or_minus=mt_rand(0, 1);
                    switch ($noise_plus_or_minus) {
                        case 0:
                            $noise_plus_or_minus=-1;
                        break;
                        default:
                            $noise_plus_or_minus=+1;
                        break;
                    }
                    imageline($im, $noisex, $noisey, $noisex+1, $noisey+$noise_plus_or_minus, $fontcolor);
                }
                //

                imagefttext($im, 18, $angle, 9, 29, $fontbackcolor, 'libs/fonts/'.$info_font, $universe[$keyword]);
				imagefttext($im, 18, $angle, 8, 28, $fontcolor, 'libs/fonts/'.$info_font, $universe[$keyword]);
				
                imagesavealpha($im, true);
                imagepng($im);
                $imagedata = ob_get_contents();
                ob_end_clean();
                $abdata='<img src="data:image/png;base64,'.base64_encode($imagedata).'" alt="" width="'.$infostring_length.'" height="40" />';
                $antibotlinks_array['links'][$z]['link']='<a href="/" rel="'.$random_number.'">'.$abdata.'</a>';
            } else {
                $abdata=$universe[$keyword];
                $antibotlinks_array['links'][$z]['link']='<a href="/" rel="'.$random_number.'">'.$abdata.'</a>';
            }
            
            $antibotlinks_array['links'][$z]['keyword']=$keyword;
        }

        $info_array=array();
        foreach ($antibotlinks_array['links'] as $link) {
            $info_array[]=$link['keyword'];
        }

        $info_string=implode(', ', $info_array);
        if ($this->use_gd) {
            ob_start();
            if (count($this->fonts)>0) {
                // use ttf/otf
                $info_font=$this->fonts[mt_rand(0, count($this->fonts)-1)];
                $angle=mt_rand(-1, 1);

                // get dimension
                $infostring_length=(strlen($universe[$keyword])+1)*14;
                $imx = imagecreate($infostring_length, 32);
                $fontcolor = imagecolorallocate($imx, mt_rand(5, 50), mt_rand(5, 50), mt_rand(5, 50));
                $fontbackcolor = imagecolorallocate($imx, mt_rand(5, 50), mt_rand(5, 50), mt_rand(5, 50));
                $imageinfo=imagefttext($imx, 16, $angle, 1, 14, $fontcolor, 'libs/fonts/'.$info_font, $info_string);

                // draw the image
                $infostring_length=$imageinfo[2]+10;
                $im = imagecreatetruecolor($infostring_length, 24);
                imagealphablending($im, true);
                $background = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagefill($im, 0, 0, $background);
                if ($this->settings['abl_light_colors']=='light') {
                    $fontcolor = imagecolorallocatealpha($im, mt_rand(174, 254), mt_rand(174, 254), mt_rand(174, 254), mt_rand(0, 32));
                    $fontbackcolor = imagecolorallocatealpha($im, mt_rand(1, 80), mt_rand(1, 80), mt_rand(1, 80), mt_rand(0, 32));
                } else {
                    $fontcolor = imagecolorallocatealpha($im, mt_rand(1, 80), mt_rand(1, 80), mt_rand(1, 80), mt_rand(0, 32));
                    $fontbackcolor = imagecolorallocatealpha($im, mt_rand(174, 254), mt_rand(174, 254), mt_rand(174, 254), mt_rand(0, 32));
                }
                imagecolortransparent($im, $background);
                imagerectangle($im, 0, 0, $infostring_length, 14, $background);

                $noise_dots=$infostring_length/2;
                for ($zz=0;$zz<$noise_dots;$zz++) {
                    $noisex=mt_rand(0, $infostring_length-3);
                    $noisey=mt_rand(1, 40-3);
                    $noise_plus_or_minus=mt_rand(0, 1);
                    switch ($noise_plus_or_minus) {
                        case 0:
                            $noise_plus_or_minus=-1;
                        break;
                        default:
                            $noise_plus_or_minus=+1;
                        break;
                    }
                    imageline($im, $noisex, $noisey, $noisex+1, $noisey+$noise_plus_or_minus, $fontcolor);
                }
                
				$angle=mt_rand(-1, 1);
                imagefttext($im, 16, $angle, 3, 19, $fontbackcolor, 'libs/fonts/'.$info_font, $info_string);
                imagefttext($im, 16, mt_rand(-1, 1), 2, 18, $fontcolor, 'libs/fonts/'.$info_font, $info_string);
                imagesavealpha($im, true);
                imagepng($im);
                $imagedata = ob_get_contents();
            } else {
                // use standard fonts
                $infostring_length=(strlen($info_string)+1)*8;
                $im = imagecreate($infostring_length, 24);
                $background = imagecolorallocate($im, mt_rand(0, 4), mt_rand(0, 4), mt_rand(0, 4));
                if ($this->abl_settings['abl_light_colors']=='light') {
                    $fontcolor = imagecolorallocatealpha($im, mt_rand(174, 254), mt_rand(174, 254), mt_rand(174, 254), mt_rand(0, 32));
                } else {
                    $fontcolor = imagecolorallocatealpha($im, mt_rand(1, 80), mt_rand(1, 80), mt_rand(1, 80), mt_rand(0, 32));
                }
                imagecolortransparent($im, $background);
                imagerectangle($im, 0, 0, $infostring_length, 16, $background);

                $noise_dots=$infostring_length/2;
                for ($zz=0;$zz<$noise_dots;$zz++) {
                    $noisex=mt_rand(0, $infostring_length-3);
                    $noisey=mt_rand(1, 40-3);
                    $noise_plus_or_minus=mt_rand(0, 1);
                    switch ($noise_plus_or_minus) {
                        case 0:
                            $noise_plus_or_minus=-1;
                        break;
                        default:
                            $noise_plus_or_minus=+1;
                        break;
                    }
                    imageline($im, $noisex, $noisey, $noisex+1, $noisey+$noise_plus_or_minus, $fontcolor);
                }
                    
                imagestring($im, 4, mt_rand(1, 5), 2, $info_string, $fontcolor);
                imagepng($im);
                $imagedata = ob_get_contents();
            }
            ob_end_clean();
			$antibotlinks_array['info']='Solve captcha then click on the AntiBot links in the following order to continue<br><img src="data:image/png;base64,'.base64_encode($imagedata).'" width="'.$infostring_length.'" class="img-fluid mx-auto"><a href="#" id="antibotlinks_reset" style="vertical-align: top;">Reset</a>';
        } else {
			$antibotlinks_array['info']='Solve captcha then click on the AntiBot links in the following order to continue<br>'.$info_string.'<br><a href="#" id="antibotlinks_reset" style="vertical-align: top;">Reset</a>';
        }

        shuffle($antibotlinks_array['links']);

        $antibotlinks_array['time']=time();
        $antibotlinks_array['solution']=trim($antibotlinks_solution);

        if (!$force_regeneration) {
            $antibotlinks_array['valid']=true;
        }

        $antibotlinks_array['universe']=$wordlist[$universe_number];

        $_SESSION[$faucetID]['antibotlinks']=$antibotlinks_array;
        return true;
    }

    public function check() {
		global $_SESSION, $faucetID, $_POST;
        
        $zero_solution='';
        for ($z=0;$z<$this->link_count;$z++) {
            $zero_solution.='0 ';
        }
        $zero_solution=trim($zero_solution);
        if (trim($_POST['antibotlinks'])==$zero_solution) {
            $_SESSION[$faucetID]['antibotlinks']['valid']=false;
            return $_SESSION[$faucetID]['antibotlinks']['valid'];
        }
        if ((trim($_POST['antibotlinks'])==$_SESSION[$faucetID]['antibotlinks']['solution'])&&(!empty($_SESSION[$faucetID]['antibotlinks']['solution']))) {
            $_SESSION[$faucetID]['antibotlinks']['valid'] = 'true';
        } else {
            $_SESSION[$faucetID]['antibotlinks']['valid'] = 'false';
        }
        return $_SESSION[$faucetID]['antibotlinks']['valid'];
    }

    public function get_links() {
		global $_SESSION, $faucetID;
        
		$retval='';
        foreach ($_SESSION[$faucetID]['antibotlinks']['links'] as $linkarray) {
            if (!empty($retval)) {
                $retval.='","';
            }
            $retval.= str_replace('"', '\"', $linkarray['link']);
        }
        return '["'.$retval.'"]';
    }

    public function get_js($antibotClass='') {
		
		global $_SESSION, $faucetID, $settings;
       
		   if($settings['disable_antibot']){ $_SESSION[$faucetID]['antibotlinks']['info'] = 'Solve captcha below to continue';
		   return; }
        ?>
		<script type="text/javascript">
		$(function() {
			var claim_button=$('input[id="login"]');
			var clicks = 0;
			var ablinks=<?= $this->get_links(); ?>;
			var interval;
			$('#antibotlinks_reset').hide();
			if (claim_button.length==0) {
				return;
			}
			if (ablinks.length>$('.<?= $antibotClass;?>').length) {
				alert('Not enough antibotlinks in the template.');
			}
			$('.<?= $antibotClass;?>').each(function(k){
				if (typeof(ablinks[k])!=='undefined') {
					$(this).html(ablinks[k]);
				}
			});
			
			$('input[id="login"]').addClass('d-none');

			$('.<?= $antibotClass;?> a').click(function() {
				$('#antibotlinks_reset').show();
				clicks++;
				$('#antibotlinks').val($('#antibotlinks').val()+' '+$(this).attr('rel'));
				if(clicks==ablinks.length) {
					$('input[id="login"]').removeClass('d-none');
				}
				$(this).hide();
				return false;
			});

			$('#antibotlinks_reset').click(function() {
				clicks = 0;
				$('#antibotlinks').val('');
				$('.<?= $antibotClass;?> a').show();
				if (typeof(interval)!='undefined') {
					clearInterval(interval);
				}
				$('input[id="login"]').addClass('d-none');
				$('#antibotlinks_reset').hide();
				return false;
			});
		});
		</script>
<?php
        
    }

    

    public function get_link_count() {
		global $_SESSION, $faucetID;
		
        return count($_SESSION[$faucetID]['antibotlinks']['links']);
    }

}