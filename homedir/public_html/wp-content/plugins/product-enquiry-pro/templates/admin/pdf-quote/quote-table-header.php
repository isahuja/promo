<tr>
<?php
    $classPrefix = '';
if ($show_price == 1) {
    $classPrefix = '-price';
}
    ?>
    <th class="head-product<?php echo $classPrefix; ?>" align="left"><?php _e('Product', QUOTEUP_TEXT_DOMAIN); ?></th>
    <th class="head-sku<?php echo $classPrefix ?>" align="left"> <?php _e('Sku', QUOTEUP_TEXT_DOMAIN); ?> </th>
    <?php if ($show_price == 1) : ?>
        <th class="old_price" align="left"> <?php _e('Old', QUOTEUP_TEXT_DOMAIN); ?> </th>
    <?php endif; ?>
    <th class="new_price" align="left"> <?php _e('New', QUOTEUP_TEXT_DOMAIN); ?> </th>
    <th class="quantitiy" align="center"> <?php _e('Quantity', QUOTEUP_TEXT_DOMAIN); ?> </th>
    <th class="total" align="right"> <?php _e('Amount', QUOTEUP_TEXT_DOMAIN); ?> </th>
</tr>