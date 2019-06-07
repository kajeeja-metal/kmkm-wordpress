<?php
/**
 * The template for displaying all single posts.
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
		<main id="main" class="site-main" role="main" style="    margin-top: 0px;">

		<?php
		while ( have_posts() ) :
			the_post();

			do_action( 'storefront_single_post_before' );

			
			if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
				// the_post_thumbnail( 'full' );
				$url = wp_get_attachment_url( get_post_thumbnail_id($post) );
			}
			$imgURL = MultiPostThumbnails::get_post_thumbnail_url(get_post_type(), 'secondary-image', NULL, 'full');
		?>
		<figure class="banner-single" style="background-image: url('<?php echo $imgURL ?>')">
			<figcaption class="content-box-images">
				<h2><?php echo get_the_title(); ?></h2>
				<h6><?php echo excerpt(20); ?></h6>
			</figcaption>
		</figure>
		<?php echo the_content();?>

		<?php
			do_action( 'storefront_single_post_after' );

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
