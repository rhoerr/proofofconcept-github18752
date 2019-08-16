<?php

namespace ProofOfConcept\Github18752\Observer;

/**
 * TriggerObserver Class
 */
class TriggerObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * TriggerObserver constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $connection = $this->resource->getConnection();
        
        /**
         * Preconditions: Magento 2.2.4+ and guest checkout (tested with sample data and purchaseorder payment, but any will do)
         *
         * These two lines will trigger exception log:
         * 	"An exception occurred on 'sales_model_service_quote_submit_failure' event: Rolled back transaction has not been completed correctly."
         * And checkout error message:
         * 	"An error occurred on the server. Please try to place the order again."
         */
        $connection->beginTransaction();
        $connection->rollBack();
    }
}
