<?php

use Tygh\Registry;


/*
 * Amount refund by wallet
 */
function fn_exim_report_get_refund_by_wallets($row){
	$extra = fn_exim_report_get_refund($row);

	if(empty($extra) || empty($extra['wallet']))
		return '';

	return $extra['wallet'];
}

/*
 * Amount refund by gift certificates
 */
function fn_exim_report_get_refund_by_gift_certificates($row){
	$extra = fn_exim_report_get_refund($row);

	if(empty($extra) || empty($extra['gift_certificates']))
		return '';

	return $extra['gift_certificates'];
}

/*
 * Get Order Detail Discount (Product Discount)
 */
function fn_exim_get_order_details_discount($row){
	$extra = fn_exim_report_get_order_detail_extra($row);

	if(empty($extra) || empty($extra['discount']))
		return '';

	return $extra['discount'];
}

/*
 * Get Order Detail Base Price (Original Price)
 */
function fn_exim_get_order_details_base_price($row){
	$extra = fn_exim_report_get_order_detail_extra($row);

	if(empty($extra) || empty($extra['base_price']))
		return '';

	return $extra['base_price'];
}

/*
 * Get Order Coupons
 */
function fn_exim_report_get_order_coupons($row){
	
	if(empty($row['Discount/Promo Code']))
		return '';

	$data = @unserialize($row['Discount/Promo Code']);

	if(empty($data))
		return '';

	return implode(' | ', array_keys($data));
}

/*
 * Get Order Taxes
 */
/*
function fn_exim_report_get_order_taxes($row){
	$order_info = fn_exim_report_get_order_info($row);

	if(empty($order_info) || empty($order_info['taxes']))
		return '';
	
	$return = 0;
	foreach ($order_info['taxes'] as $tax)
		$return += $tax['tax_subtotal'];

	return $return;
}*/

/*
 * Get Order Detail Extra
 */
function fn_exim_report_get_order_detail_extra($row){

	if(empty($row['Item Extra Info.']) || empty($row['SKU']))
		return false;

	$extra = $row['Item Extra Info.'];
	$item_id = $row['SKU'];

	/*// Set cache in $_POST
	if(!isset($_POST['order_detail_extra_cache']))
		$_POST['order_detail_extra_cache'] = array();

	if(!isset($_POST['order_detail_extra_cache'][$item_id])){
		$_POST['order_detail_extra_cache'][$item_id] = @unserialize($extra);
	}
	error_log(print_r($_POST['order_detail_extra_cache'][$item_id],true));
	// Check cache in $_POST
	$cache = $_POST['order_detail_extra_cache'][$item_id];	
	if(empty($cache))
		return false;

	return $cache;*/

	return @unserialize($extra);
}

/*
 * Get Order Detail Extra
 */
function fn_exim_report_get_refund($row){

	if(empty($row['Return Extra Info.']))
		return false;

	$extra = @unserialize($row['Return Extra Info.']);
	$order_id = $row['Order ID'];

	if(empty($extra))
		return false;

	// Set cache in $_POST
	if(!isset($_POST['return_extra_cache']))
		$_POST['return_extra_cache'] = array();

	if(!isset($_POST['return_extra_cache'][$order_id])){
		$tmp = array(
			'wallet' => 0,
			'gift_certificates' => 0,
		);

		if(isset($extra['wallet'])){
			foreach ($extra['wallet'] as $wallet) {
				$tmp['wallet'] += $wallet['amount'];
			}
		}
		if(isset($extra['gift_certificates'])){
			foreach ($extra['gift_certificates'] as $gc) {
				$tmp['gift_certificates'] += $gc['amount'];
			}
		}
		$_POST['return_extra_cache'][$order_id] = $tmp;
	}

	// Check cache in $_POST
	$cache = $_POST['return_extra_cache'][$order_id];	
	if(empty($cache))
		return false;

	return $cache;
}

function fn_exim_report_conditions(&$conditions, &$options){
	extract($options);

	// Start Date
	if(!empty($start_date))
		$start_date = strtotime($start_date);
	if(!empty($start_date))
		$conditions[] = db_quote("orders.timestamp >= ?i", $start_date);

	// End Date
	if(!empty($end_date))
		$end_date = strtotime($end_date);
	if(!empty($end_date))
		$conditions[] = db_quote("orders.timestamp <= ?i", $end_date);

	// Company
	if(!empty($company_id))
		$conditions[] = db_quote("orders.company_id = ?i", $company_id);

	// Brands
	if(!empty($brands) && !empty($options['brand_id'])){
		$brands = fn_exim_report_process_multiple_choices($brands);

		if(!empty($brands)){
			// Search for brands variant id
			$brand_variant_ids = db_get_fields("SELECT variant_id FROM ?:product_feature_variant_descriptions WHERE variant IN (?a)", $brands);

			$conditions[] = db_quote("product_features_values.variant_id IN (?a)", $brand_variant_ids);
		}
	}

	// Order Statuses
	if(!empty($order_statuses)){

		$order_statuses = fn_exim_report_process_multiple_choices($order_statuses);

		if(!empty($order_statuses)){
			// Search for statuses variant id
			$order_statuses = db_get_fields("SELECT b.status FROM ?:status_descriptions a JOIN ?:statuses b ON a.status_id = b.status_id WHERE a.description IN (?a) AND b.type = ?s", $order_statuses, STATUSES_ORDER);

			$conditions[] = db_quote("orders.status IN (?a)", $order_statuses);
		}
	}

	// Return Statuses
	if(!empty($return_statuses)){

		$return_statuses = fn_exim_report_process_multiple_choices($return_statuses);

		if(!empty($return_statuses)){
			// Search for statuses variant id
			$return_statuses = db_get_fields("SELECT b.status FROM ?:status_descriptions a JOIN ?:statuses b ON a.status_id = b.status_id WHERE a.description IN (?a) AND b.type = ?s", $return_statuses, STATUSES_RETURN);

			$conditions[] = db_quote("rma_returns.status IN (?a)", $return_statuses);
		}
	}

	// Order Ids
	if(!empty($order_ids)){
		$order_ids = fn_exim_report_process_multiple_choices($order_ids);
		if(!empty($order_ids))
			$conditions[] = db_quote("orders.order_id IN (?a)", $order_ids);
	}

	/*error_log(print_r($conditions,true));
	//error_log(print_r($options,true));
	die();*/
}

function fn_exim_report_pre_moderation(&$options){

	// Get the Product Feature ID for Brand
	$brand_id = db_get_field("SELECT feature_id FROM ?:product_features_descriptions WHERE LOWER(description) LIKE  'brand%'");
	if(empty($brand_id))
		$brand_id = 0;

	$options['brand_id'] = $brand_id;
}

function fn_exim_report_process_multiple_choices($data, $delimiter = ','){
	if(empty($data))
		return array();

	$return = array();
	foreach (explode($delimiter, $data) as $value){
		$tmp = trim($value);
		if(!empty($tmp))
			$return[] = trim($value);
	}
	
	return $return;
}

function fn_exim_companies_selections(){
	$return = array( '0' => "-");
	$companies = db_get_array("SELECT company_id, company FROM ?:companies");

	foreach ($companies as $key => $value) 
		$return[$value['company_id']] = $value['company'];

	return $return;
}

function fn_exim_unserialize($data){
    if (!empty($data)) {
        $data = @unserialize($data);
        return fn_exim_json_encode($data);
    }

    return '';
}

/*function fn_exim_promotion_info($row){
	$return = array(
		'conditions' => array(),
		'bonuses' => array(),
	);

	if (empty($row)) 
		return array();

	if(isset($row['Promotion: Conditions']))
		$return['conditions'] = @unserialize($row['Promotion: Conditions']);

	if(isset($row['Promotion: Bonuses']))
		$return['bonuses'] = @unserialize($row['Promotion: Bonuses']);

    return $return;	
}

function fn_exim_coupon_name($data, $row){
	$info = fn_exim_promotion_info($row);
	
	if(!isset($info['conditions']['conditions']) || !is_array($info['conditions']['conditions']))
		return '';
	
	foreach ($info['conditions']['conditions'] as $key => $value) {
		if(isset($value['condition']) && $value['condition'] == 'coupon_code')
			return $value['value'];
	}
	return '';
}

function fn_exim_coupon_value($data, $row){
	$info = fn_exim_promotion_info($row);
	
	if(empty($info['conditions']))
		return '';

	foreach ($info['conditions']['conditions'] as $key => $value) {
		if(isset($value['condition']) && $value['condition'] == 'coupon_code'){
			foreach ($info['bonuses'] as $key => $value) {
				if(isset($value['discount_value']))
					return $value['discount_value'];
			}
		}
	}
	
	return '';
}*/

function fn_exim_get_status_name($data, $type = STATUSES_ORDER){

	if (empty($data)) 
		return '';

	$status = db_get_field("SELECT b.description FROM ?:statuses a LEFT JOIN ?:status_descriptions b ON a.status_id = b.status_id WHERE a.status = ?s AND a.type = ?s", $data, $type);
   return $status;
}

function fn_exim_get_product_variants($row){
	if (empty($row) || empty($row['Item Extra Info.'])) 
		return '';

	$extra = @unserialize($row['Item Extra Info.']);
	if(empty($extra['product_options']))
		return '';
	
	$return = array();
	foreach ($extra['product_options'] as $key => $option) {
		$name = db_get_field("SELECT option_name FROM ?:product_options_descriptions WHERE option_id = ?i", $key);
		if(empty($name))
			$name = 'Unknown';
		$name_cache[$key] = $name;

		$value = db_get_field("SELECT variant_name FROM ?:product_option_variants_descriptions WHERE variant_id = ?i", $option);
		if(empty($value))
			$value = 'Unknown';

		$return[] = $name.' : '.$value;
	}
	return implode(' | ', $return);
}