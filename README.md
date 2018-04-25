# woocommerce-subscriptions-direct-cart-switch
WooCommerce Subscriptions Direct Cart / Checkout Switch

Allows you to skip the Upgrade / Downgrade process on the My Subscriptions page to create more of a direct switch process.

add this snippet to your Child Theme functions.php

Once added you must have subscription products setup according to the switching guide:
https://docs.woocommerce.com/document/subscriptions/switching-guide/

Then you can create direct cart / checkout links like this to place anywhere on your site:

Cart Example of upgrading to a Subscription in a Grouped Product:

https://yourstore.com/cart/?add-to-cart=40586&quantity[24167]=1&direct-switch-subscription=true

Checkout Example of Upgrading to a Subscription in a Grouped Product:

https://yourstore.com/checkout/?add-to-cart=40586&quantity[24167]=1&direct-switch-subscription=true

add-to-cart=xxxxx   This is the Product ID of the Grouped Product

quantity[xxxx]=1    This is the Product ID of the Simple Subscription you are changing to

direct-switch-subscription=true   This must be added to trigger this new function

Direct Checkout links thanks to this resources: https://businessbloomer.com/woocommerce-custom-add-cart-urls-ultimate-guide/
