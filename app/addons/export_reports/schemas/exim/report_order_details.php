<?php

use Tygh\Registry;

include_once( 'reports.functions.php' );

$schema = array(
    'section' => 'reports',
    'pattern_id' => 'report_order_details',
    'name' => 'Report: Orders Details',
    'key' => array('order_id'),
    'order' => 2,
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
        'order_data' => array(      // For coupons
            'reference_fields' => array( 
                'order_id' => '&order_id',
                'type' => 'C'
            ),
            'join_type' => 'LEFT'
        ),
        'products' => array(
            'reference_fields' => array( 'product_id' => '#order_details.product_id' ),
            'join_type' => 'LEFT'
        ),
        'product_descriptions' => array(
            'reference_fields' => array( 'product_id' => '#products.product_id' ),
            'join_type' => 'LEFT'
        ),
        'product_features_values' => array(
            'reference_fields' => array( 
                'feature_id' => '@brand_id',
                'product_id' => '#products.product_id',
            ),
            'join_type' => 'LEFT'
        ),
        'product_feature_variant_descriptions' => array(
            'reference_fields' => array( 'variant_id' => '#product_features_values.variant_id' ),
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
        'Product Code' => array(
            'db_field' => 'product_code',
            'table' => 'order_details',
        ),
        'SKU' => array(
            'db_field' => 'item_id',
            'table' => 'order_details',
        ),
        'Vendor' => array(
            'db_field' => 'company',
            'table' => 'companies',
        ),
        'Product name' => array(
            'db_field' => 'product',
            'table' => 'product_descriptions',
        ),
        'Brand' => array(
            'db_field' => 'variant',
            'table' => 'product_feature_variant_descriptions',
        ),
        'Product Options (Size)' =>array(
            'linked' => false,
            'process_get' => array('fn_exim_get_product_variants', '#row'),
        ),
        'Quantity' => array(
            'db_field' => 'amount',
            'table' => 'order_details',
        ),
        'Merchant Retail Price incl. GST (RM)' => array(
            'db_field' => 'list_price',
            'table' => 'products',
        ),
        'Disc. Merchant Retail Price incl. GST (RM)' => array(
            'linked' => false,
            'process_get' => array('fn_exim_get_order_details_base_price', '#row'),
        ),        
        'Customer Payable Price (RM)' => array(
            'db_field' => 'price',
            'table' => 'order_details',
        ),
        'Item Discount' => array(
            'linked' => false,
            'process_get' => array('fn_exim_get_order_details_discount', '#row'),
        ),
        'Item Extra Info.' => array(
            'db_field' => 'extra',
            'table' => 'order_details',
            'linked' => true,
            'process_get' => array('fn_exim_unserialize', '#this'),
        ),
        'Order Status' => array(
            'db_field' => 'description',
            'table' => 'status_descriptions',
        ),
    ),
);

return $schema;
