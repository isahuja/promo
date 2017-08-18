jQuery.noConflict()(function($){
   
    $('.WIP_plugin_panel_box .WIP_plugin_panel_bool-slider .inset .control').live('click',function() {
		
        if (!$(this).parent().parent().hasClass('disabled')) {
          
		    if ($(this).parent().parent().hasClass('on')) {
                $(this).parent().parent().addClass('off').removeClass('on');
                $(this).parent().parent().children('.on-off').val('off');
				$(this).parent().parent().parent().next('.hidden-element').slideUp("slow");
		    } else {
                $(this).parent().parent().addClass('on').removeClass('off');
                $(this).parent().parent().children('.on-off').val('on');
				$(this).parent().parent().parent().next('.hidden-element').slideDown("slow");
        
		    }
       
	    }
   
    });
	
    $('.WIP_plugin_panel_box .WIP_plugin_panel_bool-slider .inset .control').each(function() {
     
	    if (!$(this).parent().parent().hasClass('disabled')) {
     
	        if ($(this).parent().parent().hasClass('on')) {
				$(this).parent().parent().parent().next('.hidden-element').css({'display':'block'});
	        } else {
				$(this).parent().parent().parent().next('.hidden-element').css({'display':'none'});
            }
    
	    }
  
    });

});