<?php

use Tygh\Registry;

include_once( 'reports.functions.php' );

$schema = array(
    'section' => 'reports',
    'pattern_id' => 'report_shipments',
    'name' => 'Report: Shipments',
    'key' => array('order_id'),
    'order' => 3,
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
        'order_details' => array(
            'reference_fields' => array( 'order_id' => '&order_id' ),
            'join_type' => 'LEFT'
        ),
        'shipment_items' => array(
            'reference_fields' => array( 'item_id' => '#order_details.item_id' ),
            'join_type' => ''
        ),
        'shipments' => array(
            'reference_fields' => array( 'shipment_id' => '#shipment_items.shipment_id' ),
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
        
    ),
    'export_fields' => array(
        'Order ID' => array(
            'db_field' => 'order_id',
            'alt_key' => true,
            'required' => true,
        ),
        'SKU' => array(
            'db_field' => 'item_id',
            'table' => 'order_details',
        ),
        'Shipping (State)' => array(
            'db_field' => 'state',
            'table' => 'state_descriptions',
        ),
        'Shipping Cost (RM)' => array(
            'db_field' => 'shipping_cost',
        ),
        'Shipment ID' => array(
            'db_field' => 'shipment_id',
            'table' => 'shipments'
        ),
        'Shipment Date' => array(
            'db_field' => 'timestamp',
            'table' => 'shipments',
            'process_get' => array('fn_timestamp_to_date', '#this'),
        ),
        'Carrier' => array(
            'db_field' => 'carrier',
            'table' => 'shipments',
        ),
        'Tracking Number' => array(
            'db_field' => 'tracking_number',
            'table' => 'shipments',
        ),
    ),
);

return $schema;
