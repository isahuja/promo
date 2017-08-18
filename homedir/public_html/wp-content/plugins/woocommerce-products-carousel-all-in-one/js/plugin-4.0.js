(function() {    
    tinymce.PluginManager.add('woocommerce_products_carousel_all_in_one_button', function(ed, url) {
            ed.addButton('woocommerce_products_carousel_all_in_one_button', {
                title : 'WooCommerce Products Carousel all in one',
                image : url+'/../images/shortcode-icon.png',
                onclick : function() {
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 750 : width - 50;
                    H = H - 150;                       
                    tb_show('WooCommerce Products Carousel all in one','admin-ajax.php?action=woocommerce_products_carousel_all_in_one_shortcode_generator&width=' + W + '&height=' + H );
               }
            });        
    });
})();