<?php
/**
 * Product quantity inputs
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

$defaults = array(
	'max_value'   => apply_filters( 'woocommerce_quantity_input_max', '', $product ),
	'min_value'   => apply_filters( 'woocommerce_quantity_input_min', '', $product ),
	'step'        => apply_filters( 'woocommerce_quantity_input_step', '1', $product ),
);

if ( ! empty( $defaults['min_value'] ) )
	$min = $defaults['min_value'];
else $min = 1;

if ( ! empty( $defaults['max_value'] ) )
	$max = $defaults['max_value'];
else $max = 10;

if ( ! empty( $defaults['step'] ) )
	$step = $defaults['step'];
else $step = 1;

?>
	<select name="<?php echo esc_attr( $input_name ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="qty">
	<?php
	echo '<option value>Quantity</option>';
	for ( $count = $min; $count <= $max; $count = $count+$step ) {
		if (!is_product()){
			if ( $count == $input_value )
				$selected = ' selected';
			else $selected = '';
		}
		echo '<option value="' . $count . '"' . $selected . '>' . $count . '</option>';
	}
	?>
	</select>