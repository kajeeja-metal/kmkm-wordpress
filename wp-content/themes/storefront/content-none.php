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
					<div class="box-nav">
						<a class="hover_blog" href="http://localhost/kmkm-wordpress/category/all-scents">Fragrance Oil Single</a>
						<div class="list_category_blog_dd">
							<div><a href="http://localhost/kmkm-wordpress/category/all-scents">Flower </a></div>
							<div><a href="http://localhost/kmkm-wordpress/category/craftmanship">Herb and Spice</a></div>
							<div><a href="http://localhost/kmkm-wordpress/category/decoration">Wood</a></div>
							<div><a href="http://localhost/kmkm-wordpress/category/karmakamet-tips">Fruit</a></div>
							<div><a href="http://localhost/kmkm-wordpress/category/new-and-events">Etcetara</a></div>
						</div>
					</div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/craftmanship" class="hover_blog">Fragrance Oil Blend</a></div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/decoration" class="hover_blog">Essential Oil Single</a></div>
					<div class="box-nav"><a href="http://localhost/kmkm-wordpress/category/karmakamet-tips" class="hover_blog">Essential Oil Blend</a></div>
				</div>
			</div>
			<h1 class="page-title" style="text-align:center;padding: 80px 0;"><?php esc_html_e( 'Nothing Found', 'storefront' ); ?></h1>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();