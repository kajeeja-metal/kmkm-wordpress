<?php /* Template Name: ScentsWithoutSidebar */ ?>
<?php get_header(); ?>
<style type="text/css">
	.col-full{
		padding: 0 !important;
	}
</style>
<div id="primary">
    <main id="main" class="site-main" role="main" style="    margin-top: 0;">
    	 <?php echo do_shortcode( '[rev_slider alias="editoral"]' ); ?>
    	 <?php 
			if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
			// the_post_thumbnail( 'full' );
			$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
			// echo($url);
			}
			$imgURL = MultiPostThumbnails::get_post_thumbnail_url(get_post_type(), 'secondary-image', NULL, 'full');
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
        // Start the loop.
     	while ( have_posts() ) : the_post();
	    // Include the page content template.
	    get_template_part('content-blog-sc', 'page' );
	    // If comments are open or we have at least one comment, load up the comment template.
	    // End of the loop.
     	endwhile;
        ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->
<?php get_footer(); ?>