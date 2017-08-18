(function() {
    tinymce.create('tinymce.plugins.woocommerce_products_carousel_all_in_one_button', {
        init : function(ed, url) {            
            ed.addButton('woocommerce_products_carousel_all_in_one_button', {
                title : 'WooCommerce Products Carousel all in one',
                image : url+'/../images/shortcode-icon.png',
                onclick : function() {
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                    W = W - 80;
                    H = 300;                       
                    tb_show('WooCommerce Products Carousel all in one','admin-ajax.php?action=woocommerce_products_carousel_all_in_one_shortcode_generator&width=' + W + '&height=' + H );
               }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('woocommerce_products_carousel_all_in_one_button', tinymce.plugins.woocommerce_products_carousel_all_in_one_button);
})();