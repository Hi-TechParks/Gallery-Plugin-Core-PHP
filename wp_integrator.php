<?php
/*
Plugin Name: Panda Image Gallery
Plugin URI: http://www.schiffner.com/software/panda-image-gallery/
Description: Panda Image Gallery for Wordpress
Version: 4.0.0
Author: Christopher Schiffner
Author URI: http://www.schiffner.com
*/
?>
<?php

//define the level of error reporting
//error_reporting(-1);
//ini_set('error_reporting', E_ALL);
//error_reporting(0);

//add_action("widgets_init", array('wp_panda_image_gallery', 'register'));
add_action('admin_menu', array('wp_panda_image_gallery', 'wp_panda_image_gallery_create_menu'));
//add_action("wp_print_styles", array('wp_panda_image_gallery', 'styles'));
add_shortcode('wp_panda_image_gallery_page', array('wp_panda_image_gallery', 'wp_panda_image_gallery_page'));
register_activation_hook( __FILE__, array('wp_panda_image_gallery', 'activate'));
register_deactivation_hook( __FILE__, array('wp_panda_image_gallery', 'deactivate'));

class wp_panda_image_gallery{

    function activate(){
        //initiate the plugin data structure
        $data = array();

        if ( ! get_option('wp_panda_image_gallery')){
            add_option('wp_panda_image_gallery' , $data);
        } else {
            update_option('wp_panda_image_gallery' , $data);
        }
        
        if(file_exists(plugin_dir_path(__FILE__)."conf/auto_conf.php")){
        	include_once "conf/auto_conf.php";
        }
        
        $auto_conf["wordpress_plugin"]=true;
        			
        $pf=fopen(plugin_dir_path(__FILE__)."conf/auto_conf.php", 'w+');      
        flock($pf, LOCK_EX);   
        fwrite($pf, "<?php\n");
        fwrite($pf, "\$auto_conf=".var_export($auto_conf, TRUE).";\n");
        fwrite($pf, "?>\n"); 
        flock($pf, LOCK_UN);
        fclose($pf);       		
    }
    
    function deactivate(){
        //clean up after ourselves
        delete_option('wp_panda_image_gallery');
        
        if(file_exists(plugin_dir_path(__FILE__)."conf/auto_conf.php")){
        	include_once "conf/auto_conf.php";
        }
        
       	$auto_conf["wordpress_plugin"]=false;
        unset($auto_conf["base_url"]);
        			
        $pf=fopen(plugin_dir_path(__FILE__)."conf/auto_conf.php", 'w+');      
       	flock($pf, LOCK_EX);   
       	fwrite($pf, "<?php\n");
       	fwrite($pf, "\$auto_conf=".var_export($auto_conf, TRUE).";\n");
       	fwrite($pf, "?>\n"); 
       	flock($pf, LOCK_UN);
       	fclose($pf);
	}
    
    function register(){
        /*register_sidebar_widget('YouTube Integrator Video List', array('wp_youtube_integrator', 'wp_youtube_integrator_video_list_widget')); //initiate the widget
        register_widget_control('YouTube Integrator Video List', array('wp_youtube_integrator', 'wp_youtube_integrator_video_list_widget_control')); //intiate the widget controls*/
    }




    function wp_panda_image_gallery_create_menu() {
        //add options page under the settings menu
    	//add_options_page('Panda Image Gallery', 'Panda Image Gallery', 'manage_options', __FILE__, array('wp_panda_image_gallery', 'wp_panda_image_gallery_options'));

        //adds top level menu
    	add_menu_page('PANDA Image Gallery', 'PANDA Image Gallery', 'manage_options', __FILE__, array('wp_panda_image_gallery', 'wp_panda_image_gallery_options'));
    }
    function wp_panda_image_gallery_options(){
        //make sure users aren't trying to bypass security
    	if ( !current_user_can( 'manage_options' ) )  {
    		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    	}

        //Update settings
        $data = get_option('wp_panda_image_gallery');
        if (isset($_POST['wp_panda_image_gallery'])){
            $data['users'] = attribute_escape($_POST['users']);
            update_option('wp_panda_image_gallery', $data);
            echo '<div id="message" class="updated"><p>Settings Saved.</p></div>';
        }
        

		//display admin page in options panel
    	$content_temp="<h1>PANDA Image Gallery (Gallery Management)</h1>
    				   <iframe src='".plugin_dir_url(__FILE__)."/admin/index.php' style='width: 98.4%; border: 0; margin-top: 20px; padding-bottom: 10px;'  frameBorder='0' scrolling='no' id='PandaImageGalleryIFRAME'></iframe>
    				   <script type='text/javascript'>
    					  // Only do anything if jQuery isn't defined
    					  if (typeof jQuery == 'undefined') {
    					  
    					  	if (typeof $ == 'function') {
    					  		// warning, global var
    					  		thisPageUsingOtherJSLibrary = true;
    					  	}
    					  	
    					  	function getScript(url, success) {
    					  	
    					  		var script     = document.createElement('script');
    					  		     script.src = url;
    					  		
    					  		var head = document.getElementsByTagName('head')[0],
    					  		done = false;
    					  		
    					  		// Attach handlers for all browsers
    					  		script.onload = script.onreadystatechange = function() {
    					  		
    					  			if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
    					  			
    					  			done = true;
    					  				
    					  				// callback function provided as param
    					  				success();
    					  				
    					  				script.onload = script.onreadystatechange = null;
    					  				head.removeChild(script);
    					  				
    					  			};
    					  		
    					  		};
    					  		
    					  		head.appendChild(script);
    					  	
    					  	};
    					  	
    					  	getScript('http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', function() {
    					  	
    					  		if (typeof jQuery=='undefined') {
    					  		
    					  			// Super failsafe - still somehow failed...
    					  		
    					  		} else {
    					  		
    					  			// jQuery loaded! Make sure to use .noConflict just in case
    					  			fancyCode();
    					  			
    					  			if (thisPageUsingOtherJSLibrary) {
    					  
    					  				// Run your jQuery Code
    					  
    					  			} else {
    					  
    					  				// Use .noConflict(), then run your jQuery Code
    					  
    					  			}
    					  		
    					  		}
    					  	
    					  	});
    					  	
    					  } else { // jQuery was already loaded
    					  	// Run your jQuery Code
    					  
    					  };
    				   </script>
    				   
    				   <!--We don't want out user to know they're trapped in an iframe. Automatically resize it to make it invisible-->
    				   <script type='text/javascript' src='".plugin_dir_url(__FILE__)."scripts/jquery/iframeResizer.js'></script>
    				   <script type='text/javascript'>
    				   
    				   			iFrameResize({
    				   				log                     : false,                  // Enable console logging
    				   				enablePublicMethods     : false,                  // Enable methods within iframe hosted page
    				   				resizedCallback         : function(messageData){ // Callback fn when resize is received
    				   					$('p#callback').html(
    				   						'<b>Frame ID:</b> '    + messageData.iframe.id +
    				   						' <b>Height:</b> '     + messageData.height +
    				   						' <b>Width:</b> '      + messageData.width + 
    				   						' <b>Event type:</b> ' + messageData.type
    				   					);
    				   				},
    				   				messageCallback         : function(messageData){ // Callback fn when message is received
    				   					$('p#callback').html(
    				   						'<b>Frame ID:</b> '    + messageData.iframe.id +
    				   						' <b>Message:</b> '    + messageData.message
    				   					);
    				   					alert(messageData.message);
    				   				},
    				   				closedCallback         : function(id){ // Callback fn when iFrame is closed
    				   					$('p#callback').html(
    				   						'<b>IFrame (</b>'    + id +
    				   						'<b>) removed from page.</b>'
    				   					);
    				   				}
    				   			});
    				   
    				   
    				   	</script>
    					";

    	echo $content_temp;
    }


    function wp_panda_image_gallery_page($atts, $content = null) {
        //get database variables
        $data = get_option('wp_panda_image_gallery');

        /*extract(shortcode_atts(array(
            'user' => $data['users'][0],
    		'view_width' => $data['view_video_width'],
            'view_height' => $data['view_video_height'],
            'view_resolution' => $data['view_video_default_resolution'],
            'videos_per_page' => $data['videos_per_page'],
            'nav_style' => $data['nav_style'],
            'nav_location' => $data['nav_location']
    	), $atts));*/
    	
    	//We need to pass the current plugin base directory to the script for proper functionality
    	$base_url=explode("?", $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    	$base_url="http://".$base_url[0];
		
    	if(file_exists(plugin_dir_path(__FILE__)."conf/auto_conf.php")){
    		include_once "conf/auto_conf.php";
    	}
		
    	if($auto_conf["base_url"]!=$base_url){
	    	$auto_conf["base_url"]=$base_url;
					
	    	$pf=fopen(plugin_dir_path(__FILE__)."conf/auto_conf.php", 'w+');      
	    	flock($pf, LOCK_EX);   
	    	fwrite($pf, "<?php\n");
	    	fwrite($pf, "\$auto_conf=".var_export($auto_conf, TRUE).";\n");
	    	fwrite($pf, "?>\n"); 
	    	flock($pf, LOCK_UN);
	    	fclose($pf);
		}
    	
    	//process get string
    	$get_string="?cms=1&";
    	while($value = current($_GET)){
    	    if($get_string!="?cms=1&"){
    	        $get_string.="&";
    	    }
    	
    	    $get_string.=key($_GET)."=".$value;
    	    next($_GET);
    	}
    	 
    	//generate and display gallery inclusion code
    	$content_temp="<iframe src='".plugin_dir_url(__FILE__)."index.php{$get_string}' style='width: 100%; border: 0;'  frameBorder='0' scrolling='no' id='PandaImageGalleryIFRAME'></iframe>
    				   <script type='text/javascript'>
    					  // Only do anything if jQuery isn't defined
    					  if (typeof jQuery == 'undefined') {
    					  
    					  	if (typeof $ == 'function') {
    					  		// warning, global var
    					  		thisPageUsingOtherJSLibrary = true;
    					  	}
    					  	
    					  	function getScript(url, success) {
    					  	
    					  		var script     = document.createElement('script');
    					  		     script.src = url;
    					  		
    					  		var head = document.getElementsByTagName('head')[0],
    					  		done = false;
    					  		
    					  		// Attach handlers for all browsers
    					  		script.onload = script.onreadystatechange = function() {
    					  		
    					  			if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
    					  			
    					  			done = true;
    					  				
    					  				// callback function provided as param
    					  				success();
    					  				
    					  				script.onload = script.onreadystatechange = null;
    					  				head.removeChild(script);
    					  				
    					  			};
    					  		
    					  		};
    					  		
    					  		head.appendChild(script);
    					  	
    					  	};
    					  	
    					  	getScript('http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', function() {
    					  	
    					  		if (typeof jQuery=='undefined') {
    					  		
    					  			// Super failsafe - still somehow failed...
    					  		
    					  		} else {
    					  		
    					  			// jQuery loaded! Make sure to use .noConflict just in case
    					  			fancyCode();
    					  			
    					  			if (thisPageUsingOtherJSLibrary) {
    					  
    					  				// Run your jQuery Code
    					  
    					  			} else {
    					  
    					  				// Use .noConflict(), then run your jQuery Code
    					  
    					  			}
    					  		
    					  		}
    					  	
    					  	});
    					  	
    					  } else { // jQuery was already loaded
    					  	// Run your jQuery Code
    					  
    					  };
    				   </script>
    				   
    				   <!--We don't want out user to know they're trapped in an iframe. Automatically resize it to make it invisible-->
    				   <script type='text/javascript' src='".plugin_dir_url(__FILE__)."scripts/jquery/iframeResizer.js'></script>
    				   <script type='text/javascript'>
    				   
    				   			iFrameResize({
    				   				log                     : false,                  // Enable console logging
    				   				enablePublicMethods     : false,                  // Enable methods within iframe hosted page
    				   				resizedCallback         : function(messageData){ // Callback fn when resize is received
    				   					$('p#callback').html(
    				   						'<b>Frame ID:</b> '    + messageData.iframe.id +
    				   						' <b>Height:</b> '     + messageData.height +
    				   						' <b>Width:</b> '      + messageData.width + 
    				   						' <b>Event type:</b> ' + messageData.type
    				   					);
    				   				},
    				   				messageCallback         : function(messageData){ // Callback fn when message is received
    				   					$('p#callback').html(
    				   						'<b>Frame ID:</b> '    + messageData.iframe.id +
    				   						' <b>Message:</b> '    + messageData.message
    				   					);
    				   					alert(messageData.message);
    				   				},
    				   				closedCallback         : function(id){ // Callback fn when iFrame is closed
    				   					$('p#callback').html(
    				   						'<b>IFrame (</b>'    + id +
    				   						'<b>) removed from page.</b>'
    				   					);
    				   				}
    				   			});
    				   
    				   
    				   	</script>
    					";

    	return $content_temp;
    }
     
}

?>