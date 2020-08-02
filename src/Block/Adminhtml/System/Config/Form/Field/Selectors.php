<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\Usercentrics\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Snk\Usercentrics\Block\Adminhtml\System\Config\Form\Field\Selectors\Type;

class Selectors extends AbstractFieldArray
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'name',
            [
                'label' => __('Cookie Group Name'),
                'style' => 'width:150px',
            ]
        );
        $this->addColumn(
            'type',
            [
                'label'    => __('Type'),
                'style'    => 'width:150px',
                'renderer' => $this->getLayout()->createBlock(Type::class),
            ]
        );
        $this->addColumn(
            'selector',
            [
                'label' => __('Value'),
                'style' => 'width:150px',
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
