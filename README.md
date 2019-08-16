# ProofOfConcept_Github18752

Proof of concept bug reproduction for the https://github.com/magento/magento2/issues/18752 github issue.

### To install:

    composer config repositories.poc18752 git https://github.com/rhoerr/proofofconcept-github18752.git
    composer require rhoerr/proofofconcept-github18752:master
    bin/magento module:enable ProofOfConcept_Github18752
    bin/magento setup:upgrade


### Preconditions:

 * Magento 2.2.4+
 * Guest checkout (because the triggering code exists in `\Magento\Checkout\Model\GuestPaymentInformationManagement` but not `\Magento\Checkout\Model\PaymentInformationManagement`)

I tested with sample data and purchaseorder payment method, but any will do.


### What's going on:

This POC adds an observer on `sales_order_place_after`. This observer creates a DB transaction and then immediately triggers rollback. This demonstrates Magento's inability to handle nested transaction rollbacks.


#### Expected behavior:

Checkout should complete successfully, since no exception was thrown by the observer.


#### Actual behavior:

The two observer lines will trigger this error (from exception.log):

	[2019-08-16 13:16:28] main.CRITICAL: An exception occurred on 'sales_model_service_quote_submit_failure' event: Rolled back transaction has not been completed correctly. {"exception":"[object] (Exception(code: 0): An exception occurred on 'sales_model_service_quote_submit_failure' event: Rolled back transaction has not been completed correctly. at /var/www/vhosts/mag23x.paradoxlabs-dev.com/httpdocs/vendor/magento/module-quote/Model/QuoteManagement.php:665, Exception(code: 0): Rolled back transaction has not been completed correctly. at /var/www/vhosts/mag23x.paradoxlabs-dev.com/httpdocs/vendor/magento/framework/DB/Adapter/Pdo/Mysql.php:278)"} []

And the following checkout error message:

	An error occurred on the server. Please try to place the order again.

Tested on Magento 2.3.2.

#### Workaround:

Commenting out all `$salesConnection` and `$checkoutConnection` lines from `\Magento\Checkout\Model\GuestPaymentInformationManagement::savePaymentInformationAndPlaceOrder()` stops the error from happening.

