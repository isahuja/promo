var globalTaskComplete = false;
jQuery(document).ready(function ( $ ) {
      //This is to allow metabox to collapse
      postboxes.add_postbox_toggles(pagenow);

      jQuery('.req').each(function(){
        $innerDiv = jQuery(this).closest('.mpe-left').next('.mpe-right').find('.mpe-right-inner');
        if(!$innerDiv.find('input#custname').length>0 && !$innerDiv.find('input#txtemail').length>0)
        {
            jQuery(this).remove();
        }
    })

      jQuery('#txtmsg, #txtsubject').closest('.mpe_form_input').css('display','none');
      $(window).on('beforeunload', function(){
        if( globalTaskComplete == false) {
            if(jQuery(".admin-quote-table tbody tr").length>0 && jQuery(".admin-quote-table tbody tr.quoteup-no-product").length == 0 ){
                return 'Are you sure you want to leave?';
            }
        }
    });
      jQuery(document).on( 'input', 'input, select, .wdm-checkbox', function(){
        quotationUpdated();
    });
      jQuery(document).on( 'change', 'input, select, .wdm-checkbox', function(){
        quotationUpdated();
    });

      function quotationUpdated()
      {
        if(globalTaskComplete == false) {
            return;
        }
        if(jQuery(this).hasClass('wc-product-search') || jQuery(this).hasClass('select2-input')) {
            return;
        }
        globalTaskComplete = false;
        jQuery('#btnQuoteSave').val(wdm_data.update_quotation_text);
        jQuery('#btnQuoteSend').css('display', 'none');
    }

    jQuery(document).on('change', '.variation_id', function(){
        if(globalTaskComplete == false) {
            return;
        }
        globalTaskComplete = false;
        jQuery('#btnQuoteSave').val(wdm_data.update_quotation_text);
        jQuery('#btnQuoteSend').css('display', 'none');
    })

    jQuery(document).on('click', 'a.remove', function(){
        jQuery('#btnQuoteSend').css('display', 'none');
    })

    var globalEnquiryID = 0;
    jQuery('.wdm-modal_textarea').on('input', function () {
       var text_max = 500;
       var text_length = jQuery(this).val().length;
       var text_remaining = text_max - text_length;

       jQuery(this).next('#lbl-char').find('.wdmRemainingCount').html(text_remaining + ' characters remaining');
       if (text_remaining<50) {
           jQuery(this).next('#lbl-char').css('color','red');
       } else {
           jQuery(this).next('#lbl-char').css('color','#43454b');
       }
   });


    var validated = true;
    var error_val = 0;
    var err_string = '';
    jQuery("body").on('click', '#btnQuoteSave', function ( e ) {
        jQuery('#wdm-quoteupform-error').css('display', 'none')
        jQuery('#wdm-quoteupform-error > .form-errors > ul.error-list').html();
        jQuery("#text").css("visibility", "hidden");
        jQuery("#text").css("display", "none");
        
        if(qtyValidated != true){
            jQuery(".load-send-quote-ajax").removeClass('loading');
            jQuery('#wdm-quoteupform-error').css('display', 'block');
            jQuery('#wdm-quoteupform-error > .form-errors > ul.error-list').html(wdm_data.quantity_not_valid);
            return false;
        }
        if(priceValidated != true){
            jQuery(".load-send-quote-ajax").removeClass('loading');
            jQuery('#wdm-quoteupform-error').css('display', 'block');
            jQuery('#wdm-quoteupform-error > .form-errors > ul.error-list').html(wdm_data.price_not_valid);
            return false;
        }
        err_string = '';
        error_val = 0;
        var $this = jQuery(this);

        e.preventDefault();
        var path = jQuery('#site_url').val();
        var cust_name = jQuery('#frm_dashboaard_quote').find('#custname').val();
        var email = jQuery('#frm_dashboaard_quote').find('#txtemail').val();
        var subject = jQuery('#frm_dashboaard_quote').find('#txtsubject').val();
        var message = jQuery('#frm_dashboaard_quote').find('#txtmsg').val();
        var phone = jQuery('#frm_dashboaard_quote').find('#txtphone').val();
        var uemail = jQuery('#author_email').val();
        var fields = wdm_data.fields;

        nm_regex = /^[a-zA-Z ]+$/;
        var enquiry_field;

        if ( fields.length > 0 ) {
            jQuery('#wdm-quoteupform-error').css('display', 'none');
            jQuery('.error-list-item').remove();
            jQuery('.wdm-error').removeClass('wdm-error');
            for (i = 0; i < fields.length; i++) {
                var temp = jQuery('#frm_dashboaard_quote').find('#' + fields[i].id).val();

                var required = fields[i].required;
                if ( fields[i].validation != "" ) {
                    var validation = new RegExp(fields[i].validation);
                }

                var message = fields[i].validation_message;
                var flag = 0;
                if ( required == 'yes' && (fields[i].id == 'custname' || fields[i].id == 'txtemail')) {
                    if ( fields[i].type == "text" || fields[i].type == "textarea" ) {
                        enquiry_field = jQuery('#frm_dashboaard_quote').find('#' + fields[i].id);
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
                        jQuery('#frm_dashboaard_quote').find("[name=" + fields[i].id + "]").each(function () {

                            var temp1 = jQuery(this);
                            if ( temp1.is(":checked") ) {
                                flag = 1;
                            }
                        });

                        if ( flag == 0 ) {
                            error_val = 1;
                            jQuery('#frm_dashboaard_quote').find('#' + fields[i].id).parent().css("cssText", "background:#FCC !important;");
                            err_string += '<li class="wdmquoteup-err-display">' + fields[i].required_message + '</li>';
                        } else {
                            jQuery('#frm_dashboaard_quote').find('#' + fields[i].id).parent().css("cssText", "background:white !important;");
                        }
                    } //radio

                    else if ( fields[i].type == "checkbox" ) {
                        jQuery('#frm_dashboaard_quote').find("input[name=" + fields[i].id + "\\[\\]]").each(function () {

                            var temp1 = jQuery(this);

                            if ( temp1.is(":checked") ) {
                                flag = 1;
                            }
                        });
                        if ( flag == 0 ) {
                            error_val = 1;
                            jQuery('#frm_dashboaard_quote').find('#' + fields[i].id).parent().css("cssText", "background:#FCC !important;");
                            err_string += '<li class="error-list-item">' + fields[i].required_message + '</li>';
                        } else {
                            jQuery('#frm_dashboaard_quote').find('#' + fields[i].id).parent().css("cssText", "background:white !important;");
                        }
                    } //checkbox
                    else if ( fields[i].type == "select" ) {
                        jQuery('#frm_dashboaard_quote').find("[name=" + fields[i].id + "]").each(function () {
                            var temp1 = jQuery(this);
                            if ( temp1.val() != "#" ) {
                                flag = 1;
                            }
                        });
                        if ( flag == 0 ) {
                            error_val = 1;
                            jQuery('#frm_dashboaard_quote').find('#' + fields[i].id).parent().css("cssText", "background:#FCC !important;");
                            err_string += '<li class="wdmquoteup-err-display">' + fields[i].required_message + '</li>';
                        } else {
                            jQuery('#frm_dashboaard_quote').find('#' + fields[i].id).parent().css("cssText", "background:white !important;");
                        }
                    }
                }//required

                if ( flag == 0 ) {
                    if ( fields[i].validation != "" && temp != "" ) {
                        if ( !validation.test(temp) ) {
                            enquiry_field = jQuery('#frm_dashboaard_quote').find('#' + fields[i].id);
                            enquiry_field.addClass('wdm-error');
                            err_string += '<li class="error-list-item">' + message + '</li>';
                            error_val = 1;
                        }
                    }
                }
            }//for feilds loop
        }//if

        if ( error_val == 0 ) {
            jQuery('.quote-save, .quote-send').attr('disabled', 'disabled');
            jQuery('#submit_value').val(1);
            nonce_error = 0;
            jQuery('.wdmquoteup-loader').css('display', 'none');
    //get quote products data
    var quoteProductsData = {};
    var count = 0;
    var variationDetailsDuplicate = [ ];
    var variationIdDuplicate = [ ];
    var allSuccess = true;
    jQuery('.admin-quote-table tbody tr.quotetbl-content-row').each(function(){
        if(jQuery(this).hasClass('quoteup-no-product')) {
            return;
        }
        var rowNumber = jQuery(this).find('.remove').attr('data-row-num');
        productID = jQuery(this).find('.remove').data('product_id');
        // variationID = jQuery(this).find('.remove').data('variation_id');
        variationDetails = jQuery(this).find('.remove').data('variation');

        // productTitle = jQuery(this).closest('.quote-product-title').html();
        salePrice = jQuery(this).find('#content-sale-price-'+rowNumber).val();
        productPrice = jQuery(this).find('.wdm-prod-price').val();
        productQty = jQuery(this).find('.quote-newqty').val();
        variationID = "";
        if(jQuery('#variation-id-' + rowNumber).length>0){
            variationID = jQuery('#variation-id-' + rowNumber).val();
            if ( !variationID) {
              var message = wdm_data.invalid_variation + " " + jQuery('#product-title-' + rowNumber).text();
              jQuery("#text").css("visibility", "visible");
              jQuery("#text").css("display", "block");
          // var message = quote_data.invalid_variation + " " + jQuery('#product-title-' + rowNumber).text();
          document.getElementById("text").innerHTML = message;
          jQuery(".load-send-quote-ajax").removeClass('loading');
          jQuery('.quote-save, .quote-send').attr('disabled', false);

          allSuccess = false;
          return false;
      }
  }
  var variationArray = [ ];
  jQuery('#variation-' + rowNumber + '  select[name^=attribute_]').each(function ( ind, obj ) {
   name = jQuery(this).attr('name');
         name = name.substring(10); //length of attrbute_ string is 10
         variation = name + " : " + jQuery(this).val();
         variationArray.push(variation);

         variationDetails = variationArray;

     });

  variationDetailsDuplicate[rowNumber] = variationDetails;
  variationIdDuplicate[rowNumber] = variationID
  if ( show_price == "1" ) {
     show_price = 'yes'
 } else {
     show_price = 'no'
 }
 singleProductData = {
    'productID' : productID,
    'variationID' : variationID,
    'variationDetails' : variationDetails,
    'productPrice' : productPrice,
    'salePrice' : salePrice,
    'productQty' : productQty,
}
quoteProductsData[count] = singleProductData;
count = parseInt(count) + 1;
});
    if(!allSuccess) {
        allSuccess = true;
        return;
    }
    if(jQuery.isEmptyObject(quoteProductsData)) {
        jQuery("#text").css("visibility", "visible");
        jQuery("#text").css("display", "block");
        document.getElementById("text").innerHTML = wdm_data.no_products_in_quotation;
        jQuery(".load-send-quote-ajax").removeClass('loading');
        jQuery('.quote-save, .quote-send').attr('disabled', false);
        return false;
    }

    var variationLength = variationDetailsDuplicate.length;
    for (i = 0; i < variationLength; i++) {
      for (j = i + 1; j < variationLength; j++) {
        if ( variationIdDuplicate[i] != 0 && parseInt(variationIdDuplicate[i]) == parseInt(variationIdDuplicate[j]) ) {
          if ( variationDetailsDuplicate[i].compare(variationDetailsDuplicate[j]) ) {
            jQuery("#text").css("visibility", "visible");
            jQuery("#text").css("display", "block");
            document.getElementById("text").innerHTML = wdm_data.same_variation;
            jQuery(".load-send-quote-ajax").removeClass('loading');
            jQuery('.quote-save, .quote-send').attr('disabled', false);
            return false;
        }
    }
}
}


var language = jQuery('.quoteup-pdf-language').val();
var show_price = jQuery('input[name="show_price"]:checked').val();
if ( show_price == "1" ) {
   show_price = 'yes'
} else {
   show_price = 'no'
}
var security = jQuery('#quote_ajax_nonce').val();
var expiration_date = jQuery('.expiration_date_hidden').val();
$form_data = new FormData();
$form_data.append( 'action', 'save_dashboard-quote' );
$form_data.append( 'quoteProductsData', JSON.stringify(quoteProductsData));
$form_data.append( 'txtemail', email );
$form_data.append( 'custname', cust_name);
$form_data.append( 'language', language );
$form_data.append( 'show-price', show_price);
$form_data.append( 'expiration-date', expiration_date);
$form_data.append( 'security', security);
$form_data.append( 'globalEnquiryID', globalEnquiryID);
$form_data.append( 'wdmLocale', jQuery('#wdmLocale').val());

jQuery(".quoteup_registered_parameter").each(function () {
    $form_data.append( jQuery(this).attr('id'), jQuery(this).val());
});
if ( fields.length > 0 ) {
    for (i = 0; i < fields.length; i++) {
        if ( fields[i].type == 'text' || fields[i].type == 'textarea' || fields[i].type == 'select' ) {
            $form_data.append( fields[i].id, jQuery('#frm_dashboaard_quote').find("#" + fields[i].id).val());
        } else if ( fields[i].type == 'radio' ) {
            $form_data.append( fields[i].id, jQuery('#frm_dashboaard_quote').find("[name='" + fields[i].id + "']:checked").val());
        } else if ( fields[i].type == 'checkbox' ) {
            var selected = "";
            jQuery('#frm_dashboaard_quote').find("[name='" + fields[i].id + "[]']:checked").each(function () {
                if ( selected == "" ) {
                    selected = jQuery(this).val();
                } else {
                    selected += "," + jQuery(this).val();
                }
            });

            $form_data.append( fields[i].id, selected);
        } else if ( fields[i].type == 'multiple' ) {
            var selected = "";
            selected = jQuery('#frm_dashboaard_quote').find("#" + fields[i].id).multipleSelect('getSelects').join(',');
            $form_data.append( fields[i].id, selected);
        } else if(fields[i].type == 'file')
        {
            var attachedFiles = jQuery('#frm_dashboaard_quote').find('#wdmFileUpload').prop('files');
            if(attachedFiles && attachedFiles.length > 0) {
                jQuery(attachedFiles).each(function(index, value){
                    $file = value;
                    $file_size = $file.size;
                    $form_data.append( index, $file );
                });
            }
        }
    }
}

jQuery.ajax( {
    type: 'POST',
    url: wdm_data.ajax_url,
    data: $form_data,
    contentType: false,
    processData: false,
    dataType: 'json',
    cache: false,
    beforeSend: function(){
        jQuery(".load-send-quote-ajax").addClass('loading');
    },
    success: function ( response ) {
        console.log("HERE");
        jQuery(".load-send-quote-ajax").removeClass('loading');
        jQuery('.quote-save, .quote-send').attr('disabled', false);
        jQuery("#text").css("visibility", "visible");
        jQuery("#text").css("display", "block");
        enquiry_id = response.enquiry_id;
        saveString = response.saveString;
        console.log(response);
        if ( saveString == 'Saved Successfully.' ) {
            globalEnquiryID = enquiry_id;
            document.getElementById("text").innerHTML = "Quotation Saved";
            jQuery('#btnQuoteSave').val(wdm_data.update_quotation_text);
            jQuery('#btnQuoteSend').css('display','block');
            if(parseInt(wdm_data.PDF) === 1) {
                handleResponseAfterPDFGeneration( enquiry_id )
            }
            globalTaskComplete = true;

        }else{
            document.getElementById("text").innerHTML = response;
        }
    },
    complete: function(){
        jQuery(".load-send-quote-ajax").removeClass('loading');
    },
    error: function(response){
        console.log("ERROR");
        console.log(response.responseText);
        jQuery("#text").css("visibility", "visible");
        jQuery("#text").css("display", "block");
        document.getElementById("text").innerHTML = response.responseText;
        jQuery('.quote-save, .quote-send').attr('disabled', false);
    }
} );


} else {
    jQuery(".load-send-quote-ajax").removeClass('loading');
    jQuery('#wdm-quoteupform-error').css('display', 'block');
    jQuery('#wdm-quoteupform-error > .form-errors > ul.error-list').html(err_string);
    return false;
}

return false;
});

// This function displays the PDF in modal
function handleResponseAfterPDFGeneration( enquiry_id )
{
    var file = 'http://docs.google.com/gview?embedded=true&url='+quote_data.path + enquiry_id + ".pdf?reload=" + Math.random();
    jQuery('.wdm-pdf-iframe').attr('src', file);
    jQuery(".wdm-pdf-preview-modal").modal().on("hidden.bs.modal", function () {
      jQuery(this).remove()
  });
}
//End of display PDF in modal.

/**
 * This function is used to send quote through mail
 */
 jQuery("body").on('click', '#btnQuoteSend', function ( e ) {
    jQuery('.wdm-quote-heading').html(jQuery('.wdm-quote-heading').html()+globalEnquiryID)
    jQuery('.wdm-quote-modal').modal();
});
 // End of send quote through mail






//If WPML is active this is used to check language
jQuery(".quoteup-pdf-language").change(function () {
    if(jQuery(this).val() != 'null') {
        jQuery('#quoteProducts').css('display','block');
        jQuery('#saveSendQuote').css('display','block');
    } else {
        jQuery('#quoteProducts').css('display','none');
        jQuery('#saveSendQuote').css('display','none');
    }
});


/**
 * This function is triggered when quantity or price of any product is changed
 */
 jQuery(document).on( 'input', ".quote-newqty, .wdm-prod-price", function(){
    var rowNumber = jQuery(this).data('row-num');
    var qty = jQuery(this).closest("tr").find(".quote-newqty").val(); //Updated Qty
    var price = jQuery(this).closest("tr").find(".wdm-prod-price").val(); //Updated Price
    var finalprice = qty*price; //final total of porduct
    jQuery('#content-cost-'+rowNumber).html(quoteupFormatPrice(finalprice));
    jQuery('#content-amount-'+rowNumber).val(finalprice);
    var finaltotal=0
    jQuery('.amount_database').each(function () {
        var current=jQuery(this).val();
        finaltotal=parseFloat(finaltotal)+parseFloat(current);
    })

    jQuery('#database-amount').val(finaltotal);
    jQuery('.quote-final-total').html(quoteupFormatPrice(finaltotal));
});


 jQuery("#btnSendQuote").click(function () {
    jQuery('#btnSendQuote .quote-save, .quote-send').attr('disabled', 'disabled');
    var enquiry_id = globalEnquiryID;
    var cemail = jQuery('input[name="txtemail"]').val();
    var subject = jQuery('#subject').val();
    var message = jQuery('#wdm_message').val();
    var language = jQuery('.quoteup-pdf-language').val();
    jQuery("#txt").css("visibility", "visible");
    jQuery("#Load").css("visibility", "visible");
    document.getElementById("txt").innerHTML = "";
    var data = {
        'action': 'action_send_dashboard_quote',
        'enquiry_id': enquiry_id,
        'email': cemail,
        'subject': subject,
        'message': message,
        'language' : language,
        'source' : 'ajaxCall',
    };

    jQuery.post(wdm_data.ajax_url, data, function ( response ) {

        jQuery("#Load").css("visibility", "hidden");
        jQuery("#txt").css("visibility", "visible");
        jQuery("#text").css("visibility", "visible");
        jQuery("#text").css("display", "block");
        jQuery('#btnSendQuote .quote-save, .quote-send').attr('disabled', 'false');
        document.getElementById("text").innerHTML = response;
        document.getElementById("txt").innerHTML = response;
        return;
    });

});

 //Language change alert
 var prev_val;

 $('.quoteup-pdf-language').focus(function() {
    prev_val = $(this).val();
}).change(function() {
        $(this).blur() // Firefox fix as suggested by AgDude
        if(jQuery(".admin-quote-table tbody tr").length>0 && jQuery(".admin-quote-table tbody tr.quoteup-no-product").length == 0){
            var success = confirm("Are you sure you want to change the Language?        Products in a language other than the quote language selected will be removed.");
            if(success)
            {
                jQuery(".admin-quote-table tbody tr").remove();
                jQuery(".admin-quote-table tbody").append(wdm_data.quoteup_no_products);
                jQuery('.quote-final-total').html(quoteupFormatPrice('0'));
            }  
            else
            {
                $(this).val(prev_val);
                return false; 
            }
        }
    });
 //End of language change alert
 
 //Expiration Date picker
 jQuery(".wdm-input-expiration-date").datepicker({
    altFormat: "yy-mm-dd 00:00:00",
    altField: ".expiration_date_hidden",
    minDate: 0,
    showButtonPanel: true,
    closeText: dateData.closeText,
    currentText: dateData.currentText,
    monthNames: dateData.monthNames,
    monthNamesShort: dateData.monthNamesShort,
    dayNames: dateData.dayNames,
    dayNamesShort: dateData.dayNamesShort,
    dayNamesMin: dateData.dayNamesMin,
    dateFormat: dateData.dateFormat,
    firstDay: dateData.firstDay,
    isRTL: dateData.isRTL,
});
 //End of expiration date picker

  //Change Quantity to 1 if it is blank or invalid
  jQuery('body').on('focusout','.wdm-prod-price, .wdm-prod-quant',function() {
      makeDataValid(jQuery(this));
  });

  function makeDataValid(selector)
  {
      var isDataValid = true;
      if (selector.hasClass('wdm-prod-quant')) {
        if (!jQuery.trim(selector.val()) || selector.val() <= 0) {
          isDataValid = false;
          selector.val("1");
      }
      if ( Number(selector.val()) % 1 !== 0 ) {
        isDataValid = false;
        selector.val("1");
    }
} else {
    if (!jQuery.trim(selector.val()) || selector.val() < 0) {
      isDataValid = false;
      selector.val("0");
  }
}

if (isDataValid == false) {
 rowNumber=selector.attr("data-row-num");
 var quantity=jQuery('#content-qty-'+rowNumber).val();
 var newprice=jQuery('#content-new-'+rowNumber).val();
 var finalprice=newprice*quantity;
 jQuery('#content-cost-'+rowNumber).html(quoteupFormatPrice(finalprice));
 jQuery('#content-amount-'+rowNumber).val(finalprice);
 var finaltotal=0;
 jQuery('.amount_database').each(function () {
    finaltotal=parseFloat(finaltotal)+parseFloat(jQuery(this).val());
})

 jQuery('.quote-final-total').html(quoteupFormatPrice(finaltotal));
}
}

Array.prototype.compare = function ( testArr ) {
    if ( this.length != testArr.length ) {
      return false;
  }
  for (var i = 0; i < testArr.length; i++) {
      if ( this[i].compare ) {
        if ( !this[i].compare(testArr[i]) ) {
          return false;
      }
  }
  if ( this[i] !== testArr[i] ) {
    return false;
}
}
return true;
}

})

qtyValidated = true;
priceValidated = true;