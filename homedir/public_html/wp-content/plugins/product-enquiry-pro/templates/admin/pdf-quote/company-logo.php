<div class="PDFLogo">
    <?php
    do_action('quoteup_before_pdf_logo', $pdfSetting);
    if (isset($pdfSetting[ 'company_logo' ]) && $pdfSetting[ 'company_logo' ] != '') {
        ?>
        <img class="quote-logo"  src='<?php echo $pdfSetting[ 'company_logo' ];
        ?>' height="150px" width="150px">
    <?php
    }
    do_action('quoteup_after_pdf_logo', $pdfSetting);
    ?>
</div>