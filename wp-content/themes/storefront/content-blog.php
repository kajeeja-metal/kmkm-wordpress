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
	$a = shortcode_atts( array(
        'cpt' => 'post',
        'tax' => 'category',
    ), $atts );

    $queried_category = get_term( get_query_var('cat'), 'category' ); 
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    $args = array(
        'post_type' => 'post',
        'category_name' => $queried_category->slug,
        'posts_per_page' => 9,
        'post_status' => 'publish',
        'paged' => $paged
    );

    $post_query = new WP_Query($args);
    $counter = 1;
    $endpoint = 0;
	while($post_query->have_posts() ) {
	$post_query->the_post();
	if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
		// the_post_thumbnail( 'full' );
		$url = wp_get_attachment_url( get_post_thumbnail_id($post) );
	}?>
	<?php if(($counter%5) == 2){ echo '<div class="box-group-item">';}?>
	  <?php if(($counter%5) == 0){ echo '<div class="box-group-item2">';  }?>
		<div class="box-container">
			<a  class="background-post pull-left box" style="background-image:url(<?php echo $url ?>) " href="<?php echo get_permalink( $post ) ?>">
		    </a>
		    
		    <div class="box-detail pull-right box" style="width: 34%;padding: 49px;background: #fff;">
		    	<h5><?php the_category( ' / ' ); ?></h5>
			    <h2><a href="<?php echo get_permalink( $post ) ?>"><?php the_title(); ?></a></h2>
			    <div style="font-size: 22px;line-height: 1.1;">
			    	<?php echo excerpt(30); ?>
			    </div>
			    <div class="date"><?php echo get_the_date( 'd F Y' ); ?></div>
		    </div>
	    </div>
		<?php if( ($counter%6) == 0 ){ echo '</div>';}?>
		<?php if(($counter%5) == 4){ echo '</div>'; echo '<div style="margin-bottom:30px">'. do_shortcode( '[rev_slider alias="ads"]' ) .'</div>'; }?>

		<?php if( ($counter%9) == 0 ){ $counter = 0 ;}?>
		<?php $counter++ ; ?>	
    <?php } 
	
    ?>
    <!-- Add the pagination functions here. -->
	<div class="nav-previous alignleft"><?php previous_posts_link( 'Older posts' ); ?></div>
	<div class="nav-next alignright"><?php next_posts_link( 'Newer posts' ); ?></div>
</article><!-- #post-## -->

