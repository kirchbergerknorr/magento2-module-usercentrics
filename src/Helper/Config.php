<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\Usercentrics\Helper;

use Magento\Framework\{
    App\Config\ScopeConfigInterface,
    Serialize\SerializerInterface
};
use Magento\Store\Model\ScopeInterface;

class Config
{
    const CONFIG_PATH = 'usercentrics/general/';

    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(ScopeConfigInterface $config, SerializerInterface $serializer)
    {
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     */
    public function isEnabled() :bool
    {
        return (bool) $this->getConfigValue('enable');
    }

    /**
     * @return bool
     */
    public function isSmartDataProtectorEnabled() :bool
    {
        return (bool) $this->getConfigValue('smart_data_protector_enable');
    }

    /**
     * @return bool
     */
    public function isPageReloadEnabled() :bool
    {
        return (bool) $this->getConfigValue('page_reload_enable');
    }

    /**
     * @return string
     */
    public function getSettingsID() :string
    {
        return (string) $this->getConfigValue('settings_id');
    }

    /**
     * @return string
     */
    public function getScriptUrl() :string
    {
        return (string) $this->getConfigValue('script_url');
    }

    /**
     * Get selectors and cookie group for block, templates or custom content
     *
     * @return array
     * [
     *     {type} => [
     *        {selector} => {cookie group}
     *     ],
     *     ...
     * ]
     */
    public function getSelectors(): array
    {
        $selectors = [];
        try {
            $selectorsRaw = $this->serializer->unserialize($this->getConfigValue('selectors'));
        } catch (\InvalidArgumentException $e) {
            return $selectors;
        }

        if (is_array($selectorsRaw)) {
            foreach ($selectorsRaw as $selectorRaw) {
                $type = $selectorRaw['type'] ?? false;
                $selector = trim($selectorRaw['selector'] ?? '');
                $name = trim($selectorRaw['name'] ?? '');

                if ($type && $selector && $name) {
                    $selectors[$type][$selector] = $name;
                }
            }
        }

        return $selectors;
    }

    /**
     * @param string $name
     * @return string|null
     */
    private function getConfigValue($name)
    {
        $value = $this->config->getValue(self::CONFIG_PATH . $name, ScopeInterface::SCOPE_STORES);
        return !is_array($value) ? $value : null;
    }
}
