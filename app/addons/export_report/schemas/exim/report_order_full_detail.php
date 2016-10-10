<?php

use Tygh\Registry;

include_once( 'report_order_full_detail.functions.php' );

$schema = array(
	'section' => 'report_order_full_detail',
	'pattern_id' => 'report_order_full_detail',
	'name' => 'Report: Orders',
	'key' => array('order_id'),
	'order' => 0,
	'table' => 'orders',
	'export_only' => true,
	'permissions' => array(
		'import' => 'manage_users',
		'export' => 'view_users',
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
		'products' => array(
			'reference_fields' => array( 'product_id' => '#order_details.product_id' ),
			'join_type' => 'LEFT'
		),
		'product_descriptions' => array(
			'reference_fields' => array( 'product_id' => '#products.product_id' ),
			'join_type' => 'LEFT'
		),
		'shipment_items' => array(
			'reference_fields' => array( 'item_id' => '#order_details.item_id' ),
			'join_type' => 'LEFT'
		),
		'shipment_items' => array(
			'reference_fields' => array( 'shipment_id' => '#shipment_items.shipment_id' ),
			'join_type' => 'LEFT'
		),
		'state_descriptions' => array(
			'reference_fields' => array( 'state_id' => '&s_state' ),
			'join_type' => 'LEFT'
		),
		'promotions' => array(
			'reference_fields' => array( 'promotion_id' => '&promotion_ids' ),
			'join_type' => 'LEFT'
		),
		'rma_returns' => array(
			'reference_fields' => array( 'order_id' => '&order_id' ),
			'join_type' => 'LEFT'
		),
		'statuses' => array(
			'reference_fields' => array( 'status' => '#rma_returns.status', 'type' => STATUSES_RETURN ),
			'join_type' => 'LEFT'
		),
		'status_descriptions' => array(
			'reference_fields' => array( 'status_id' => '#statuses.status_id' ),
			'join_type' => 'LEFT'
		),
		
	),
	'options' => array(
		'delimiter' => array(
			'default_value' => 'C',
		),
	),
	'export_fields' => array(
		'Return ID' => array(
			'db_field' => 'return_id',
			'alt_key' => true,
			'required' => true,
		),
		'Status' => array(
			'db_field' => 'description',
			'table' => 'status_descriptions',
		),
		'Customer Firstname' => array(
			'db_field' => 'firstname',
			'table' => 'orders',
		),
		'Customer Lastname' => array(
			'db_field' => 'lastname',
			'table' => 'orders',
		),
		'Request Date' => array(
			'db_field' => 'timestamp',
			'process_get' => array( 'fn_timestamp_to_date', '#this' ),
		),
		'Action' => array(
			'db_field' => 'property',
			'table' => 'rma_property_descriptions',
		),
		'Order ID' => array(
			'db_field' => 'order_id',
		),
		'Quantity' => array(
			'db_field' => 'total_amount',
		),
	),
);

return $schema;
