/*------------------------------
*
* WooCommerce Subscriptions Direct Cart / Checkout Switch
*
----------------------------------*/
if ( class_exists( 'WC_Subscriptions_Switcher' ) ) {
// Record direct subscription switching in the cart
add_action( 'woocommerce_add_cart_item_data', 'set_direct_switch_details_in_cart', 10, 3 );

/**
* When a subscription switch is added to the cart, store a record of pertinent meta about the switch.
*/
function set_direct_switch_details_in_cart( $cart_item_data, $product_id, $variation_id ) {

        try {
            if ( ! isset( $_GET['direct-switch-subscription'] ) ) {
                return $cart_item_data;
            }

			$user_id = get_current_user_id();
    		$has_sub = wcs_user_has_subscription( $user_id, '', 'active' );
			if ( $has_sub) {
    			$subscriptions = wcs_get_users_subscriptions( $user_id );
    			//$results = '<pre>' . print_r($subscriptions, true) . '</pre>';
    			$results = '';
    			foreach ($subscriptions as $subscription) {
    				if( $subscription->has_status('active') ) {
    					foreach ($subscription->get_items() as $item_id => $item ) {
    			   			//$results .= '<br>Item ID: '.$item_id;
    			   			//$results .= '<pre>' . print_r($item, true) . '</pre>';
    					}
    					break;
    				}
    			}
			} else {
				wc_add_notice( __( 'You do not have an active subscription to switch.', 'woocommerce-subscriptions' ), 'error' );
                WC()->cart->empty_cart( true );
                wp_redirect( get_permalink( wc_get_page_id( 'cart' ) ) );
                exit();
			}

            // Requesting a switch for someone elses subscription
            if ( ! current_user_can( 'switch_shop_subscription', $subscription->get_id() ) ) {
                wc_add_notice( __( 'You can not switch this subscription. It appears you do not own the subscription.', 'woocommerce-subscriptions' ), 'error' );
                WC()->cart->empty_cart( true );
                wp_redirect( get_permalink( $subscription['product_id'] ) );
                exit();
            }

            // Else it's a valid switch
            $product         = wc_get_product( $item['product_id'] );
            $parent_products = WC_Subscriptions_Product::get_parent_ids( $product );
            $child_products  = array();

            if ( ! empty( $parent_products ) ) {
                foreach ( $parent_products as $parent_id ) {
                    $child_products = array_unique( array_merge( $child_products, wc_get_product( $parent_id )->get_children() ) );
                }
            }

            if ( $product_id != $item['product_id'] && ! in_array( $item['product_id'], $child_products ) ) {
                return $cart_item_data;
            }

            $next_payment_timestamp = $subscription->get_time( 'next_payment' );

            // If there are no more payments due on the subscription, because we're in the last billing period, we need to use the subscription's expiration date, not next payment date
            if ( false == $next_payment_timestamp ) {
                $next_payment_timestamp = $subscription->get_time( 'end' );
            }

            $cart_item_data['subscription_switch'] = array(
                'subscription_id'         => $subscription->get_id(),
                'item_id'                 => absint( $item_id ),
                'next_payment_timestamp'  => $next_payment_timestamp,
                'upgraded_or_downgraded'  => '',
            );

            return $cart_item_data;

        } catch ( Exception $e ) {

            wc_add_notice( __( 'There was an error locating the switch details.', 'woocommerce-subscriptions' ), 'error' );
            WC()->cart->empty_cart( true );
            wp_redirect( get_permalink( wc_get_page_id( 'cart' ) ) );
            exit();
        }
}
}
