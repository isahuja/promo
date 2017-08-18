<?php

/**
 * Wp in Progress
 * 
 * @package Wordpress
 * @author WPinProgress
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!function_exists('bazaarlite_header_slideshow_function')) {

	function bazaarlite_header_slideshow_function() {
?>

	<div class="slick-wrapper">
    
		<?php 
        
            for ( $i = 1;  $i<= 6; ++$i ) {
				
				if ( !get_theme_mod('wip_slideshow_'.$i.'_default_image') || get_theme_mod('wip_slideshow_'.$i.'_default_image') == 'off' ) :
                
                    echo '<div class="slick-image" style="background-image:url('.esc_url( get_theme_mod('wip_slideshow_'.$i.'_image', get_template_directory_uri().'/assets/images/slideshow/img0'.$i.'.jpg')).')">';
                    echo '<p class="slick-title">'.esc_html(get_theme_mod('wip_slideshow_'.$i.'_title','Welcome to Bazaar')).'</p>';
                    echo '<a class="button" href="'.esc_url( get_theme_mod('wip_slideshow_'.$i.'_url','#')).'">'.esc_html(get_theme_mod('wip_slideshow_'.$i.'_cta','Get Now')).'</a>';
                    echo '</div>';
        		
				endif;
            
            } 
    
        ?>
		
	</div>

<?php
	
	}
	
	add_action( 'bazaarlite_header_slideshow','bazaarlite_header_slideshow_function', 10, 2 );
	
}

?>