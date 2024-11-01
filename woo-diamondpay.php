<?php
/*
Plugin Name: woo-diamondpay
Plugin URI: http://tasveeq.com
Description: Add Diamond Payment Gateways for WooCommerce. 
Version: 1.0.0
Author: Tasveeq IT Solutions
Author URI: http://tasveeq.com
*/

function init_diamond(){
  function add_diamond_gateway_class( $methods ) {
	$methods[] = 'WC_Gateway_Diamond'; 
	return $methods;
  }
  add_filter( 'woocommerce_payment_gateways', 'add_diamond_gateway_class' );
  if(class_exists('WC_Payment_Gateway')){
	
	class WC_Gateway_Diamond extends WC_Payment_Gateway {
	  
	  public function __construct(){
		$this->id               = 'diamond';
		$this->icon             = apply_filters( 'woocommerce_diamond_icon', plugins_url( 'images/diamondlogo.png' , __FILE__ ) );
		$this->has_fields       = true;
		$this->method_title     = 'DiamondPay';		
		$this->init_form_fields();
		$this->init_settings();
		$this->title              	  = $this->get_option( 'title' );
		$this->diamond_merchantId    = $this->get_option( 'diamond_merchantId' );
		
		define("diamond_merchantId", $this->diamond_merchantId); 
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	  }
	  
	  public function admin_options(){
		?>
		<h3><?php _e( 'DiamondPay', 'woocommerce' ); ?></h3>
		<p><?php _e( 'DiamondPay is one of the best Paymeny gateway', 'woocommerce' ); ?></p>
		<table class="form-table">
			  <?php $this->generate_settings_html(); ?>
		</table>
		<?php
	  }
	  
	  public function init_form_fields(){
		$this->form_fields = array(
			'enabled' => array(
			  'title' => __( 'Enable/Disable', 'woocommerce' ),
			  'type' => 'checkbox',
			  'label' => __( 'Enable DiamondPay', 'woocommerce' ),
			  'default' => 'yes'
			  ),
			'title' => array(
			  'title' => __( 'Title', 'woocommerce' ),
			  'type' => 'text',
			  'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
			  'default' => __( 'DiamondPay', 'woocommerce' ),
			  'desc_tip'      => true,
			  ),
			'diamond_merchantId' => array(
			  'title' => __( 'Merchant ID', 'woocommerce' ),
			  'type' => 'text',
			  'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
			  'default' => '',
			  'desc_tip'      => true,
			  'placeholder' => 'Your Merchant ID'
			  )
		  );
	  }
	  
	  public function payment_fields(){ ?>
      <?php }
	  
		public function process_payment( $order_id ){
			global $woocommerce;
			$wc_order = new WC_Order( $order_id );
			$grand_total = $wc_order->order_total;
			$email = $wc_order->billing_email;
			$amount = (int)$grand_total;
			$diamondpay_merchant_id = $this->diamond_merchantId;
			echo '<form method="POST" id="upay_form" name="upay_form" action="https://cipg.diamondbank.com/cipg/MerchantServices/MakePayment.aspx" target="_top">
				<input type="hidden" name="mercId" value="'.$diamondpay_merchant_id.'">
				<input type="hidden" name="currCode" value="566">
				<input type="hidden" name="amt" value="'.$amount.'">
				<input type="hidden" name="orderId" value="'.$order_id.'">
				<input type="hidden" name="prod" value="'.$order_id.'">
				<input type="hidden" name="email" value="'.$email.'">
				<input type="submit" name="submit" id="submit_diamondpay" value="Pay" style="display:none;">
			</form>';
			echo '<script> document.getElementById("submit_diamondpay").click(); </script>';
		}
	}
  }
}

add_action( 'plugins_loaded', 'init_diamond' );
