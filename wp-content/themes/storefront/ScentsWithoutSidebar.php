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
					<a href="http://localhost/kmkm-wordpress/category/all-scents" class=" hover_blog">Fragrance Oil Single</a>
					<div class="list_category_blog_dd hover_blog">
						<div><a href="http://localhost/kmkm-wordpress/category/all-scents">Flower </a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/craftmanship">Herb and Spice</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/decoration">Wood</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/karmakamet-tips">Fruit</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/new-and-events">Etcetara</a></div>
					</div>
				</div>
				<div class="box-nav">
					<a href="http://localhost/kmkm-wordpress/category/craftmanship" class="hover_blog">Fragrance Oil Blend</a>
					<div class="list_category_blog_dd hover_blog">
						<div><a href="http://localhost/kmkm-wordpress/category/all-scents">Little India Series</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/craftmanship">Patir en voyage Series</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/decoration">Potpourri </a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/karmakamet-tips">Special Edition </a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/new-and-events">Heritage Bazaar</a></div>
					</div>
				</div>
				<div class="box-nav">
					<a href="http://localhost/kmkm-wordpress/category/decoration" class="hover_blog">Essential Oil Single</a>
					<div class="list_category_blog_dd hover_blog">
						<div><a href="http://localhost/kmkm-wordpress/category/all-scents">Flower </a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/craftmanship">Herb and Spice</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/decoration">Wood</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/karmakamet-tips">Fruit</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/new-and-events">Etcetara</a></div>
					</div>
				</div>
				<div class="box-nav">
					<a href="http://localhost/kmkm-wordpress/category/karmakamet-tips" class="hover_blog">Essential Oil Blend</a>
					<div class="list_category_blog_dd hover_blog">
						<div><a href="http://localhost/kmkm-wordpress/category/all-scents">Refreshing </a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/craftmanship">Relaxing</a></div>
						<div><a href="http://localhost/kmkm-wordpress/category/decoration">Romantic</a></div>
					</div>
				</div>
			</div>
		</div>
        <?php
        
        // Start the loop.
        while ( have_posts() ) : the_post();
                        // Include the page content template.
                        get_template_part( 'content-blog', 'page' );
                        // If comments are open or we have at least one comment, load up the comment template.
                        // End of the loop.
        endwhile;
        ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->
<?php get_footer(); ?>