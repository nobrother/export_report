<?php

use Tygh\Registry;

include_once( 'reports.functions.php' );

$schema = array(
    'section' => 'reports',
    'pattern_id' => 'report_orders',
    'name' => 'Report: Orders',
    'key' => array('order_id'),
    'order' => 1,
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
        'order_data' => array(      // For coupons
            'reference_fields' => array( 
                'order_id' => '&order_id',
                'type' => 'C'
            ),
            'join_type' => 'LEFT'
        ),
        'states' => array(
            'reference_fields' => array( 
                'code' => '&s_state',
                'country_code' => '&s_country',
            ),
            'join_type' => 'LEFT'
        ),
        'state_descriptions' => array(
            'reference_fields' => array( 'state_id' => '#states.state_id' ),
            'join_type' => 'LEFT'
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
        'Payment Date' => array(
            'db_field' => 'timestamp',
            'process_get' => array('fn_timestamp_to_date', '#this'),
        ),
        'Vendor' => array(
            'db_field' => 'company',
            'table' => 'companies',
        ),
        'Order Status' => array(
            'db_field' => 'description',
            'table' => 'status_descriptions',
        ),
        'Total Order Amount (RM)' => array(
            'db_field' => 'total',
        ),
        'Subtotal Order Amount (RM)' => array(
            'db_field' => 'subtotal',
        ),
        'Order Including Discount (RM)' => array(
            'db_field' => 'discount',
        ),
        'Order Discount (RM)' => array(
            'db_field' => 'subtotal_discount',
        ),
        'Discount/Promo Code' => array(
            'db_field' => 'data',
            'table' => 'order_data',
            'process_get' => array('fn_exim_report_get_order_coupons', '#row'),
        ),
        'Payment Surcharge (RM)' => array(
            'db_field' => 'payment_surcharge',
        ),
        'Shipping (State)' => array(
            'db_field' => 'state',
            'table' => 'state_descriptions',
        ),
        'Shipping Cost (RM)' => array(
            'db_field' => 'shipping_cost',
        ),
    ),
);

return $schema;
