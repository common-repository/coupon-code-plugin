<?php 
// Spoof CSS
header("Content-type: text/css");

// cater for stand-alone calls
if (!function_exists('get_option'))
	require_once('../../../wp-config.php');

// retreive settings 
$options = get_option('couponcode');
$custom1 = htmlspecialchars($options['custom1'], ENT_QUOTES);
$custom2 = htmlspecialchars($options['custom2'], ENT_QUOTES);
$custom3 = htmlspecialchars($options['custom3'], ENT_QUOTES);
$customicon = htmlspecialchars($options['customicon'], ENT_QUOTES);

?>

@charset "utf-8";
/* CouponCode CSS

Style: Custom
Last Updated: December 8th, 2010
Author: Weborithm.com

Styling Guide -

Color 1 	- Background color

Color 2 	- Border color
Color 2.1 	- Border 'hover' color { this doesn't seem to work regularly due to the Flash overlay, so if the tests don't work just leave it out }

Color 3		- Link color
Color 3.1	- Link 'hover' color { this doesn't seem to work regularly due to the Flash overlay, so if the tests don't work just leave it out }

*/

span.couponcode {
	background: <?php echo $custom1; ?>; /* Background - Color 1 */
	border: 1px solid <?php echo $custom2; ?>; /* Border - Color 2 */
	
	/* Do Not Edit Below */
	
	margin: 0 5px 0 0;
	padding: 2px 3px 2px 5px;
	position: relative;
}

span.couponcode a {
	background: url(/wp-content/plugins/coupon-code-plugin/icons/<?php echo $customicon; ?>) no-repeat right center; /* Static Background - Do Not Edit */
	color: <?php echo $custom3; ?>; /* Link Color - Color 3 */
	
	/* Do Not Edit Below */
	
	padding: 0 20px 0 0;
	text-decoration: none;
}

/* Tooltip */

span.couponcodehover {
	background: <?php echo $custom1; ?>; /* Background - Color 1 */
	border: 1px solid #d5c335; /* Border - Color 2.1 */
	color: #ad2900; /* Link Hover Color - Color 3.1 */
	
	/* Do Not Edit Below */
	
	font-size: 12px;
	left: 0;
	line-height: 12px;
	padding: 5px;
	position: absolute;
	text-align: center;
	top: -28px;
	width: 148px;
	border-radius: 5px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
}