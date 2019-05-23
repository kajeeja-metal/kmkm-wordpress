<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $product;
?>
<?php do_action('woocommerce_before_single_product'); ?>
<?php do_action( 'woocommerce_product_meta_start' ); ?>
<?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as" style="    border: 1px solid #000;color: #000;padding: 2px 10px;font-size: 13px;display: inline-block;margin-bottom: 5px;    letter-spacing: 1px;">' . _n( '', '', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

<?php do_action( 'woocommerce_product_meta_end' ); ?>
<?php
the_title( '<h1 class="product_title entry-title" style="    margin: 5px 0;">', '</h1>' );

?>
	
<p style="margin: 0px;padding-bottom: 0px;    letter-spacing: 1px;" class="size-main"><?php echo get_post_meta($product->get_id(), 'size', true); ?></p>
<div class="product_meta" style="padding-top: 0px;border:none;margin: 0px 0px 5px 0px;    letter-spacing: 1px;">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>

	<?php endif; ?>

</div>
