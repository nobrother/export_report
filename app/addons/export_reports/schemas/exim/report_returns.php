<?php

use Tygh\Registry;

include_once( 'reports.functions.php' );

$schema = array(
    'section' => 'reports',
    'pattern_id' => 'report_returns',
    'name' => 'Report: Returns',
    'key' => array('order_id'),
    'order' => 4,
    'table' => 'orders',
    'export_only' => true,
    'permissions' => array(
        'import' => 'manage_users',
        'export' => 'view_users',
    ),
    'condition' => array(
        'conditions' => array(
            '&orders.is_parent_order' => 'N'
        ),
    ),
    'options' => array(
        'start_date' => array(
            'title' => 'start_date',
            'type' => 'input',
            'default_value' => '',
            'description' => 'export_report_order_details_options_start_date_tooltip',
        ),
        'end_date' => array(
            'title' => 'end_date',
            'type' => 'input',
            'default_value' => '',
            'description' => 'export_report_order_details_options_end_date_tooltip',
        ),
        'company_id' => array(
            'title' => 'company_id',
            'type' => 'select',
            'default_value' => '',
            'variants_function' => 'fn_exim_companies_selections'
        ),
        'brands' => array(
            'title' => 'brands',
            'type' => 'input',
            'default_value' => '',
            'description' => 'export_report_order_details_options_brands_tooltip',
        ),
        'order_statuses' => array(
            'title' => 'order_statuses',
            'type' => 'input',
            'default_value' => '',
            'description' => 'export_report_order_details_options_statuses_tooltip',
        ),
        'return_statuses' => array(
            'title' => 'return_statuses',
            'type' => 'input',
            'default_value' => '',
            'description' => 'export_report_order_details_options_return_statuses_tooltip',
        ),
        'order_ids' => array(
            'title' => 'order_ids',
            'type' => 'input',
            'default_value' => '',
            'description' => 'export_report_order_details_options_order_ids_tooltip',
        ),
    ),
    'export_pre_moderation' => array(
        array(
            'function' => 'fn_exim_report_pre_moderation',
            'args' => array('$options'),
            'export_only' => true,
        ),
    ),
    'pre_export_process' => array(
        array(
            'function' => 'fn_exim_report_conditions',
            'args' => array('$conditions','$options'),
            'export_only' => true,
        ),
    ),
    'references' => array(
        'companies' => array(
            'reference_fields' => array( 'company_id' => '&company_id' ),
            'join_type' => 'LEFT'
        ),
        'rma_returns' => array(
            'reference_fields' => array( 'order_id' => '&order_id' ),
            'join_type' => ''
        ),
        'statuses' => array(
            'reference_fields' => array( 'status' => '&status', 'type' => STATUSES_ORDER ),
            'join_type' => 'LEFT'
        ),
        'status_descriptions' => array(
            'reference_fields' => array( 'status_id' => '#statuses.status_id' ),
            'join_type' => 'LEFT'
        ),
        
    ),
    'export_fields' => array(
        'Order ID' => array(
            'db_field' => 'order_id',
            'alt_key' => true,
            'required' => true,
        ),
        'Return ID' => array(
            'db_field' => 'return_id',
            'table' => 'rma_returns',
        ),
        'Return Status' => array(
            'db_field' => 'status',
            'table' => 'rma_returns',
            'process_get' => array('fn_exim_get_status_name', '#this', STATUSES_RETURN),
        ),
        'Wallet Refund Amount (RM)' => array(
            'linked' => false,
            'process_get' => array('fn_exim_report_get_refund_by_wallets', '#row'),
        ),
        'Gift Cert. Refund Amount (RM)' => array(
            'linked' => false,
            'process_get' => array('fn_exim_report_get_refund_by_gift_certificates', '#row'),
        ),
        'Return Extra Info.' => array(
            'db_field' => 'extra',
            'table' => 'rma_returns',
                        'process_get' => array('fn_exim_unserialize', '#this'),
        ),
    ),
);

return $schema;
