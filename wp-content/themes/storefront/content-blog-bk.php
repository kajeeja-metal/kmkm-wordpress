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
	        'posts_per_page' => 999,
	    );

	    $post_query = new WP_Query($args);
	    $counter = 1;
	    $endpoint = 0;
		if($post_query->have_posts() ) {
		  while($post_query->have_posts() ) {
		    $post_query->the_post();
		    if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
				// the_post_thumbnail( 'full' );
				$url = wp_get_attachment_url( get_post_thumbnail_id($post) );
			}
			
		    ?>

		    <?php if(($counter%5) == 2){ echo '<div class="box-group-item">';}?>
	          <?php if(($counter%5) == 0){ echo '<div class="box-group-item2">';  }?>

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

				<?php if( ($counter%6) == 0 ){ echo '</div>'; $counter ;}?>
			<?php if(($counter%5) == 4){ echo '</div>'; }?>


			<?php if( ($counter%6) == 0 ){ $counter = 0 ;}?>
		    <?php $counter++ ; ?>

		


		    	
		    <?php
		  }
		}
	?>
</article><!-- #post-## -->
s