<?php /* Template Name: PageWithoutSidebar */ ?>
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