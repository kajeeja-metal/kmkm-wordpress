<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package storefront
 */

get_header(); ?>
<style type="text/css">
	.col-full{
		padding: 0 !important;
	}
</style>
	<div id="primary">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<?php echo do_shortcode( '[rev_slider alias="editoral"]' ); ?>
	    	 <?php 
				if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
				// the_post_thumbnail( 'full' );
				$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
				// echo($url);
				}
				$imgURL = MultiPostThumbnails::get_post_thumbnail_url(get_post_type(), 'secondary-image', NULL, 'full');
	    	 ?>
	    	 <div class="group_category_blog">
				<div class="list_category_blog">
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/all-editorial" class="hover_blog">ALL EDITORIAL</a></div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/craftmanship" class="hover_blog">CRAFTMANSHIP</a></div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/decoration" class="hover_blog">DECORATION</a></div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/karmakamet-tips" class="hover_blog">KARMAKAMET TIPS</a></div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/new-and-events" class="hover_blog">NEW AND EVENTS</a></div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/our-product" class="hover_blog">OUR PRODUCT</a></div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/scent-and-space" class="hover_blog">SCENT AND SPACE</a></div>
				</div>
			</div>

			<?php
			get_template_part( 'content-blog', 'page' );

		else :

			get_template_part( 'content', 'none' );

		endif;
		?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();