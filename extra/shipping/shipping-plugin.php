<?php
/**
 * Plugin Name: Cloudways
 * Description: Create a woocommerce custom shipping method plugin
 */
if ( ! defined( 'WPINC' ) ){
	die('security by preventing any direct access to your plugin file');
}
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	function cloudways_shipping_method()
	{
		if (!class_exists('cloudways_Shipping_Method')) {
			class cloudways_Shipping_Method extends WC_Shipping_Method
			{
				public function __construct()
				{
					$this->id = 'cloudways';
					$this->method_title = __('cloudways Shipping', 'cloudways');
					$this->method_description = __('Custom Shipping Method for cloudways', 'cloudways');
					// Contreis availability
					$this->availability = 'including';
					$this->countries = array(
						'ES',
						'GB',
						'US',
					);
					$this->init();
					$this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
					$this->title = isset($this->settings['title']) ? $this->settings['title'] : __('cloudways Shipping', 'cloudways');
				}
				/**
				Load the settings API
				 */
				function init()
				{
					$this->init_form_fields();
					$this->init_settings();
					add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
				}
				function init_form_fields()
				{
					$this->form_fields = array(
						'enabled' => array(
							'title' => __('Enable', 'cloudways'),
							'type' => 'checkbox',
							'default' => 'yes'
						),
						'weight' => array(
							'title' => __('Weight (kg)', 'cloudways'),
							'type' => 'number',
							'default' => 50
						),
						'title' => array(
							'title' => __('Title', 'cloudways'),
							'type' => 'text',
							'default' => __('cloudways Shipping', 'cloudways')
						),
					);
				}
				public function cloudways_shipping_calculation($package)
				{
					$weight = 0;
					$cost = 0;
					$country = $package["destination"]["country"];
					foreach ($package['contents'] as $item_id => $values) {
						$_product = $values['data'];
						$weight = $weight + $_product->get_weight() * $values['quantity'];
					}
					$weight = wc_get_weight($weight, 'kg');
					if ($weight <= 5) {
						$cost = 0;
					} elseif ($weight <= 25) {
						$cost = 5;
					} elseif ($weight <= 45) {
						$cost = 10;
					} else {
						$cost = 15;
					}
					$countryZones = array(
						'ES' => 2,
						'GB' => 2,
						'US' => 3,
						'IT'=>4
					);
					$zonePrices = array(
						2 => 50,
						3 => 70
					);
					$zoneFromCountry = $countryZones[$country];
					$priceFromZone = $zonePrices[$zoneFromCountry];
					$cost += $priceFromZone;
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => $cost
					);
					$this->add_rate($rate);
				}
			}
		}
	}
	add_action('woocommerce_shipping_init', 'cloudways_shipping_method');
	function add_cloudways_shipping_method($methods)
	{
		$methods[] = 'cloudways_Shipping_Method';
		return $methods;
	}
	add_filter('woocommerce_shipping_methods', 'add_cloudways_shipping_method');
	function cloudways_validate_order($posted)
	{
		$packages = WC()->shipping->get_packages();
		$chosen_methods = WC()->session->get('chosen_shipping_methods');
		if (is_array($chosen_methods) && in_array('cloudways', $chosen_methods)) {
			foreach ($packages as $i => $package) {
				if ($chosen_methods[$i] != "cloudways") {
					continue;
				}
				$cloudways_Shipping_Method = new cloudways_Shipping_Method();
				$weightLimit = (int)$cloudways_Shipping_Method->settings['weight'];
				$weight = 0;
				foreach ($package['contents'] as $item_id => $values) {
					$_product = $values['data'];
					$weight = $weight + $_product->get_weight() * $values['quantity'];
				}
				$weight = wc_get_weight($weight, 'kg');
				if ($weight > $weightLimit) {
					$message = sprintf(__('OOPS, %d kg increase the maximum weight of %d kg for %s', 'cloudways'), $weight, $weightLimit, $cloudways_Shipping_Method->title);
					$messageType = "error";
					if (!wc_has_notice($message, $messageType)) {
						wc_add_notice($message, $messageType);
					}
				}
			}
		}
	}
	add_action('woocommerce_review_order_before_cart_contents', 'cloudways_validate_order', 10);
	add_action('woocommerce_after_checkout_validation', 'cloudways_validate_order', 10);
}