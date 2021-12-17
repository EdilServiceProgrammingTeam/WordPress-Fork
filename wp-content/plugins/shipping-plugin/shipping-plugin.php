<?php
/**
 * Plugin Name: New shipping
 * Description: spedizioni fatte bene
 */
if ( ! defined( 'WPINC' ) ){
	die('security by preventing any direct access to your plugin file');
}
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	function new_shipping_method()
	{
		if (!class_exists('new_Shipping_Method')) {
			class new_Shipping_Method extends WC_Shipping_Method
			{
				public function __construct()
				{
					$this->id = 'standard-shipping';
					$this->method_title = __('Standard (iva esclusa)', 'cloudways');
					$this->method_description = __('Standard (iva esclusa)', 'cloudways');
					$this->init();
					$this->enabled = $this->settings['enabled'] ?? 'yes';
					$this->title = $this->settings['title'] ?? __( 'Standard (iva esclusa)', 'cloudways' );
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
						'title' => array(
							'title' => __('Title', 'cloudways'),
							'type' => 'text',
							'default' => __('Standard (iva esclusa)', 'cloudways')
						),
					);
				}

				function search_for_range(int $total_cost,array $shipping_costs) {
					if ($total_cost<25) {
						$cost=$shipping_costs[0];
					}
					elseif ($total_cost<45) {
						$cost=$shipping_costs[1];
					}
					elseif ($total_cost<100) {
						$cost=$shipping_costs[2];
					}
					elseif ($total_cost<200) {
						$cost=$shipping_costs[3];
					}
					elseif ($total_cost<500) {
						$cost=$shipping_costs[4];
					}
					elseif ($total_cost<1000) {
						$cost=$shipping_costs[5];
					}
					else {
						$cost=$shipping_costs[6];
					}
					return $cost;
				}

				function apply_range_shipping_cost($total_cost,$case) {
					$light_range = [8,9,10,13,15,23,35];
					$medium_range = [10,12,15,17,25,35,45];
					$heavy_range = [16,18,20,27,35,45,70];
					return match ( $case ) {
						'light' => $this->search_for_range( $total_cost, $light_range ),
						'medium' => $this->search_for_range( $total_cost, $medium_range ),
						'heavy' => $this->search_for_range( $total_cost, $heavy_range ),
					};
				}

				public function calculate_shipping($package = array())
				{
					$countryZones = array(
						'IT' => 1
					);
					$zonePrices = array(
						1 => 0
					);
					$cost = 0;
					$total_without_discount = 0;
					$shipping_class_light = [];
					$shipping_class_medium = [];
					$shipping_class_heavy = [];
					$country = $package["destination"]["country"];
					foreach ($package['contents'] as $item_id => $values) {
						$_product = $values['data'];
						$regular_price=$_product->get_regular_price();
						$total_without_discount+=$regular_price;
						if ($_product->get_weight() && !$_product->get_shipping_class()) {
							$weight = $_product->get_weight() * $values['quantity'];
							$cost+= $weight *0.4; //very important variable (0.4)
						}
						else {
							$shipping_class=$_product->get_shipping_class();
							if ($shipping_class) {
								switch($shipping_class) {
									case 'light':
										array_push($shipping_class_light,$regular_price);
										break;
									case 'medium':
										array_push($shipping_class_medium,$regular_price);
										break;
									case 'heavy':
										array_push($shipping_class_heavy,$regular_price);
										break;
								}
							}
							else {
								array_push($shipping_class_medium,$regular_price);
								break;
							}
						}

					}

					if ($shipping_class_heavy) {
						$cost+=$this->apply_range_shipping_cost($total_without_discount,'heavy');
					}
					elseif ($shipping_class_medium) {
						$cost+=$this->apply_range_shipping_cost($total_without_discount,'medium');
					}
					elseif ($shipping_class_light) {
						$cost+=$this->apply_range_shipping_cost($total_without_discount,'light');
					}

					$zoneFromCountry = $countryZones[$country];
					$priceFromZone = $zonePrices[$zoneFromCountry];
					$cost += $priceFromZone;
					if ($cost<8) {
						$cost=8;
					}
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
	add_action('woocommerce_shipping_init', 'new_shipping_method');
	function add_new_shipping_method($methods)
	{
		$methods['standard-shipping'] = 'new_Shipping_Method';
		return $methods;
	}
	add_filter('woocommerce_shipping_methods', 'add_new_shipping_method');
	function validate_order($posted)
	{
		$packages = WC()->shipping->get_packages();
		$country = $packages[0]["destination"]["country"];

		$message ='ciao';
		$messageType = "error";
		if (!wc_has_notice($message, $messageType)) {
			wc_add_notice($message, $messageType);
		}
	}
	add_action('woocommerce_review_order_before_cart_contents', 'validate_order', 10);
	add_action('woocommerce_after_checkout_validation', 'validate_order', 10);
}