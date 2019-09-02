<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

session_start();
$_SESSION['id_products'] = null;
get_header(); ?>
<style type="text/css">
	.col-full{
		padding: 0 !important;
	}
</style>
	<div id="primary" class="storefront-full-width-content">
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
				<!-- <h6><?php echo excerpt(20); ?></h6> -->
			</figcaption>
		</figure>
		<div class="col-grid">
			<div class="">
				<div style="    font-size: 20px;"><?php echo get_the_date( 'd F Y' ); ?></div>
				<?php echo the_content();?>
				
			</div>
			<?php
				do_action( 'storefront_single_post_after' );

			endwhile; // End of the loop.
			?>

			<?php get_sidebar(); ?>
		</div>
		<?php
			
			$tags = wp_get_post_tags(get_the_ID());
			$args     = array( 'post_type' => 'product', 'posts_per_page' => -1);
			$products = get_posts( $args );
			$count = count($products);
			$id_products = $_SESSION['id_products'];
			$is = 0;
			for ($i=0; $i < $count; $i++) { 
				$editoral = 'editoral'.$i;
				$is++;
				 if(get_post_meta($products[$i]->ID, 'editoral'.$is , true) == $tags[0]->name && $tags[0]->name != ''){
				 	 $id_products = $id_products . $products[$i]->ID . ",";
				 	 $_SESSION['id_products'] = $id_products;
				 }

				
			}
			if($_SESSION['id_products'] != ''){?>
			<section class="related products" style="    padding: 0 2.617924em;padding-top: 50px;">
			<h2 style="text-align: center;">Relate Products</h2>
				<?php
					echo do_shortcode('[products ids="'.$_SESSION['id_products'].'" columns="5"]');
				?>
			</section>
				<?php
			}
			
		?>
		



		</section>
		</main><!-- #main -->
		
	</div><!-- #primary -->

<?php
get_footer();
