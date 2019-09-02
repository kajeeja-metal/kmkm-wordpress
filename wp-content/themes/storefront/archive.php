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
	<div id="primary" class="page-template-PageWithoutSidebar">
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
				    $queried_category = get_term( get_query_var('cat'), 'category' );
				    $categories = get_the_category(); //returns categories
					$thiscat = $categories[0];
					
					$parent_id = $thiscat->parent; //the parent id
					$parent = get_category($parent_id); //this returns the parent category as an object

					//use id or slug of category you are searching for
					if( $parent_id == 88 || $thiscat->slug == 'all-scents' ){
						?>
						<div class="group_category_blog box-shadow">
							<div class="list_category_blog">
								<div class="box-nav">
									<a href="https://www.karmakametonline.com/category/fragrance-oil-single" class=" hover_blog">Fragrance Oil Single</a>
									<div class="list_category_blog_dd hover_blog">
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-single/flower">Flower</a></div>
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-single/herb-and-spice">Herb and Spice</a></div>
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-single/wood">Wood</a></div>
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-single/fruit">Fruit</a></div>
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-single/etcetara">Etcetara</a></div>
									</div>
								</div>
								<div class="box-nav">
									<a href="https://www.karmakametonline.com/category/fragrance-oil-blend" class="hover_blog">Fragrance Oil Blend</a>
									<div class="list_category_blog_dd hover_blog">
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-blend/little-india-series">Little India Series</a></div>
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-blend/patir-en-voyage-series">Patir en voyage Series</a></div>
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-blend/potpourri">Potpourri </a></div>
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-blend/special-edition">Special Edition </a></div>
										<div><a href="https://www.karmakametonline.com/category/fragrance-oil-blend/heritage-bazaar">Heritage Bazaar</a></div>
									</div>
								</div>
								<div class="box-nav">
									<a href="https://www.karmakametonline.com/category/essential-oil-single" class="hover_blog">Essential Oil Single</a>
									<div class="list_category_blog_dd hover_blog">
										<div><a href="https://www.karmakametonline.com/category/flower-essential-oil-single">Flower </a></div>
										<div><a href="https://www.karmakametonline.com/category/herb-and-spice-essential-oil-single">Herb and Spice</a></div>
										<div><a href="https://www.karmakametonline.com/category/wood-essential-oil-single">Wood</a></div>
										<div><a href="https://www.karmakametonline.com/category/fruit-essential-oil-single">Fruit</a></div>
										<div><a href="https://www.karmakametonline.com/category/etcetara-essential-oil-single">Etcetara</a></div>
									</div>
								</div>
								<div class="box-nav">
									<a href="https://www.karmakametonline.com/category/essential-oil-blend	" class="hover_blog">Essential Oil Blend</a>
									<div class="list_category_blog_dd hover_blog">
										<div><a href="https://www.karmakametonline.com/category/essential-oil-blend/refreshing">Refreshing </a></div>
										<div><a href="https://www.karmakametonline.com/category/essential-oil-blend/relaxing">Relaxing</a></div>
										<div><a href="https://www.karmakametonline.com/category/essential-oil-blend/romantic">Romantic</a></div>
									</div>
								</div>
								<div class="box-nav">
										<a href="https://www.karmakametonline.com/category/all-scents"  class="">All Scents</a>
								</div>
							</div>
						</div>

					<?php
						}else{
					?>
					<div class="group_category_blog">
						<div class="list_category_blog">
							<div class="box-nav"><a href="https://www.karmakametonline.com/category/all-editorial" class="hover_blog">ALL EDITORIAL</a></div>
							<div class="box-nav"><a href="https://www.karmakametonline.com/category/craftmanship" class="hover_blog">CRAFTMANSHIP</a></div>
							<div class="box-nav"><a href="https://www.karmakametonline.com/category/good-scent-good-life" class="hover_blog">GOOD SCENT, GOOD LIFE.</a></div>
							<div class="box-nav"><a href="https://www.karmakametonline.com/category/karmakamet-tips" class="hover_blog">KARMAKAMET TIPS</a></div>
							<div class="box-nav"><a href="https://www.karmakametonline.com/category/secret-of-scent" class="hover_blog">SECRET OF SCENT</a></div>
						</div>
					</div>
					<?php		
						}
					?>
	    	 

	    	 

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