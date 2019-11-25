<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_OneStepCheckout
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OneStepCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\GiftMessage\Model\CompositeConfigProvider as GiftMessageConfig;
use Magento\Framework\Serialize\Serializer\Json;
use Bss\OneStepCheckout\Helper\Config;
use Bss\OneStepCheckout\Helper\Data;
use Magento\Framework\View\LayoutInterface;
use Bss\OneStepCheckout\Model\ResourceModel\CompositeConfig;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

/**
 * Class CompositeConfigProvider
 *
 * @package Bss\OneStepCheckout\Model
 */
class CompositeConfigProvider implements ConfigProviderInterface
{
    /**
     * OSC config helper.
     *
     * @var Config
     */
    private $configHelper;

    /**
     * @var GiftMessageConfig
     */
    private $configProvider;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var CompositeConfig
     */
    private $compositeConfig;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * CompositeConfigProvider constructor.
     * @param Config $configHelper
     * @param GiftMessageConfig $configProvider
     * @param Json $serializer
     * @param Data $helperData
     * @param LayoutInterface $layout
     * @param CompositeConfig $compositeConfig
     * @param RemoteAddress $remoteAddress
     * @param Curl $curl
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $configHelper,
        GiftMessageConfig $configProvider,
        Json $serializer,
        Data $helperData,
        LayoutInterface $layout,
        CompositeConfig $compositeConfig,
        RemoteAddress $remoteAddress,
        Curl $curl,
        LoggerInterface $logger
    ) {
        $this->configHelper = $configHelper;
        $this->configProvider = $configProvider;
        $this->serializer = $serializer;
        $this->helperData = $helperData;
        $this->layout = $layout;
        $this->compositeConfig = $compositeConfig;
        $this->remoteAddress = $remoteAddress;
        $this->curl = $curl;
        $this->logger = $logger;
    }

    /**
     * Get Config
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getConfig()
    {
        $output = [];
        $helper = $this->configHelper;
        $version = $this->helperData->getVersion();
        if ($helper->isEnabled()) {
            if ($helper->isGiftMessageField('enable_gift_message') && $helper->isMessagesAllowed()) {
                $config['giftOptionsConfig'] = $this->getGiftOptionsConfigJson();
            }
            $config['googleApiAutoComplete'] = $helper->isAutoComplete();
            if ($api = $helper->getAutoCompleteGroup('google_api_key')) {
                $config['googleApi'] = $api;
            }
            $config['googleApiListCountries'] = $this->compositeConfig->getCountryHasRegion();
            $ipCustomer = $this->remoteAddress->getRemoteAddress();
            if ($ipCustomer) {
                $customerCountryCode = $this->getCountryByIpInfo($ipCustomer);
                if ($customerCountryCode) {
                    $config['googleApiCustomerCountry'] = $customerCountryCode;
                }
            }
            if ($helper->getAutoCompleteGroup('allowspecific')) {
                $countries = explode(',', $helper->getAutoCompleteGroup('specificcountry'));
                $config['specificcountry'] = $countries;
            }
            if ($helper->isAutoCreateNewAccount()) {
                $config['autoCreateNewAccount']['enable'] = true;
            } else {
                $config['autoCreateNewAccount']['enable'] = false;
            }
            $config['autoCreateNewAccount']['minLength'] = $this->helperData->getPassPasswordMinLeng();
            $config['autoCreateNewAccount']['minCharacterSets'] = $this->helperData->getPassPasswordMinCharacterSets();
            $output['bssOsc'] = $config;
            $giftWrapFee = $this->configHelper->getGiftWrapFee();

            if ($giftWrapFee !== false) {
                $giftWrapType = $this->configHelper->getGiftWrap('type');
                $giftWrapLabel = $this->helperData->getGiftWrapLabel($this->configHelper->getGiftWrap('fee'), $giftWrapType);
                $output['bssOsc']['giftwrap'] = $giftWrapLabel;
            }
            $output['magento_version'] = $version;
            if ($version >= "2.3.1") {
                $output['paypal_in_context'] = $this->helperData->isPayPalContext();
            } else {
                $output['paypal_in_context'] = false;
            }
            $output['oscWidget'] = $this->getOscWidget();
        }
        return $output;
    }

    /**
     * Retrieve gift message configuration
     *
     * @return string
     */
    private function getGiftOptionsConfigJson()
    {
        return $this->configProvider->getConfig();
    }

    /**
     * Get Osc Widget
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getOscWidget()
    {
        $widgetList = $this->compositeConfig->getOscWidget();
        $result = [];
        $isset = [];
        foreach ($widgetList as $widget) {
            $widgetParams = $this->serializer->unserialize($widget['widget_parameters']);
            if (isset($widgetParams['block_id']) &&
                !isset($isset[$widget['block_reference']][$widgetParams['block_id']])
            ) {
                $isset[$widget['block_reference']][$widgetParams['block_id']] = $widgetParams['block_id'];
                $result[$widget['block_reference']][] =
                    $this->layout->createBlock(\Magento\Cms\Block\Block::class)
                        ->setBlockId($widgetParams['block_id'])->toHtml();
            }
        }
        return $result;
    }

    /**
     * Get Country
     *
     * @param string $ipCustomer
     * @return mixed|null
     */
    protected function getCountryByIpInfo($ipCustomer)
    {
        $countryCode = false;
        try {
            $url = 'http://ipinfo.io/' . $ipCustomer . '/json';
            $this->curl->get($url);
            $response = $this->serializer->unserialize($this->curl->getBody(), true);
            if (is_array($response) && isset($response['country'])) {
                $countryCode = $response['country'];
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return $countryCode;
    }
}
