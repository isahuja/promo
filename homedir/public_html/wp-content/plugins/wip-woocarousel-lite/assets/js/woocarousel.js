jQuery.noConflict()(function($){
	

/* ===============================================
   Carousel
   ============================================= */

	$('.wip-woocarousel-lite-carousel').each(function(){

		var slick_columns = parseInt($(this).attr('data-columns'));
		var slick_dots_option = $(this).attr('data-dots');
	
		var slick_dots = false;
		var slick_columns_992 = 2 ;

		if ( slick_columns <= 1 ) {
			slick_columns_992 = 1 ;
		}

		if ( slick_dots_option === 'on' ) {
			slick_dots = true ;
		}
		
		$(this).slick({
			
		  dots: slick_dots,
		  infinite: false,
		  speed: 300,
		  adaptiveHeight: true,
		  prevArrow:'<div class="slick-prev" style=""></div>',
		  nextArrow:'<div class="slick-next" style=""></div>',
		  slidesToShow: slick_columns,
		  slidesToScroll: slick_columns,
		  responsive: [
			{
			  breakpoint: 992,
			  settings: {
				slidesToShow: slick_columns_992,
				slidesToScroll: slick_columns_992,
			  }
			},
			{
			  breakpoint: 768,
			  settings: {
				slidesToShow: 1,
				slidesToScroll: 1
			  }
			},

		  ]
		  
		});
		

	}); 
	
});          