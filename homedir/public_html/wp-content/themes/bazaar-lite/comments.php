<?php 

if ( ( have_comments() ) && ( post_password_required() == false ) ) : ?>

	<div class='post-container col-md-12 comments-container'>
    	
        <div class='comments-title'>
        
            <div class='title-container'>
        
                <h3 class="title"><?php esc_html_e('Comments','bazaar-lite') ?></h3>
        
            </div>
		
        </div>
        
		<?php wp_list_comments('type=comment&callback=bazaarlite_comment'); ?>
	
    </div>

<?php

endif; 

function bazaarlite_comment ($comment, $args, $depth) {
    
	$GLOBALS['comment'] = $comment; ?>
 
    <section id="comment-<?php comment_ID(); ?>" <?php comment_class('post-article'); ?> >
    
    	<div class="comment-container">
        
            <div class="comment-avatar">
                <?php echo get_avatar( $comment->comment_author_email, 90 ); ?>
            </div>
             
            <div class="comment-text">
                
                <header class="comment-author">
                    
                    <span class="author"><?php printf( '<cite>' . esc_html__('%s','bazaar-lite') . '</cite>', get_comment_author_link()) ?></span>
                    <time datetime="<?php echo get_comment_date("c")?>" class="comment-date">  
                    <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(esc_html__('%1$s at %2$s','bazaar-lite'), get_comment_date(),  get_comment_time()) ?></a> - 
                    <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
                    <?php edit_comment_link(esc_html__('(Edit)','bazaar-lite')) ?>
                    </time>
                    
                </header>
        
                <?php if ($comment->comment_approved == '0') : ?>
                    <br /><em><?php esc_html_e('Your comment is awaiting approval.','bazaar-lite') ?></em>
                <?php endif; ?>
              
                <?php comment_text() ?>
              
            </div>
            
            <div class="clear"></div>
            
		</div>
        
    </section>

<?php 

	} 
	
	if ( ( post_password_required() == false ) && ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) ) :  
	
?>

    <section class="col-md-12">
    
        <div class="wp-pagenavi">
        
			<div class="left"><?php previous_comments_link(esc_html__('&laquo;','bazaar-lite')) ?></div>
			<div class="right"><?php next_comments_link(esc_html__('&raquo;','bazaar-lite')) ?></div>
            <div class="clear"></div>
        
        </div>
        
    </section>

<?php endif;?>

<div class="clear"></div>

<?php if ( post_password_required() == false ) : ?>

    <section class="col-md-12">
        
        <?php 
		
			comment_form( array ( 
			
				'label_submit' =>  esc_html__('Leave a reply','bazaar-lite') , 
				'title_reply' =>  '<span>' . esc_html__('Write a Reply or Comment','bazaar-lite') . '</span>' ,
				'title_reply_to' =>  '<span>' . esc_html__('Leave a Reply to %s','bazaar-lite') . '</span>',
			
				) 
			); 
		
		?>

    </section>

<?php endif; ?>