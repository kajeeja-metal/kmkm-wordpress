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
				echo($url);
				}
				$imgURL = MultiPostThumbnails::get_post_thumbnail_url(get_post_type(), 'secondary-image', NULL, 'full');
					global $wp;
					$current_slug = $wp->request;
					$splittedstring=explode("/",$current_slug);
					$categories = get_the_category();
					$thiscat = $categories[0];
					
					$parent_id = $thiscat->parent; //the parent id
					$parent = get_category($parent_id); //this returns the parent category as an object

					$terms = get_terms( array('taxonomy' => 'category', 'hide_empty' => false) );
					$args = array (
		                'post_type'         => 'post',
		                'category'           => $terms->slug,
		                'posts_per_page'    => '-1',
		            );
 
            // The Query
					foreach ($terms as $term) {
						if($term->slug == $splittedstring[1]){
							if($term->parent == 88 || $term->slug == 'all-scents'){?>
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
							}
							else
							{
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
						    }
						}
					}
					?>
	    	 


			<h1 class="page-title" style="text-align:center;padding: 80px 0;"><?php esc_html_e( 'Nothing Found', 'storefront' ); ?></h1>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();