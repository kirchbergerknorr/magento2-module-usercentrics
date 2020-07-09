<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Snk\Usercentrics\Block;

use Magento\Framework\View\Element\Template;
use Snk\Usercentrics\Helper\Config;

class UsercentricsJs extends Template
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getSettingsId() :string
    {
        return $this->config->getSettingsID();
    }

    /**
     * @return string
     */
    public function getScriptUrl() :string
    {
        return $this->config->getScriptUrl();
    }
}
