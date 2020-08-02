<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\Usercentrics\Observer;

use Magento\Framework\{
    Event\Observer,
    Event\ObserverInterface,
    View\Element\AbstractBlock
};
use Snk\Usercentrics\Helper\Config;

class AddUsercentricsScript implements ObserverInterface
{
    const TARGET_BLOCK_NAME = 'require.js';

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
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

        /** @var AbstractBlock $block */
        $block = $observer->getBlock();
        if ($block->getNameInLayout() === self::TARGET_BLOCK_NAME) {
            $transport = $observer->getTransport();
            $transport->setHtml(
                $transport->getHtml() . $block->getChildHtml('usercentrics.js')
            );
        }
    }
}
