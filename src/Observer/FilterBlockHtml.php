<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Snk\Usercentrics\Observer;

use Magento\Framework\{
    Event\Observer,
    Event\ObserverInterface
};
use Snk\Usercentrics\{
    Helper\Config,
    Processor\HtmlProcessor
};

class FilterBlockHtml implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var HtmlProcessor
     */
    private $htmlProcessor;

    public function __construct(Config $config, HtmlProcessor $htmlProcessor)
    {
        $this->config = $config;
        $this->htmlProcessor = $htmlProcessor;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $transport = $observer->getTransport();
        $transport->setHtml($this->htmlProcessor->process(
            $observer->getBlock(),
            $transport->getHtml()
        ));
    }
}
