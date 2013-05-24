<?php
/**
 *
 * Payment gateways admin values
 * This is pulled into the WP backend admin pages
 * under the JobRoller gateways page
 *
 * @author AppThemes
 * @version 1.0
 *
 * Array param definitions are as follows:
 * name    = field name
 * desc    = field description
 * tip     = question mark tooltip text
 * id      = database column name or the WP meta field name
 * css     = any on-the-fly styles you want to add to that field
 * type    = type of html field
 * req     = if the field is required or not (1=required)
 * min     = minimum number of characters allowed before saving data
 * std     = default value. 
 * js      = allows you to pass in javascript for onchange type events
 * vis     = if field should be visible or not. used for dropdown values field
 * visid   = this is the row css id that must correspond with the dropdown value that controls this field
 * options = array of drop-down option value/name combo
 *
 *
 */
global $options_pricing, $options_gateways;

$options_gateways = array (


    array(  'type' => 'tab',
            'tabname' => __('Gateways', APP_TD)),


    array(	'name' => __('Payment Gateways', APP_TD),
                'type' => 'title',
                'desc' => '',
                'id' => ''),


   array(   'name' => '<img src="'.get_bloginfo('template_directory').'/images/paypal-lg.png" />',
            'type' => 'logo',
            'id' => ''),

              array(    'name' => __('PayPal Email', APP_TD),
                        'desc' => '',
                        'tip' => __('Enter your PayPal account email address. This is where your money gets sent.',APP_TD),
                        'id' => 'jr_jobs_paypal_email',
                        'css' => 'min-width:250px;',
                        'type' => 'text',
                        'req' => '',
                        'min' => '',
                        'std' => '',
                        'vis' => ''),
			
			  array(    'name' => __('Enable PayPal IPN', APP_TD),
                        'desc' => __("Disable IPN if the PayPal IPN does not work for you.",APP_TD),
                        'tip' => __('Turning off IPN means that you will need to manually update jobs after payment.',APP_TD),
                        'id' => 'jr_enable_paypal_ipn',
                        'css' => 'width:100px;',
                        'std' => 'no',
                        'js' => '',
                        'type' => 'select',
                        'options' => array(  'yes' => __('Yes', APP_TD),
                                             'no'  => __('No', APP_TD))),
                        
                        
              array(    'name' => __('Enable IPN Debug', APP_TD),
                        'desc' => sprintf( __("Debug PayPal IPN emails will be sent to %s.",APP_TD), get_option('admin_email') ),
                        'tip' => __('If you would like to receive the raw IPN post responses from PayPal to see if payments are being processed correctly, check this box.',APP_TD),
                        'id' => 'jr_paypal_ipn_debug',
                        'css' => '',
                        'type' => 'checkbox',
                        'req' => '',
                        'min' => '',
                        'std' => '',
                        'vis' => ''),


              array(    'name' => __('Sandbox Mode', APP_TD),
                        'desc' => sprintf( __("You must have a <a target='_new' href='%s'>PayPal Sandbox</a> account setup before using this feature.",APP_TD), 'http://developer.paypal.com/' ),
                        'tip' => __('By default PayPal is set to live mode. If you would like to test and see if payments are being processed correctly, check this box to switch to sandbox mode.',APP_TD),
                        'id' => 'jr_use_paypal_sandbox',
                        'css' => 'width:100px;',
                        'std' => 'no',
                        'js' => '',
                        'type' => 'select',
                        'options' => array(  'yes' => __('Yes', APP_TD),
                                             'no'  => __('No', APP_TD))),

    array(  'type' => 'tabend'),

);

// Merge
$options_pricing = array_merge($options_pricing, $options_gateways);
