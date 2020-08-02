<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Snk\Usercentrics\Model\Config\Backend\Serialized;

class ArraySerialized extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * Skip saving data if the module is not enabled
     *
     * @return ArraySerialized
     */
    public function beforeSave()
    {
        if (!$this->getFieldsetDataValue('enable')) {
            $this->setData('value', $this->getOldValue());

            return $this;
        }
        return parent::beforeSave();
    }
}
