jQuery(document).ready(function () {

    var triggerUpdateCartAutomatically = false;
    var validated = true;
    var error_val = 0;
    var err_string = '';
    jQuery("body").on('click', '#btnMPESend', function ( e ) {
        err_string = '';
        jQuery(".load-send-quote-ajax").addClass('loading');
        triggerUpdateCartAutomatically = true;
        sendRequestToUpdateCart(false, false);
        var $this = jQuery(this);

        e.preventDefault();
        var path = jQuery('#site_url').val();
        var cust_name = jQuery('#frm_mpe_enquiry').find('#custname').val();
        var email = jQuery('#frm_mpe_enquiry').find('#txtemail').val();
        var subject = jQuery('#frm_mpe_enquiry').find('#txtsubject').val();
        var message = jQuery('#frm_mpe_enquiry').find('#txtmsg').val();
        var phone = jQuery('#frm_mpe_enquiry').find('#txtphone').val();
        var uemail = jQuery('#author_email').val();
        var fields = wdm_data.fields;

        nm_regex = /^[a-zA-Z ]+$/;
        var enquiry_field;

        if ( fields.length > 0 ) {
            jQuery('#wdm-quoteupform-error').css('display', 'none');
            jQuery('.error-list-item').remove();
            jQuery('.wdm-error').removeClass('wdm-error');
            for (i = 0; i < fields.length; i++) {
                var temp = jQuery('#frm_mpe_enquiry').find('#' + fields[i].id).val();

                var required = fields[i].required;
                if ( fields[i].validation != "" ) {
                    var validation = new RegExp(fields[i].validation);
                }

                var message = fields[i].validation_message;
                var flag = 0;
                if ( required == 'yes' ) {

                    if (fields[i].type == "file")
                    {

                        enquiry_field = jQuery('#frm_mpe_enquiry').find('#' + fields[i].id);

                        var attachedFiles = enquiry_field.prop('files');

                        if (attachedFiles.length < 1 || attachedFiles.length > 10) {
                            enquiry_field.addClass('wdm-error');
                            flag = 1;
                            error_val = 1;
                            err_string += '<li class="error-list-item">' + fields[i].required_message + '</li>';
                        } else {
                            flag = 0;
                            enquiry_field.removeClass('wdm-error');
                        }
                    }else if ( fields[i].type == "text" || fields[i].type == "textarea" ) {
                        enquiry_field = jQuery('#frm_mpe_enquiry').find('#' + fields[i].id);
                        if ( temp == "" ) {
                            enquiry_field.addClass('wdm-error');
                            flag = 1;
                            error_val = 1;
                            err_string += '<li class="error-list-item">' + fields[i].required_message + '</li>';
                        } else {
                            flag = 0;
                            enquiry_field.removeClass('wdm-error');
                        }
                    } else if ( fields[i].type == "radio" ) {
                        jQuery('#frm_mpe_enquiry').find("[name=" + fields[i].id + "]").each(function () {

                            var temp1 = jQuery(this);
                            if ( temp1.is(":checked") ) {
                                flag = 1;
                            }
                        });

                        if ( flag == 0 ) {
                            error_val = 1;
                            jQuery('#frm_mpe_enquiry').find('#' + fields[i].id).parent().css("cssText", "background:#FCC !important;");
                            err_string += '<li class="error-list-item">' + fields[i].required_message + '</li>';
                        } else {
                            jQuery('#frm_mpe_enquiry').find('#' + fields[i].id).parent().css("cssText", "background:white !important;");
                        }
                    } //radio

                    else if ( fields[i].type == "checkbox" ) {
                        jQuery('#frm_mpe_enquiry').find("input[name=" + fields[i].id + "\\[\\]]").each(function () {

                            var temp1 = jQuery(this);

                            if ( temp1.is(":checked") ) {
                                flag = 1;
                            }
                        });
                        if ( flag == 0 ) {
                            error_val = 1;
                            jQuery('#frm_mpe_enquiry').find('#' + fields[i].id).parent().css("cssText", "background:#FCC !important;");
                            err_string += '<li class="error-list-item">' + fields[i].required_message + '</li>';
                        } else {
                            jQuery('#frm_mpe_enquiry').find('#' + fields[i].id).parent().css("cssText", "background:white !important;");
                        }
                    } //checkbox
                    else if ( fields[i].type == "select" ) {
                        jQuery('#frm_mpe_enquiry').find("[name=" + fields[i].id + "]").each(function () {
                            var temp1 = jQuery(this);
                            if ( temp1.val() != "#" ) {
                                flag = 1;
                            }
                        });
                        if ( flag == 0 ) {
                            error_val = 1;
                            jQuery('#frm_mpe_enquiry').find('#' + fields[i].id).parent().css("cssText", "background:#FCC !important;");
                            err_string += '<li class="error-list-item">' + fields[i].required_message + '</li>';
                        } else {
                            jQuery('#frm_mpe_enquiry').find('#' + fields[i].id).parent().css("cssText", "background:white !important;");
                        }
                    }
                }//required

                if ( flag == 0 ) {
                    if ( fields[i].validation != "" && temp != "" ) {
                        if ( !validation.test(temp) ) {
                            enquiry_field = jQuery('#frm_mpe_enquiry').find('#' + fields[i].id);
                            enquiry_field.addClass('wdm-error');
                            err_string += '<li class="error-list-item">' + message + '</li>';
                            error_val = 1;
                        }
                    }
                }
            }//for feilds loop
        }//if


        if ( error_val == 0 ) {
            jQuery('#btnMPESend').attr('disabled', 'disabled');
            jQuery('.wdmquoteup-loader').css('display', 'inline-block');
            // jQuery('#frm_mpe_enquiry').find('#error' ).html( '' );
            jQuery('#submit_value').val(1);
            fun_set_cookie();

            if ( jQuery("#contact-cc").is(":checked") ) {
                var wdm_checkbox_val = 'checked';
            } else {
                var wdm_checkbox_val = 0;
            }

            validate_enq = {
                action: 'quoteupValidateNonce',
                security: jQuery('#mpe_ajax_nonce').val(),
            }
            nonce_error = 0;
            jQuery.post(wdm_data.ajax_admin_url, validate_enq, function ( response ) {
                if ( response == '' ) {
                    jQuery(".load-send-quote-ajax").removeClass('loading');
                    jQuery('.wdmquoteup-loader').css('display', 'none');
                    jQuery('#frm_mpe_enquiry').find('#nonce_error').css('display', 'block');
                    nonce_error = 1;
                } else {
                    jQuery('.wdmquoteup-loader').css('display', 'none');
                    $form_data = new FormData();
                    $form_data.append( 'action', 'quoteupSubmitWooEnquiryForm' );
                    $form_data.append( 'security', jQuery('#mpe_ajax_nonce').val());
                    $form_data.append( 'wdmLocale', jQuery('#wdmLocale').val());
                    $form_data.append( 'cc', wdm_checkbox_val);

                    // mydatavar = {
                    //     action: 'quoteupSubmitWooEnquiryForm',
                    //     security: jQuery('#mpe_ajax_nonce').val(),
                    //     cc: wdm_checkbox_val,
                    // };

                    jQuery(".quoteup_registered_parameter").each(function () {
                        // mydatavar[jQuery(this).attr('id')] = jQuery(this).val();
                        $form_data.append( jQuery(this).attr('id'), jQuery(this).val());
                    });
                    if ( fields.length > 0 ) {
                        for (i = 0; i < fields.length; i++) {
                            if ( fields[i].type == 'text' || fields[i].type == 'textarea' || fields[i].type == 'select' ) {
                                $form_data.append(fields[i].id, jQuery('#frm_mpe_enquiry').find("#" + fields[i].id).val());
                                // mydatavar[fields[i].id] = jQuery('#frm_mpe_enquiry').find("#" + fields[i].id).val();
                            } else if ( fields[i].type == 'radio' ) {
                                $form_data.append(fields[i].id, jQuery('#frm_mpe_enquiry').find("[name='" + fields[i].id + "']:checked").val());
                                // mydatavar[fields[i].id] = jQuery('#frm_mpe_enquiry').find("[name='" + fields[i].id + "']:checked").val();
                            } else if ( fields[i].type == 'checkbox' ) {
                                var selected = "";
                                jQuery('#frm_mpe_enquiry').find("[name='" + fields[i].id + "[]']:checked").each(function () {
                                    if ( selected == "" ) {
                                        selected = jQuery(this).val();
                                    } else {
                                        selected += "," + jQuery(this).val();
                                    }
                                });

                                $form_data.append(fields[i].id, selected);
                                // mydatavar[fields[i].id] = selected;
                            } else if ( fields[i].type == 'multiple' ) {
                                var selected = "";
                                selected = jQuery('#frm_mpe_enquiry').find("#" + fields[i].id).multipleSelect('getSelects').join(',');

                                $form_data.append(fields[i].id, selected);
                                // mydatavar[fields[i].id] = selected;
                            } else if(fields[i].type == 'file')
                            {
                                var attachedFiles = jQuery('#frm_mpe_enquiry').find('#wdmFileUpload').prop('files');
                                if(attachedFiles && attachedFiles.length > 0) {
                                    jQuery(attachedFiles).each(function(index, value){
                                        $file = value;
                                        $file_size = $file.size;
                                        $form_data.append( index, $file );
                        // $form_data.append( 'media_file_name', $file.name );
                    });
                                }
                            }
                        }
                    }

                    if(jQuery('.g-recaptcha').length > 0){
                        $captcha = grecaptcha.getResponse();
                        if($captcha != '')
                        {
                            $form_data.append('captcha', $captcha);
                        }                        
                    }

                    jQuery('#wdm-cart-count').hide();
                // $this.parent().parent('form').siblings('#success_'+id_array[1]).show();
                

                jQuery.ajax( {
                    type: 'POST',
                    url: wdm_data.ajax_admin_url,
                    data: $form_data,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    /*async: false,*/
                    cache: false,
                    success: function ( response ) {
                        if ( response.status == 'COMPLETED' ) {
                            jQuery('.quoteup-quote-cart').slideUp();
                            jQuery(".load-send-quote-ajax").removeClass('loading');
                            setTimeout(function(){ 
                                jQuery('.success').slideDown();
                                jQuery('html, body').animate({ scrollTop: jQuery("#success").offset().top - 700 }, 0);
                            }, 150);
                            if ( wdm_data.redirect != 'n' ) {
                                window.location = wdm_data.redirect;
                            }
                            // }
                        }else {
                            if(response.status == 'failed')
                            {
                                jQuery(".load-send-quote-ajax").removeClass('loading');
                                jQuery('#btnMPESend').attr('disabled', false);
                                jQuery('#wdm-quoteupform-error').css('display', 'block');
                                jQuery('#wdm-quoteupform-error > .form-errors > ul.error-list').html(response.message);
                            // error_field.find('ul.error-list').html(response.message);
                            return false;
                        }
                    }
                }
            } );


                // jQuery.post(wdm_data.ajax_admin_url, mydatavar, function ( response ) {
                //     if ( response == 'COMPLETED' ) {
                //         jQuery('.quoteup-quote-cart').slideUp();
                //         setTimeout(function(){ 
                //             jQuery('.success').slideDown();
                //             jQuery('html, body').animate({ scrollTop: jQuery("#success").offset().top - 700 }, 0);
                //         }, 150);
                //         if ( wdm_data.redirect != 'n' ) {
                //             window.location = wdm_data.redirect;
                //         }
                //     }
                // });
            }


        });
} else {
    jQuery(".load-send-quote-ajax").removeClass('loading');
    jQuery('#wdm-quoteupform-error').css('display', 'block');
    jQuery('#wdm-quoteupform-error > .form-errors > ul.error-list').html(err_string);
    return false;
}

return false;
});

//Code to adjust view of enquiry button in mobile view.
lastRowClass = jQuery('.generated_for_mobile tr:last td').attr('class')
if (lastRowClass == "td-btn-update") {
    jQuery('.generated_for_mobile tr:last th').remove();
}
//End of enquiry button code

// Code to adjust view of total row when in mobile view.
secondLastRowClass = jQuery('.generated_for_mobile tr:last').prev().find('td').attr('class')
if (secondLastRowClass == "final-total") {
    jQuery('.generated_for_mobile tr:last').prev().find('th').html(jQuery('.generated_for_mobile tr:last').prev().find('td').html());
    jQuery('.generated_for_mobile tr:last').prev().find('td').html(jQuery('.generated_for_mobile tr:last td').html());
    jQuery('.generated_for_mobile tr:last').remove();
}
// End of total row code

jQuery('.wdm-modal_textarea').on('input', function () {
    var text_max = 500;
    var text_length = jQuery(this).val().length;
    var text_remaining = text_max - text_length;

    jQuery(this).next('#lbl-char').find('.wdmRemainingCount').html(text_remaining);
    if (text_remaining<50) {
        jQuery(this).next('#lbl-char').css('color','red');
    } else {
        jQuery(this).next('#lbl-char').css('color','#43454b');
    }
});


/**
 * Remove row from Enquiry cart when 'Remove' button is clicked
 */
 jQuery('.remove').on('click', function ( e ) {
  $enquiryCartTable = jQuery(this).closest('table');
  e.preventDefault();
          //Check if mobile table or desktop table
          if (isMobileTable($enquiryCartTable)) {
            $cellLocation = findDesktopTableCellLocation(jQuery(this).closest('tr').index(), 6);
            $desktopTableCellElement = selectCell('.generated_for_desktop.wdm-quote-cart-table', $cellLocation[0], $cellLocation[1]);
            mobileTableEnquiryCartRemoveRow(jQuery(this).closest('tr'));
            desktopTableEnquiryCartRemoveRow($desktopTableCellElement);
        } else {
         $cellLocation = findMobileTableCellLocation(jQuery(this).closest('tr').index(), 0, 6);
         $mobileTableCellElement = selectCell('.generated_for_mobile.wdm-quote-cart-table', $cellLocation[0], $cellLocation[1]);

         desktopTableEnquiryCartRemoveRow(jQuery(this).closest('tr'));
         mobileTableEnquiryCartRemoveRow($mobileTableCellElement);
     }

     product_id = jQuery(this).attr('data-product_id');
     product_var_id = jQuery(this).attr('data-variation_id');
     product_variation_details = jQuery(this).attr('data-variation');


     jQuery.ajax({
        url: wdm_data.ajax_admin_url,
        type: 'post',
        data:
        {
            action: 'wdm_update_enq_cart_session',
            'product_id': product_id,
            'product_var_id': product_var_id,
            'quantity': 0,
            'variation' : JSON.parse(product_variation_details),
            'clickcheck': 'remove'
        },
        success: function ( response ) {
            count = jQuery('.wdm-quoteupicon-count').text();
            count = parseInt(count) - 1;
            jQuery('.wdm-quoteupicon-count').text(count);
        },
        error: function ( error ) {
            console.log(error);
        }

    });
 });

 jQuery('.wdm-prod-quant,.wdm-remark').change(function () {
    $enquiryCartTable = jQuery(this).closest('table');
    $currentElementValue = jQuery(this).val();
    $currentElementClass = jQuery(this).attr('class').split(' ')[0];
        //Check if mobile table or desktop table
        if (isMobileTable($enquiryCartTable)) {
            $cellLocation = findDesktopTableCellLocation(jQuery(this).closest('tr').index(), 6);
            $desktopTableCellElement = selectCell('.generated_for_desktop.wdm-quote-cart-table', $cellLocation[0], $cellLocation[1]);
            jQuery($desktopTableCellElement).find('.'+$currentElementClass).val($currentElementValue);
        } else {
            $cellLocation = findMobileTableCellLocation(jQuery(this).closest('tr').index(), jQuery(this).closest('td').index(), 6);
            $mobileTableCellElement = selectCell('.generated_for_mobile.wdm-quote-cart-table', $cellLocation[0], $cellLocation[1]);
            jQuery($mobileTableCellElement).find('.'+$currentElementClass).val($currentElementValue);
        }
    })

 jQuery('.wdm-update').click(function () {
    sendRequestToUpdateCart(true, true);
});

/**
 * Returns true if Table being processed is mobile table. Else returns false
 */
 function isMobileTable($tableElement)
 {
  return $tableElement.hasClass('generated_for_mobile');
}


function mobileTableEnquiryCartRemoveRow($rowElement)
{
  $rowClassName = $rowElement.closest('tr').attr('class');
  var $count = 0;
  while ($rowElement.closest('tr').next().attr('class') == $rowClassName && $count < 5) {
    $rowElement.closest('tr').next().remove();
    $count++;
}
$rowElement.closest('tr').remove();
}

function desktopTableEnquiryCartRemoveRow($rowElement)
{
          //remove the row.
          $rowElement.closest('tr').remove();

          if ( jQuery('.generated_for_desktop .cart_item').length == 1 ) {
            jQuery('.wdm-quote-cart-table').append("<tr> <td colspan='6 class='no-product'>"+ wdm_data.empty_cart_remove +"</td></tr>");
            jQuery('.td-btn-update').remove();
            jQuery('.wdm-enquiry-form').remove();
        }

    }


    function sendRequestToUpdateCart( showUpdateCartImage, showAlertAfterUpdateCart )
    {
      document.getElementById("error-quote-cart").innerHTML = "";
      validated = true;
      error_val = 0;
      jQuery('.td-btn-update').find('.load-ajax').removeClass('updated');
      jQuery('.cart_product').each(function () {
        thiss = jQuery(this);
        thiss.find('.wdm-prod-quant').css('border-color', '#515151');
    })
      jQuery('.cart_product').each(function () {
       thiss = jQuery(this);
       quant = thiss.find('.wdm-prod-quant').val();
       if ( quant < 1 ) {
        thiss.find('.wdm-prod-quant').css('border-color', 'red');
        validated = false;
        err_string = '<li class="error-list-item">' + wdm_data.cart_not_updated + '</li>';
        error_val = 1;
    }

    if ( Number(quant) % 1 !== 0 ) {
        thiss.find('.wdm-prod-quant').css('border-color', 'red');
        validated = false;
        err_string = '<li class="error-list-item">' + wdm_data.cart_not_updated + '</li>';
        error_val = 1;
    }

})

      if ( validated ) {
        if ( jQuery('.cart_item').length > 1 ) {
            if ( showUpdateCartImage ) {
                jQuery('.td-btn-update').find('.load-ajax').removeClass('updated').addClass('loading');
            }

            jQuery('.cart_item').each(function () {
               thiss = jQuery(this);
               prod_id = thiss.find('.wdm-prod-quant').attr('data-product_id');
               product_var_id = thiss.find('.wdm-prod-quant').attr('data-variation_id');
               product_variation_details = thiss.find('.wdm-prod-quant').attr('data-variation');
               if ( !isNaN(prod_id) ) {
                quant = thiss.find('.wdm-prod-quant').val();
                remark = thiss.find('.wdm-remark').val();
                jQuery.ajax({
                    url: wdm_data.ajax_admin_url,
                    type: 'post',
                    dataType: "JSON",
                    data:
                    {
                        action: 'wdm_update_enq_cart_session',
                        'product_id': prod_id,
                        'product_var_id': product_var_id,
                        'quantity': quant,
                        'remark': remark,
                        'variation' : JSON.parse(product_variation_details),

                    },
                    success: function ( response ) {
                        if ( showUpdateCartImage ) {
                            jQuery('.td-btn-update').find('.load-ajax').removeClass('loading').addClass('updated');
                        }

                        if ( response.variation_id == undefined ) {
                            jQuery('input[data-product_id="' + response.product_id + '"]').closest('tr').find('.product-price').html(response.price);
                        } else {
                            $fields = jQuery('input[data-variation_id="' + response.variation_id + '"]');
                            $fields.each(function () {
                                if (jQuery(this).attr('data-variation') == JSON.stringify(response.variation_detail)) {
                                    jQuery(this).closest('tr').find('.product-price').html(response.price);
                                    jQuery(this).closest('.generated_for_mobile tr').prev().find('td.product-price').html(response.price)
                                }

                            });
                        }

                        jQuery('.sold_individually').val(1);

                    },
                    error: function ( error ) {
                        console.log(error);
                    }
                });
            }
        });
        } else {
            jQuery('.wdm-quote-cart-table').append("<tr> <td colspan='6> No Products available in Quote Cart </td></tr>");
        }
    } else {
        document.getElementById("error-quote-cart").innerHTML = wdm_data.cart_not_updated;
    }
}

});


function fun_set_cookie()
{
    var cname = document.getElementById('custname').value;
    var cemail = document.getElementById('txtemail').value;
    if ( cname != '' && cemail != '' ) {
        var d = new Date();
        d.setTime(d.getTime() + ( 90 * 24 * 60 * 60 * 1000 ));
        var expires = "expires=" + d.toGMTString();
        document.cookie = "wdmusername=" + cname + "; expires=" + expires + "; path=/";
        document.cookie = "wdmuseremail=" + cemail + "; expires=" + expires + ";path=/";
    }

}