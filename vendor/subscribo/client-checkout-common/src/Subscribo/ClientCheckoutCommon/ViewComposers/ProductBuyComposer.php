<?php

namespace Subscribo\ClientCheckoutCommon\ViewComposers;

use Exception;
use RuntimeException;
use Subscribo\Api1Connector\Connectors\AccountConnector;
use Subscribo\Api1Connector\Connectors\BusinessConnector;
use Subscribo\Api1Connector\Connectors\TransactionConnector;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\ClientCheckoutCommon\Http\Processors\CheckoutProcessor;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;

/**
 * Class ProductBuyComposer
 * @package Subscribo\ClientCheckoutCommon
 */
class ProductBuyComposer
{
    /**
     * @var AccountConnector
     */
    protected $accountConnector;

    /**
     * @var BusinessConnector
     */
    protected $businessConnector;

    /**
     * @var TransactionConnector
     */
    protected $transactionConnector;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Guard
     */
    protected $auth;

    /**
     * @var LocalizerInterface
     */
    protected $localizer;

    /**
     * @var Store
     */
    protected $session;

    /**
     * @param AccountConnector $accountConnector
     * @param BusinessConnector $businessConnector
     * @param TransactionConnector $transactionConnector
     * @param LocalizerInterface $localizer
     * @param Request $request
     * @param Guard $auth
     * @param Store $session
     */
    public function __construct(
        AccountConnector $accountConnector,
        BusinessConnector $businessConnector,
        TransactionConnector $transactionConnector,
        LocalizerInterface $localizer,
        Request $request,
        Guard $auth,
        Store $session
    ) {
        $this->accountConnector = $accountConnector;
        $this->businessConnector = $businessConnector;
        $this->transactionConnector = $transactionConnector;
        $this->request = $request;
        $this->auth = $auth;
        $this->localizer = $localizer;
        $this->session = $session;
    }

    /**
     * @param View $view
     * @throws \RuntimeException
     */
    public function compose(View $view)
    {
        try {
            $products = $this->businessConnector->getProduct();
            $id = $this->request->route('productId');
            $oldItems = $this->request->old('item');
            $selectedProduct = static::pickProductById($products, $id);
            if ($selectedProduct and ! isset($oldItems[$selectedProduct['price_id']])) {
                $oldItems[$selectedProduct['price_id']] = 1;
            }
            $subscriptionPeriods = $this->businessConnector->getSubscriptionPeriods();

            $noSubsTrId = 'forms.buy.subscriptionPeriod.select.noSubscription';
            $localizer = $this->localizer->template('messages', 'client-checkout-common');
            $subscriptionPeriods[CheckoutProcessor::NO_SUBSCRIPTION_OPTION] = $localizer->trans($noSubsTrId);


            $transactionGateways = $this->transactionConnector->getGateway();
            $addresses = $this->auth->user() ? $this->accountConnector->getAddress() : [];
            $deliveries = $this->businessConnector->getAvailableDeliveries();
            $usualDeliveryWindowTypes = $this->businessConnector->getUsualDeliveryWindowTypes();
            $deliveryWindowTypes = [];
            foreach ($usualDeliveryWindowTypes as $deliveryWindowType)
            {
                $deliveryWindowTypes[$deliveryWindowType['id']] = $deliveryWindowType['name'];
            }
        } catch (Exception $e) {
            throw new RuntimeException('Error in communication with API', 0, $e);
        }
        $flashMessageText = $this->session->pull(CheckoutProcessor::SESSION_KEY_BUY_PRODUCT_FLASH_MESSAGE);
        if ($flashMessageText) {
            $view->with('flashMessageText', $flashMessageText);
        }
        $view->with('ariaLabelClose', $localizer->trans('aria.label.close'));
        $view->with('products', $this->businessConnector->getProduct());
        $view->with('oldItems', $oldItems);
        $view->with('transactionGateways', $transactionGateways);
        $view->with('localizer', $localizer);
        $view->with('addresses', $addresses);
        $view->with('deliveries', $deliveries);
        $view->with('subscriptionPeriods', $subscriptionPeriods);
        $view->with('deliveryWindowTypes', $deliveryWindowTypes);
    }

    /**
     * @param array $products
     * @param $id
     * @return null|array
     */
    protected static function pickProductById(array $products, $id)
    {
        if (empty($id)) {

            return null;
        }
        $stringId = strval($id);
        foreach ($products as $product) {
            if (strval($product['id']) === $stringId) {

                return $product;
            }
        }

        return null;
    }
}
