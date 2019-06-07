<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package storefront
 */

?>

<article id="post-<?php the_ID(); ?>" style="padding: 3em 6em;
    background: #f3f3f3;" class="box-group">
	<?php
	    $args = array(
	        'post_type' => 'post',
	        'category_name' => 'all-editorial',
	    );

	    $post_query = new WP_Query($args);
	    $i = 1;
		if($post_query->have_posts() ) {
		  while($post_query->have_posts() ) {

		    $post_query->the_post();
		    $i++;
		    if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
				// the_post_thumbnail( 'full' );
				$url = wp_get_attachment_url( get_post_thumbnail_id($post) );
			}
		    ?>
		    <?php if($i == 3){ echo '<div class="box-group-item">';} ?>
		    	<div class="box-container">
				    <div class="background-post pull-left box" style="background-image:url(<?php echo $url ?>) "> 
				    </div>
				    <div class="box-detail pull-right box" style="width: 30%;
						    padding: 49px;
						    background: #fff;
						    border: 1px solid #cecece;">
				    	<h5><?php the_category( ' / ' ); ?></h5>
					    <h2><a href="<?php echo get_permalink( $post ) ?>"><?php the_title(); ?></a></h2>
					    <div>
					    	<?php echo excerpt(30); ?>
					    </div>
				    </div>
			    </div>
			<?php if($i == 5){ echo '</div>'; }?>
		    	
		    <?php
		  }
		}
	?>
</article><!-- #post-## -->
