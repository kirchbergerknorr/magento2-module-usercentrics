<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\Usercentrics\Processor;

use Magento\Framework\View\Element\AbstractBlock;
use Snk\Usercentrics\{
    Block\Adminhtml\System\Config\Form\Field\Selectors\Type,
    Helper\Config
};

class HtmlProcessor
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get cookie group (service name) for current block if available
     *
     * @param AbstractBlock $block
     * @param string $html
     * @return false|string
     */
    private function getCookieGroup($block, $html)
    {
        if (empty($html)) {
            return false;
        }

        $selectors = $this->config->getSelectors();

        foreach ($selectors[Type::SELECTOR_REGEX] ?? [] as $regex => $cookieGroup) {
            if (!$this->isRegexValid($regex)) {
                continue;
            }

            if (preg_match($regex, $html)) {
                return $cookieGroup;
            }
        }

        return $selectors[Type::SELECTOR_BLOCK][trim($block->getNameInLayout())]
            ?? $selectors[Type::SELECTOR_TEMPLATE][trim($block->getTemplate())]
            ?? false;
    }

    /**
     * Find scripts tags in given html and update them
     *
     * @param AbstractBlock $block
     * @param string $html
     * @return string
     */
    public function process(AbstractBlock $block, $html)
    {
        $cookieGroup = $this->getCookieGroup($block, $html);

        if (!$cookieGroup) {
            return $html;
        }

        // wrap html because DOMDocument needs a root node
        $html = '<html>'.  $html . '</html>';

        $domDocument = new \DOMDocument();
        libxml_use_internal_errors(true);
        $domDocument->loadHTML(
            mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $scriptTags = $domDocument->getElementsByTagName('script');
        if ($scriptTags->length) {
            /** @var \DOMElement $scriptTag */
            foreach ($scriptTags as $scriptTag) {
                $this->updateScriptTag($scriptTag, $cookieGroup);
            }

            $html = $domDocument->saveHTML();
        }

        // remove the root node
        return str_replace(['<html>', '</html>'], '', $html);
    }

    /**
     * Change type and add usercentrics group (service name) attribute
     *
     * @param \DOMElement $scriptTag
     * @param string $cookieGroup
     * @return void
     */
    private function updateScriptTag(\DOMElement $scriptTag, $cookieGroup)
    {
        // exclude x-magento-init because usercentrics cannot set it back
        if ($scriptTag->getAttribute('type') !== 'text/x-magento-init') {
            $scriptTag->setAttribute('type', 'text/plain');
            $scriptTag->setAttribute('data-usercentrics', $cookieGroup);
        }
    }

    /**
     * @param string $regex
     * @return bool
     */
    private function isRegexValid($regex): bool
    {
        // phpcs:disable Generic.PHP.NoSilencedErrors.Discouraged
        return @preg_match($regex, null) !== false;
        // phpcs:enable Generic.PHP.NoSilencedErrors.Discouraged
    }
}
