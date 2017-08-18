<?php


/**
 *
 * Bazaar Theme Functions
 *
 * This is your standard WordPress
 * functions.php file.
 *
 * @author  Alessandro Vellutini
 *
*/


	require_once get_template_directory() . '/core/main.php';










function pep_add_new_field($enq_fields) {
   $new_field = array(
                     /* the id of each field should be unique, do not include spaces or special characters except _ */
                     'id' => '',

                     /* you can specify a class, to style the element, there are two classes provided wdm-modal_text, wdm-modal_textarea, wdm-modal_radio
                    To specify multiple classes, add classes separated by a space */
                    'class' => '',

                    /* specify the type of the field: text, textarea, radio, select, checkbox, multiple*/
                    'type' => '',

                    /* add a placeholder for a customer making the inquiry */
                    'placeholder' => '',

                   /* specify if the field is required ‘yes’ or ‘no’ */
                   'required' => '',

                   /* add a message which has to be displayed, if the required field is not filled */
                   'required_message' => '',

                   /* specify a regular expression (excluding the leading and trailing /), to validate the field */
                   'validation' => '',

                   /* add a message which has to be displayed, if there is an error in validation */
                   'validation_message' => '',

                   /* if the value has to be added to the admin email sent, specify ‘yes’ else ‘no’ */
                   'include_in_admin_mail' => '',

                   /* if the value has to be added to the email sent to the customer, specify ‘yes’ else ‘no’ */
                   'include_in_customer_mail' => '',

                   /* add a label for the field, which will be used to save the field in the database */
                   'label' => '',

                  /* add a default value for text field or textarea */
                  'value' => '',

                  /* for radio buttons, checkbox, multiple select, dropdown, and select field, specify an array of options */
                  'options' => ''
                );

    // ****** IMPORTANT********     
    // the order of the fields specified will be decide the order in which fields will be displayed
    $enq_fields = array( $enq_fields, $new_field );
    return $enq_fields;
}

/* replace <field_id> with custname, txtemail, txtphone, txtsubject, txtmsg */
add_filter( 'pep_fields_<field_id>' , 'pep_add_new_field' , 10, 1 );











function pep_add_favorite_color_options($enq_fields)
{
    $favorite_color = array(
                         'id'=>'fav_color',
                         'class'=>'wdm-modal_text',
                         'type'=>'select',
                         'placeholder'=>'Price Term',
                         'required'=>'yes',
                         'required_message' => 'Price Term cannot be empty',
                         'validation' => '',
                         'validation_message' => '',
                         'include_in_admin_mail'=>'yes',
                         'include_in_customer_mail'=>'yes',
                         'label' => 'Price Term',
                         'options' => array(
                                      'one'=> 'Select',
                                      'two' => 'FOB',
                                      'three' => 'CIF',
                                      'four' => 'DDP'
                                     )
                   );

    // ****** IMPORTANT********
    // adding the field before $enq_fields, will place the favorite color field, above the subject field
    $enq_fields = array($favorite_color, $enq_fields);
    return $enq_fields;
}

/* since we will be adding this field next to the subject field, hook the function on pep_fields_txtsubject */
add_filter( 'pep_fields_txtsubject', 'pep_add_favorite_color_options', 10, 1 );




















  
?>