<?php 

	get_header(); 

	do_action('bazaarlite_header_sidebar', 'header_sidebar_area');

?>

<div class="container">

	<div class="row" id="blog" >
		
        <article class="post-container col-md-12">

			<div class="post-article">

                <h2> <?php esc_html_e( 'Oops, it is a little bit embarassing&hellip;',"bazaar-lite" ) ?> </h2>           
			
				<?php esc_html_e( 'The page that you requested, was not found.','bazaar-lite'); ?> 

                <h2> <?php esc_html_e( 'What can i do?',"bazaar-lite" ) ?> </h2>           

                <p> <a href="<?php echo esc_url(home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name')) ?>"> <?php esc_html_e( 'Back to the homepage','bazaar-lite'); ?> </a> </p>
              
                <p> <?php esc_html_e( 'Check the typed URL','bazaar-lite'); ?> </p>

                <p> <?php esc_html_e( 'Make a search, from the below form:','bazaar-lite'); ?> </p>
                
				<?php get_search_form(); ?>

			</div>

    	</article>
           
    </div>
    
</div>

<?php 
	
	do_action('bazaarlite_bottom_sidebar', 'bottom_sidebar_area' );
	
	get_footer(); 

?>