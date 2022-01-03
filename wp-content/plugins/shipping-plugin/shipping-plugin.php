<?php
/**
 * Plugin Name: Standard shipping
 * Description: The standard shipping method with preconfigured ranges and multipliers that will estimate the shipping cost
 * Text Domain: shipping-plugin
 * Domain Path: /languages
 */
if ( ! defined( 'WPINC' ) ){
	die('security by preventing any direct access to your plugin file');
}
function admin_theme_style() {
	wp_enqueue_style('admin-theme', plugins_url('css/wp-admin.css', __FILE__));
}
function translations_init() {
	load_plugin_textdomain( 'shipping-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action('init', 'translations_init');
add_action('admin_enqueue_scripts', 'admin_theme_style');
add_action('login_enqueue_scripts', 'admin_theme_style');
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	function new_shipping_method()
	{
		if (!class_exists('new_Shipping_Method')) {
			class new_Shipping_Method extends WC_Shipping_Method
			{
				public function __construct()
				{
					parent::__construct();
					$this->id = 'standard-shipping';
					$this->method_title = __('Standard (tax excluded)', 'shipping-plugin');
					$this->method_description = __('
					<div>
						This is the standard shipping method. 
						It calculates the shipping cost based on the products that have a weight property.
						The rest of the products are divided into three shipping classes: light, medium and heavy. 
						The products without a shipping class will be considered as "light". <br>
						The three shipping classes estimate the products shipping cost based on total price ranges.
						That\'s dedicated to the products which don\'t have the weight property.<br>
						Feel free to change the values in the settings to what best fits you.
						Here you can see the ranges:
					</div>
					<div style="margin-top: 5px;">
						<table class="bordered-table">
						<thead>
						  <tr>
						    <th class="bordered-table">Range</th>
						    <th class="bordered-table">Light</th>
						    <th class="bordered-table">Medium</th>
						    <th class="bordered-table">Heavy</th>
						  </tr>
						</thead>
						<tbody>
						  <tr>
						    <td class="bordered-table">€0-25</td>
						    <td class="bordered-table">€8</td>
						    <td class="bordered-table">€10</td>
						    <td class="bordered-table">€16</td>
						  </tr>
						  <tr>
						    <td class="bordered-table">€25-45</td>
						    <td class="bordered-table">€9</td>
						    <td class="bordered-table">€12</td>
						    <td class="bordered-table">€18</td>
						  </tr>
						  <tr>
						    <td class="bordered-table">€45-100</td>
						    <td class="bordered-table">€10</td>
						    <td class="bordered-table">€15</td>
						    <td class="bordered-table">€20</td>
						  </tr>
						  <tr>
						    <td class="bordered-table">€100-200</td>
						    <td class="bordered-table">€113</td>
						    <td class="bordered-table">€17</td>
						    <td class="bordered-table">€27</td>
						  </tr>
						  <tr>
						    <td class="bordered-table">€200-500</td>
						    <td class="bordered-table">€15</td>
						    <td class="bordered-table">€25</td>
						    <td class="bordered-table">€35</td>
						  </tr>
						  <tr>
						    <td class="bordered-table">€500-1000</td>
						    <td class="bordered-table">€23</td>
						    <td class="bordered-table">€25</td>
						    <td class="bordered-table">€45</td>
						  </tr>
						  <tr>
						    <td class="bordered-table">€1000+</td>
						    <td class="bordered-table">€35</td>
						    <td class="bordered-table">€45</td>
						    <td class="bordered-table">€70</td>
						  </tr>
						</tbody>
						</table>
					</div>', 'shipping-plugin');
					$this->init();
					$this->enabled = $this->settings['enabled'] ?? 'yes';
					$this->title = $this->settings['title'] ?? __( 'Standard (tax excluded)', 'shipping-plugin' );
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
					load_plugin_textdomain( 'shipping-plugin');
					$this->form_fields = array(
						'enabled' => array(
							'title' => __('Enable','shipping-plugin'),
							'type' => 'checkbox',
							'default' => 'yes'
						),
						'title' => array(
							'title' => __('Title'),
							'type' => 'text',
							'default' => __('Standard (tax excluded)','shipping-plugin')
						),
						'enable-takeaway' => array(
							'title' => __('Enable takeaway'),
							'type' => 'checkbox',
							'default' =>'yes'
						),
						'take-away-cost' => array(
							'title' => __('Takeaway cost (default set to 0','shipping-plugin'),
							'type'=>'text',
							'default' =>'0'
						),
						'minimum-cost'=> array(
							'title'=>__('Minimum Cost (default set to 0)','shipping-plugin'),
							'type'=>'text',
							'default'=>'0'
						),
						'take-away-info'=> array(
							'title'=>__('Take away info Cost (default set to "")','shipping-plugin'),
							'type'=>'text',
							'default'=>''
						),
						'cost-per-kilo'=> array(
							'title'=>__('Cost per Kilo (default set to 0.4)','shipping-plugin'),
							'type'=>'text',
							'default'=>'0.4'
						),
						'multiplier-quantity'=> array(
							'title'=>__('Multiplier per quantity (default set to 0.2)','shipping-plugin'),
							'description'=>__('Increments the shipping cost based on the total quantity of the
							 products in cart after €200 is reached if the products are not lightweight.<br>
							 Final shipping cost = calculated shipping cost * (1 + total quantity * multiplier)','shipping-plugin'),
							'type'=>'text',
							'default'=>'0.2'
						)
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

				function apply_multiplier_per_quantity($cost,$package,$total_without_discount) {
					if ($total_without_discount>200) {
						$total_quantity=0;
						foreach ($package['contents'] as $item_id => $values) {
							$total_quantity+=$values['quantity'];
						}
						$cost+=1+$total_quantity*floatval($this->settings['multiplier-quantity']);
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
					load_plugin_textdomain( 'shipping-plugin');
					$cost = 0;
					$not_standard=false;
					$total_without_discount = 0;
					$shipping_class_light = [];
					$shipping_class_medium = [];
					$shipping_class_heavy = [];
					$shipping_class_free =[];
					$shipping_class_takeaway =[];
					foreach ($package['contents'] as $item_id => $values) {
						$_product = $values['data'];
						$regular_price=$_product->get_regular_price();
						if (!in_array($_product->get_shipping_class(),['freeshipping','takeaway-only'])) {
							$total_without_discount+=$regular_price*$values['quantity'];
						}
						if ($_product->get_weight() && !$_product->get_shipping_class()) {
							$weight = $_product->get_weight() * $values['quantity'];
							$cost+= $weight*floatval($this->settings['cost-per-kilo']);
						}
						else {
							$shipping_class=$_product->get_shipping_class();
							if ($shipping_class) {
								switch($shipping_class) {
									case 'light':
										$shipping_class_light[] = $regular_price*$values['quantity'];
										break;
									case 'medium':
										$shipping_class_medium[] = $regular_price*$values['quantity'];
										break;
									case 'heavy':
										$shipping_class_heavy[] = $regular_price*$values['quantity'];
										break;
									case 'freeshipping':
										$shipping_class_free[]=$regular_price*$values['quantity'];
										break;
									case 'takeaway-only':
										$shipping_class_takeaway[]=$regular_price*$values['quantity'];
										break;
									case 'custom-shipping':
										if ($_product->meta_exists('custom-shipping-cost')) {
											$cost+=floatval(get_post_meta( $_product->get_id(), 'custom-shipping-cost', true ));
										}
										else {
											$shipping_class_heavy[] = $regular_price*$values['quantity'];
										}
								}
							}
							else {
								$shipping_class_light[] = $regular_price;
								break;
							}
						}

					}
					if ($shipping_class_heavy) {
						$cost+=$this->apply_range_shipping_cost($total_without_discount,'heavy');
						$cost=$this->apply_multiplier_per_quantity( $cost, $package, $total_without_discount );
					}
					elseif ($shipping_class_medium) {
						$cost+=$this->apply_range_shipping_cost($total_without_discount,'medium');
						$cost=$this->apply_multiplier_per_quantity( $cost, $package, $total_without_discount );
					}
					elseif ($shipping_class_light) {
						$cost+=$this->apply_range_shipping_cost($total_without_discount,'light');
					}
					elseif ($shipping_class_free) {
						$this->add_rate(array(
							'id' =>'free-shipping',
							'label' =>__('Spedizione gratuita','shipping-plugin'),
							'cost' => 0
						));
						$not_standard=true;
					}
					elseif ($shipping_class_takeaway) {
						$not_standard=true;
					}
					else {
						$cost+=$this->apply_range_shipping_cost($total_without_discount,'light');
					}
					if ($cost<floatval($this->settings['minimum-cost'])) {
						$cost=$this->settings['minimum-cost'];
					}
					if ($not_standard===false) {
						$this->add_rate(array(
							'id' => $this->id,
							'label' =>__('Standard (IVA esclusa)', 'shipping-plugin'),
							'cost' => $cost
							));
					}
					if ($this->settings['enable-takeaway']==='yes') {
						$this->add_rate( array(
							'id'    => 'take-away',
							'label' => __( 'Ritiro in sede ' . '<span class="take-away-info">'
							               . $this->settings['take-away-info'] . '</span>', 'shipping-plugin' ),
							'cost'  => floatval( $this->settings['take-away-cost'] )
						) );
					}
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
		load_plugin_textdomain( 'shipping-plugin');
		$country = WC()->countries->countries[WC()->session->get('customer')['shipping_country']];
		$allowed_countries = WC()->countries->get_allowed_countries();
		if (!in_array($country,$allowed_countries)) {
			$message =__('Il paese che hai selezionato non è al momento disponibile. Contattaci per ulteriori informazioni sulle spedizioni.','shipping-plugin');
			$messageType = "error";
			if (!wc_has_notice($message, $messageType)) {
				wc_add_notice($message, $messageType);
			}
		}


	}
	add_action('woocommerce_review_order_before_cart_contents', 'validate_order', 10);
	add_action('woocommerce_after_checkout_validation', 'validate_order', 10);
}