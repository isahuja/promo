<tr>
    <?php
    $classPrefix = '';
    if ($show_price == 1) :
        $classPrefix = '-price';
    endif;
    ?>
    <td class="product<?php echo $classPrefix; ?>" align="left">
        <?php
            echo get_the_title($quoteProduct['product_id']);
        if ($_product->is_type('variable')) :
            quoteupRemoveClassFilter('woocommerce_attribute_label', 'WCML_WC_Strings', 'translated_attribute_label');
            global $sitepress;
            remove_filter('get_term', array($sitepress, 'get_term_adjust_id'), 1);
            echo printVariations($quoteProduct);
        endif;
        ?>
    </td> <!-- td.product ends here -->
    <td class="sku<?php echo $classPrefix ?>" align="left">
        <?php
        if ($_product->is_type('variable')) :
            $_product_variation = wc_get_product($quoteProduct['variation_id']);
            echo $_product_variation->get_sku();
        else :
                    echo $_product->get_sku();
        endif;
        ?>
    </td> <!-- td.sku ends here -->
    <?php if ($show_price == 1) : ?>
        <td align="left" class="old_price">
            <?php echo wc_price($price); ?>
        </td> <!-- td.old_price ends here -->
    <?php endif; ?>
    <td align="left" class="new_price">
        <?php echo wc_price($quoteProduct['newprice']); ?>
    </td> <!-- td.new_price ends here -->
    <td align="center" class="quantitiy">
        <?php echo $quoteProduct['quantity']; ?>
    </td> <!-- td.new_price ends here -->
    <td align="right" class="total">
        <?php echo wc_price($quoteProduct['newprice'] * $quoteProduct['quantity']);
        ?>
    </td> <!-- td.total ends here -->
</tr>