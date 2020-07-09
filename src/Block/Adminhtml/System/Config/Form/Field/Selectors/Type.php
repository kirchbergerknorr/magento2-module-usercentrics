<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Snk\Usercentrics\Block\Adminhtml\System\Config\Form\Field\Selectors;

use Magento\Framework\View\Element\Html\Select;

class Type extends Select
{
    const SELECTOR_BLOCK = 'block';
    const SELECTOR_TEMPLATE = 'template';
    const SELECTOR_REGEX = 'regex';

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $options = [
                self::SELECTOR_BLOCK    => 'Block Name',
                self::SELECTOR_TEMPLATE => 'Template Name',
                self::SELECTOR_REGEX    => 'Regex',
            ];

            foreach ($options as $value => $label) {
                $this->addOption($value, __($label));
            }
        }
        return parent::_toHtml();
    }
}
