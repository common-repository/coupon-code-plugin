<?php
/*
Plugin Name: Coupon Code Plugin (Lite)
Plugin URI: http://www.couponcodeplugin.com/
Description: CouponCode lets you create and display protected coupon codes on your site
Version: 2.3
Author: BusinessWebsiteDev.com
Author URI: http://businesswebsitedev.com/
*/

/* History:
   v 0.2 - branched from premium version, fixed URI issue
   v 0.3 - Updated links and details
   v 2.0 - Corrected Update issue caused by version number
   v 2.1 - Minor Coding Update
   v 2.2 - Updated core ZeroClipboard system to increase security
   v 2.3 - Updated descriptions and landing page
*/

//error_reporting (E_ALL ^ E_NOTICE);

$couponcode_version = "2.3 (Lite)";

define('CC_PLUGIN_PATH', 'couponcode.php');
define('CC_PLUGIN_EXTERNAL_PATH', '/wp-content/plugins/coupon-code-plugin/');
define('CC_PLUGIN_STYLE_PATH', 'wp-content/plugins/coupon-code-plugin/styles/');
define('CC_PLUGIN_ICON_PATH', 'wp-content/plugins/coupon-code-plugin/icons/');


function coupon_func($atts) {
	extract(shortcode_atts(array(
		'code' => 'empty',
		'mode' => 'default',
	), $atts));

   global $wpdb;
   
   // Read data from options
   $options = get_option('couponcode');
   $target = $options['target'];

   $call_to_action = "Click to see deal";
   
   $table_name = $wpdb->prefix . "couponcodes";
   $strSQL = "SELECT * FROM ".$table_name." WHERE shortcode='$code'";
   $coupon = $wpdb->get_row($strSQL);

   // Read data from selected coupon
   $strID = $coupon->id;
   $strName = htmlspecialchars_decode($coupon->name, ENT_NOQUOTES);
   $strCoupon = htmlspecialchars_decode($coupon->couponcode, ENT_NOQUOTES);
   $strURL = htmlspecialchars_decode($coupon->url, ENT_NOQUOTES);
   $intShown = $coupon->shown;

   $return = "";

   if (strlen($strCoupon) != 0 ) {

      $strSQL = "UPDATE $table_name SET shown = ". ($intShown + 1) . " WHERE id=" . $strID;
      $wpdb->query($strSQL);

       switch ($mode) {
       
          case "raw":
          
             $return .= "<p>Raw Output:</p><ul>";
             $return .= "<li><strong>Coupon ID:</strong> $strID</li>";
             $return .= "<li><strong>Coupon Name:</strong> $strName</li>";
             $return .= "<li><strong>URL:</strong> $strURL</li>";
             $return .= "</ul>";
          
             break;
             
          default:
             $window = "";
             if ( $target != "same") {
                $window = ' target="_blank"';
             }
          
            $return .= "<span class='couponcode $code' id='couponcodeplugin'><a href='$strURL'$window>$strCoupon</a></span>";
            //$return .="<button id='copy-button12' data-clipboard-text='Copy Me!' title='Click to copy me.'>Copy to Clipboard</button>";
       }
   } else {
      $return = "##Unknown coupon##";
   }

	 return $return;
}

add_shortcode('coupon', 'coupon_func');


function couponcode_uninstall () {

   // Cleanup routine. - Deactivated to preserve data on upgrade

   global $wpdb;

//   $table_name = $wpdb->prefix . "couponcodes";
//   $wpdb->query("DROP TABLE {$table_name}");


}



function couponcode_install () {
   global $wpdb;

   $couponcode_db_version = "0.3";
   
   $installed_ver = get_option("couponcode_db_version");
      
   if ($installed_ver != $couponcode_db_version) {
      require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

      $table_name = $wpdb->prefix . "couponcodes";
     
      // Create Coupon Table
      
      $sql = "CREATE TABLE " . $table_name . " (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
     	   date_start datetime NOT NULL,
	       date_end datetime NOT NULL,
	       name tinytext NOT NULL,
	       couponcode text NOT NULL,
	       shortcode text NOT NULL,
	       url tinytext,
	       shown mediumint(9),
	       clicked mediumint(9),
	       UNIQUE KEY id (id)
	      );";

      dbDelta($sql);
     
  
      update_option("couponcode_db_version", $couponcode_db_version);
      
      //set initial values if none exist
      $options = get_option('couponcode');
      if ( !is_array($options) ) {
         $options = array( 'maskURL'=>'Yes', 'style'=>'default', 'call_to_action'=>'click to copy & see the deal', 'target'=>'same', 'custom1'=>'#ffffe8', 'custom2'=>'#f0e9b2', 'custom3'=>'#ed3800');
         update_option('couponcode', $options);
      }
       
   }
   
}

function couponcode_welcome() {

global $couponcode_version;
global $wp_version;

// first let's check if database is update date
couponcode_install();

// Use WordPress built-in RSS handling
require_once (ABSPATH . WPINC . '/rss.php');
$rss_feed = "http://www.couponcodeplugin.com/feed/";
$rss = @fetch_rss( $rss_feed );

?>
<link href="../wp-content/plugins/coupon-code-plugin/admin/style.css" rel="stylesheet" type="text/css" />

<div class="wrap couponcode">
  
	<div class="CC_intro">
	<img src="../wp-content/plugins/coupon-code-plugin/admin/coupon-code-logo.png" alt="Coupon Code Plugin" class="couponlogo" />
	<p><em>Version: <?php echo $couponcode_version ?></em></p>
	<p style="margin-bottom: 0;">Thank you for choosing Coupon Code Lite plugin. We provide premium support for all of our customers, completely FREE of charge. <a href="http://couponcodeplugin.com/support/" title="Support" target="_blank">Click here to contact our support team.</a></p>
	
    <div class="latestnews">
        <h3>EXCLUSIVE SPECIAL OFFER</h3>
		Upgrade to Coupon Code Elite and receive:
		<ul class="elitelist">
		<li><strong>A Whopping 50% Discount off Coupon Code Elite</strong>
		<br /><strong>Plus, 4 extra plugins for FREE!</strong> (detailed below)</li>
		<li><strong><a href="http://businesswebsitedev.com/products-and-services/wordpress-plugins/social-gateway/" target="_blank" title="Social Gateway Plugin">Social Gateway Plugin</a></strong> (normally $19.99)
		<br />Use Social networks like Facebook, Twitter, LinkedIn and Google+ like a payment gateway.</li>
		<li><strong><a href="http://businesswebsitedev.com/products-and-services/wordpress-plugins/snipit/" target="_blank" title="SnipIT Plugin">SnipIT Plugin</a></strong> (normally $19.99)
		<br />Automated clipping and watermarking of your images as you upload them.</li>
		<li><strong><a href="http://businesswebsitedev.com/products-and-services/wordpress-plugins/content-timeout/" target="_blank" title="Content Timeout Plugin">Content Timeout Plugin</a></strong> (normally $19.99)
		<br />Allow access to content for a set time before asking users to register. Works with Membership Plugins.</li>
		<li><strong><a href="http://businesswebsitedev.com/products-and-services/wordpress-plugins/wp-o-matic/" target="_blank" title="WP-O-Matic Plus">WP-O-Matic Plus</a></strong> (normally $19.99)
		<br />The biggest, best and fully updated RSS aggregation Plugin for WordPress.</li>
		</ul>
		<strong>Giving you a total saving of $98.46!</strong>
		<br />Please note, this special offer is only available for Coupon Code Lite users like you.
		<p><strong><a href="https://businesswebsitedev.com/clients/cart.php?a=add&bid=11" target="_blank" title="Read More">ORDER NOW!</a></strong></p>
    </div>
    
    <div class="upgradeccp">
        <h3>Coupon Code Elite Features</h3>
		Coupon Code Elite contains many fantastic features to help you increase and secure your income.
		<ul class="elitelist">
		<li><strong>Affiliate Link Masking</strong>
		<br />Mask your affiliate links to prevent bypassing of affiliate link and to provide coupon stats.</li>
		<li><strong>Over 16 Billion Style Combinations!</strong>
		<br />15 coupon styles and 6 coupon buttons, plus style customization with no need to edit any code!</li>
		<li><strong>Coupon Feedback</strong>
		<br />Users can vote if the coupon worked or not. You can easily see which coupons are not working.</li>
		<li><strong>Coupon Management</strong>
		<br />Easily see impressions, clicks and feedback, allowing you to see which coupons are the most popular.</li>
		</ul>
		<strong><a href="http://couponcodeplugin.com/the-wordpress-coupon-code-plugin/" title="Read More">Read More...</a></strong>
    </div>

    <div style="clear:both"></div>
</div>

<div style="clear:both"></div>

<?php couponcode_options();  ?>

</div>

<?php   
}

function couponcode_options() {
   $options = get_option('couponcode');
	
   //set initial values if none exist
   if ( !is_array($options) ) {
      $options = array( 'style'=>'default', 'target'=>'same', 'custom1'=>'#ffffe8', 'custom2'=>'#f0e9b2', 'custom3'=>'#ed3800');
   }

   if ( $_POST['couponcode-submit'] ) {

      // security check
      check_admin_referer( 'CC_nonce');

      $options['style'] = strip_tags(stripslashes($_POST['CC_style']));
      $options['target'] = strip_tags(stripslashes($_POST['CC_target']));
      $options['custom1'] = strip_tags(stripslashes($_POST['CC_custom1']));
      $options['custom2'] = strip_tags(stripslashes($_POST['CC_custom2']));
      $options['custom3'] = strip_tags(stripslashes($_POST['CC_custom3']));
      $options['customicon'] = strip_tags(stripslashes($_POST['CC_customicon']));

      update_option('couponcode', $options);
   }

   $style = htmlspecialchars($options['style'], ENT_QUOTES);
   $target = htmlspecialchars($options['target'], ENT_QUOTES);
   $custom1 = htmlspecialchars($options['custom1'], ENT_QUOTES);
   $custom2 = htmlspecialchars($options['custom2'], ENT_QUOTES);
   $custom3 = htmlspecialchars($options['custom3'], ENT_QUOTES);
   $customicon = htmlspecialchars($options['customicon'], ENT_QUOTES);

  // implement fudge for people upgrading having blank values and fabtastic doesn't handle blank fields very well
  if ( $custom1 == "" ) $custom1 = "#ffffe8";
  if ( $custom2 == "" ) $custom2 = "#f0e9b2";
  if ( $custom3 == "" ) $custom3 = "#ed3800";

  // Prepare style list based on styles in style folder
	$folder_array=array();
	$folder_count = 1;

	$path=ABSPATH.CC_PLUGIN_STYLE_PATH;
	
	if ($handle = opendir($path)) { 
		while (false !== ($file = readdir($handle))) { 
			if ( !($file == "." || $file == "..") ) { 
				$folder_array[$folder_count]=$file;
				$folder_count++;
				}
		   } 
		} else {
		  echo "Cannot open: ".$path;
		}
	sort($folder_array); 

  // Prepare icon list based on styles in icon folder
	$icon_array=array();
	$icon_count = 1;

	$path=ABSPATH.CC_PLUGIN_ICON_PATH;
	
	if ($handle = opendir($path)) { 
		while (false !== ($file = readdir($handle))) { 
			if ( !($file == "." || $file == "..") ) { 
				$icon_array[$icon_count]=$file;
				$icon_count++;
				}
		   } 
		} else {
		  echo "Cannot open: ".$path;
		}
	sort($icon_array); 
	

?>
<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('wpurl') . CC_PLUGIN_EXTERNAL_PATH . (($style == "custom" ) ? 'customCSS.php' : 'styles/'.$style.'/default.css') ; ?>" />
 
<div class="wrap couponcode">
<div class="wrap"> 

<!-- prev action: <?php echo $_SERVER['PHP_SELF'].'?page='.CC_PLUGIN_PATH; ?> --> 
 
  <form name="form1" method="post" action="">
  
  <?php wp_nonce_field('CC_nonce'); ?>
  
  <h2 class="settings"><em><?php _e('General Settings') ?></em></h2>

    <table width="100%" cellspacing="2" cellpadding="5" class="widefat" style="margin-top: 1em;"> 
      <tr valign="top"> 
        <th scope="row" class='row-title'><?php _e('Mask URL:') ?></th> 
        <td class='desc'>
        <select id="CC_maskURL" name="CC_maskURL">
                <option value="No" <?php if ($maskURL!='Yes') echo 'selected'; ?>>No</option>
         </select>
        <br />
        <p><?php _e('Would you like to mask the outgoing URL?') ?> (Unlock this feature in the Pro version)</p></td> 
      </tr> 
      <tr valign="top" class="alternate"> 
        <th scope="row" class='row-title'><?php _e('Use same browser window:') ?></th> 
        <td class='desc'>
        <select id="CC_target" name="CC_target">
                <option value="same" <?php if ($target=='same') echo 'selected'; ?>>Yes</option>
                <option value="new" <?php if ($target!='same') echo 'selected'; ?>>No, open in new window</option>
         </select>
        <br />
        <p><?php _e('Would you like the target site to be opened in the same or a new window?') ?></p></td> 
      </tr> 
      <tr valign="top"> 
        <th scope="row" class='row-title'><?php _e('Call to action:') ?></th> 
        <td class='desc'>
        <input type="text" name="CC_call_to_action" value="Click to see deal" maxlength="255" size="50" />
        <br>
        <p><?php _e('Invitation to users to click and use coupon.') ?> (Unlock this feature in the Pro version)</p></td> 
      </tr> 
      <tr valign="top" class="alternate"> 
        <th scope="row" class='row-title'><?php _e('Style:') ?></th> 
        <td class='desc'>
           <select id="CC_style" name="CC_style">
            <?php                           
               foreach ($folder_array as $thisstyle) {
			      echo '<option value="'.$thisstyle.'"';
			      if ($thisstyle == $style) 
				     echo ' selected ';
			      echo '>'.$thisstyle;
			      echo '</option>';
		       } ?>
            </select>
        <br />
        <p><?php _e('Choose a graphical style for your coupon.') ?> (Get more styles in the Pro version)</p></td> 
      </tr> 
                  
      <tr valign="top"> 
        <th scope="row" class='row-title' style="border-bottom: 0;"><?php _e('Style Sample:') ?></th> 
        <td class='desc'  style="border-bottom: 0pt none; padding: 5px 0 5px 10px;" valign="bottom">
        <span class='couponcode' id='couponcodeplugin'><a href='#'><span id='coupon'>SAMPLE</span><!-- <span class='couponcodehover' style="width: 175px;"><?php // echo $call_to_action; ?></span> --></a></span>
        </td>
      </tr> 
    </table>

	<input type="hidden" id="couponcode-submit" name="couponcode-submit" value="1" />

    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" />
    </p>
  </form> 
</div>
</div>
<?php

}

function couponcode_header() {
   $options = get_option('couponcode');
   $style = $options['style'];
   $target = $options['target'];
   $call_to_action = "Click to see deal";

   ?>
      <!-- coupconcode start -->
      
    <script type="text/javascript">
    
        // where are we directing to
        var coupon_direct = "";
    
        jQuery(function() {

       				 var clip = new ZeroClipboard( document.getElementById("copy-button12"), {
       				 
					  moviePath: "<?php echo get_bloginfo('wpurl') . CC_PLUGIN_EXTERNAL_PATH . 'js/ZeroClipboard.swf' ?>"
					  
						} );


					  clip.on( 'load', function(client) {
						//alert( "movie is loaded" );
					  } );

					  clip.on( 'complete', function(client, args) {
						//alert("Copied text to clipboard: " + args.text );
					  } );

					  clip.on( 'mouseover', function(client) {
						//alert(this.id);
					  } );

					  clip.on( 'mouseout', function(client) {
						// alert("mouse out");
					  } );

					  clip.on( 'mousedown', function(client) {

						// alert("mouse down");
					  } );

					  clip.on( 'mouseup', function(client) {
						// alert("mouse up");
					  } );  
				});                            
    </script>
      
      <link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo('wpurl') . CC_PLUGIN_EXTERNAL_PATH . (($style == "custom" ) ? 'customCSS.php' : 'styles/'.$style.'/default.css') ; ?>" />

      <!-- coupconcode end -->
   <?php

}

function couponcode_manage() {

   global $wpdb;

   // Primary action
   if(isset($_REQUEST["CC_action"])):

      // security check
      check_admin_referer( 'CC_nonce');

     if($_GET["CC_action"] == "edit"):
        echo "do it";
     elseif($_GET["CC_action"] == "delete"):
     		 $table_name = $wpdb->prefix . "couponcodes";
         $strSQL = "DELETE FROM $table_name WHERE id=" . $_GET["CC_id"];
         $wpdb->query($strSQL);         
     endif; 
   endif;

   $options = get_option('couponcode');

   $nonce = wp_create_nonce ('CC_nonce')

?>

<link href="../wp-content/plugins/coupon-code-plugin/admin/style.css" rel="stylesheet" type="text/css" />

<div class="wrap couponcode"> 
<div class="wrap">
   <p>Some features are not available in the Lite version - Go Pro, <a href="http://www.couponcodeplugin.com/discount/">Get $10 Off Here</a></p>
	<h2 class="manage"><em><?php _e('Manage Coupons') ?></em></h2>
	
	<fieldset class="options">
	<legend>Active Coupons</legend>
	<?php
		$table_name = $wpdb->prefix . "couponcodes";
		$strSQL = "SELECT id, name, couponcode, shown, clicked FROM $table_name ORDER BY id";
		$rows = $wpdb->get_results ($strSQL);
		
	?>
	<table class="widefat">
   <thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Coupon Code</th>
			<th>Shown</th>
			<th>Clicked</th>
			<th>Actions</th>
		</tr>
   </thead>
   	
	<?php if (is_array($rows)): ?>
		<?php foreach ($rows as $row) { 
             $style=" ";
             if($intAlternate==1) $style=$style."alternate "; 
             ?>
			<tr<?php if($style!=" "): ?> class="<?php echo $style ?>"<?php endif; ?>>
				<td><?php print $row->id; ?></td>
				<td><?php print $row->name; ?> </td>
				<td><?php print $row->couponcode; ?></td>
				<td>Available in Pro version</td>
				<td>Available in Pro version</td>
				<td>
            <a href="javascript:if(confirm('Delete coupon \'<?php print addslashes(str_replace ( '"' , "'" , $row->name)); ?>\'? (This will erase all details about this coupon)')==true) location.href='admin.php?page=couponcode-manage&amp;CC_action=delete&amp;CC_id=<?php echo $row->id ?>&amp;_wpnonce=<?php echo $nonce; ?>'" class="edit">Delete</a>
        </td>
			</tr>
			<?php
				if($intAlternate == 1):
					$intAlternate=0;
				else:
					$intAlternate=1;
				endif;
			?>
		<?php } ?>
	<?php else: ?>
		<tr><td colspan="5">No coupons defined</td></tr>
	<?php endif; ?>
	</table>
	</fieldset>

</div>
</div>
<?php
}

function CC_resetgetvars()
{
	unset($GLOBALS['_GET']["CC_action"]);
	unset($GLOBALS['_GET']["CC_id"]);
}

function CC_chkfields($strName, $strCoupon,$strURL,$strShortCode)
{
	if($strName == "" || $strCoupon == "" || $strURL == "" || $strShortCode == ""):
		$bitError = 1;
	endif;
	return $bitError;
}


function couponcode_add() {

   global $wpdb;
   $table_name = $wpdb->prefix . "couponcodes";
   $options = get_option('couponcode');

   // Primary action
   if(isset($_REQUEST["CC_action"])):

      // security check
      check_admin_referer( 'CC_nonce');

      if($_POST["CC_action"] == "Add Coupon"):
         $strSaveName = strip_tags(htmlspecialchars($_POST["CC_name"]));
         $strSaveCoupon = $_POST["CC_coupon"];
         $strSaveURL = $_POST["CC_URL"];
         $strSaveShortCode = $_POST["CC_ShortCode"];
      elseif($_POST["CC_action"] == "Update Coupon"):
         $strUpdateID = $_POST["CC_id"];
         $strSaveName = strip_tags(htmlspecialchars($_POST["CC_name"]));
         $strSaveCoupon = $_POST["CC_coupon"];
         $strSaveURL = $_POST["CC_URL"];
         $strSaveShortCode = $_POST["CC_ShortCode"];

         $bolUpdate = true;
      elseif($_GET["CC_action"] == "edit"):
         $strSQL = "SELECT * FROM ".$table_name." WHERE id=".$_GET["CC_id"];
         $resultEdit = $wpdb->get_row($strSQL);
         
         $strUpdateID = $_GET["CC_id"];
         $strSaveName = htmlspecialchars_decode($resultEdit->name, ENT_NOQUOTES);
         $strSaveCoupon = htmlspecialchars_decode($resultEdit->couponcode, ENT_NOQUOTES);
         $strSaveURL = htmlspecialchars_decode($resultEdit->url, ENT_NOQUOTES);
         $strSaveShortCode = htmlspecialchars_decode($resultEdit->shortcode, ENT_NOQUOTES);

         $bolUpdate = true;
         CC_resetgetvars();
      endif;
   endif;

   // Validation & Save
   if($_POST["CC_action"] == "Add Coupon"):
      if(CC_chkfields($strSaveName, $strSaveCoupon,$strSaveURL,$strSaveShortCode)==1):
         $strMessage = "Please fill out all fields.";
      endif;

      // ensure shortcode is unique
      if ( $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE shortcode='".$strSaveShortCode."';")) >0 ):
         $strMessage = "Shortcode already in use, please specify a different one";
      endif;
     

      if ($strMessage == ""):
         
         $strSQL = "INSERT INTO $table_name ( name,URL,couponcode,shortcode ) VALUES('".$strSaveName."','".$strSaveURL."','".$strSaveCoupon."','".$strSaveShortCode."')";                  
         $wpdb->query($strSQL);

         //echo $strSQL;
         
         $strMessage = "Coupon added";
         $strSaveName = "";
         $strSaveURL = "";
         $strSaveCoupon = "";
         $strSaveShortCode = "";
         
      endif;
      CC_resetgetvars();
   elseif($_POST["CC_action"] == "Update Coupon"):
      if(CC_chkfields($strSaveName, $strSaveCoupon,$strSaveURL,$strSaveShortCode)==1):
         $strMessage = "Please fill out all fields.";
      endif;

      if ($strMessage == ""):

         $strSQL = "UPDATE $table_name SET name='$strSaveName', URL = '$strSaveURL', couponcode = '$strSaveCoupon', shortcode = '$strSaveShortCode' WHERE id=" . $_POST["CC_id"];

         $strMessage = "Coupon updated";
         
         $wpdb->query($strSQL);
         CC_resetgetvars();
      endif;
   endif;
			
   ?>
   
   <link href="../wp-content/plugins/coupon-code-plugin/admin/style.css" rel="stylesheet" type="text/css" />

	<div class="wrap couponcode">
		<?php if($strMessage != ""):?>
			<fieldset class="options">
				<legend>Information</legend>
				<p><font color=red><strong><?php print $strMessage ?></strong></font></p>
			</fieldset>
		<?php endif; ?>
		
		<h2 class="details"><em>Coupon Details</em></h2>

    <!-- prev action: <?php echo $_SERVER['PHP_SELF']; ?>?page=couponcode-add -->

		<form method="post" action="" id="editform">

    <?php wp_nonce_field('CC_nonce'); ?>

    <table width="100%" cellspacing="2" cellpadding="5" class="widefat"> 
      <tr valign="top" class="alternate"> 
        <th scope="row"><?php _e('Name:') ?></th> 
        <td><input type="text" name="CC_name" value="<?php print $strSaveName ?>" maxlength="255" size="50" /><br>
        <p><?php _e('Specify the name for your coupon.') ?></p></td> 
      </tr>
      <tr valign="top"> 
        <th scope="row"><?php _e('Coupon:') ?></th> 
        <td><input type="text" name="CC_coupon" value="<?php print $strSaveCoupon ?>" maxlength="255" size="50" /><br>
        <p><?php _e('Please specify the coupon code') ?></p></td> 
      </tr>
      <tr valign="top" class="alternate"> 
        <th scope="row"><?php _e('URL:') ?></th> 
        <td><input type="text" name="CC_URL" value="<?php print $strSaveURL ?>" maxlength="255" size="100" /><br>
        <p><?php _e('Please specify the URL to direct the user to') ?></p></td> 
      </tr>
      <tr valign="top"> 
        <th scope="row" style="border-bottom: 0;"><?php _e('ShortCode:') ?></th> 
        <td style="border-bottom: 0;"><input type="text" name="CC_ShortCode" value="<?php print $strSaveShortCode ?>" maxlength="255" size="50" /><br>
        <p><?php _e('Please supply a shortcode for this coupon') ?></p></td> 
      </tr>
     </table>

		<?php if($bolUpdate == true): ?>
			<p class="submit"><input type="hidden" name="CC_id" value="<?php echo $strUpdateID ?>"><input type="hidden" name="CC_action" value="Update Coupon">
			<input type="submit" name="CC_doit" value="Update Coupon" class="button"></p>
		<?php else: ?>
			<p class="submit"><input type="hidden" name="CC_action" value="Add Coupon"><input type="submit" name="CC_doit" value="Add Coupon &raquo;" class="button" ></p>
		<?php endif; ?>


			</form>
		
	</div>
<?php
}


function insertCouponCodeSelector() {

   global $wpdb;
	 $table_name = $wpdb->prefix . "couponcodes";
	 $strSQL = "SELECT name, couponcode, shortcode FROM $table_name";
	 $rows = $wpdb->get_results ($strSQL);

?>
   <table class="form-table">
      <tr valign="top">
         <th scope="row"><label for="ccode_Admin_id">Select a coupon</label></th>
         <td>
            
	<?php if (is_array($rows)): ?>
        <select name="ccode_Admin[code]" id="ccode_Admin_id" style="width:95%;">
		       <?php foreach ($rows as $row) { 
		          echo '<option value="'.$row->shortcode.'">'.$row->name.'</option>';
           } ?>
         </select> 
         <p>Please select which coupon you would like to insert</p>
  <?php else:
          echo "Please create some coupons first"; 
         endif; 
  ?>          
            
         </td>
         <td>
         <p class="submit">
      <input type="button" onclick="return CouponCode_Setup.sendToEditor(this.form);" value="Insert Coupon" />
   </p>
         </td>
      </tr>
   </table>
   
<?php
}

function couponcode_adminmenu(){

   // add new top level menu page
   $page = add_menu_page ('CouponCode', 'CouponCode' , 7 , CC_PLUGIN_PATH , 'couponcode_welcome', get_settings('siteurl').CC_PLUGIN_EXTERNAL_PATH."admin/scissors.png" );

   // add submenus
   add_submenu_page (CC_PLUGIN_PATH, 'Manage Coupons', 'Manage Coupons', 7 , 'couponcode-manage', 'couponcode_manage' );
   add_submenu_page (CC_PLUGIN_PATH, 'Add Coupon', 'Add Coupons', 7 , 'couponcode-add', 'couponcode_add' );
   
   add_meta_box('CouponCode_Admin', 'Insert Coupon Code', 'insertCouponCodeSelector', 'post', 'normal', 'high');
   add_meta_box('CouponCode_Admin', 'Insert Coupon Code', 'insertCouponCodeSelector', 'page', 'normal', 'high');   

   // add farbtastic script script only to the Settings page
   add_action('admin_print_scripts-' . $page, 'couponcode_admin_styles');

}

function couponcode_admin_styles() {
     /*
      * It will be called only on your plugin admin page, enqueue our script here
      */
     
}


function couponcode_init() {

    // Add scripts where needed
    if ( is_admin() ) {
       wp_enqueue_script('CC_Admin', get_bloginfo('wpurl') . CC_PLUGIN_EXTERNAL_PATH . 'couponcodeAdminjs.php', array('jquery'), '1.0.0' );
    } else {
       wp_enqueue_script( 'ZeroClipboard', get_bloginfo('wpurl') . CC_PLUGIN_EXTERNAL_PATH . 'js/ZeroClipboard.js', array('jquery-ui-core') , 1.8, true );
    }
}    
 

add_action('init', 'couponcode_init');
add_action('wp_head', 'couponcode_header');
add_action('admin_menu','couponcode_adminmenu',1);
add_action('activate_'.plugin_basename(__FILE__), 'couponcode_install');
add_action('deactivate_'.plugin_basename(__FILE__), 'couponcode_uninstall');

?>
