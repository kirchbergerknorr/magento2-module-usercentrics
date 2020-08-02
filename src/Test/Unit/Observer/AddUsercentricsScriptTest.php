<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\Usercentrics\Test\Unit\Observer;

use Magento\Framework\{
    DataObject,
    Event\Observer,
    Event\ObserverInterface,
    TestFramework\Unit\Helper\ObjectManager,
    View\Element\AbstractBlock
};
use PHPUnit\Framework\TestCase;
use Snk\Usercentrics\{
    Helper\Config,
    Observer\AddUsercentricsScript
};

class AddUsercentricsScriptTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var object
     */
    private $addUsercentrisScriptObserver;

    /**
     * @var DataObject
     */
    private $transport;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->configHelper = $this->createMock(Config::class);
        $this->transport = $this->getMockBuilder(DataObject::class)
             ->setMethods(['setHtml'])
             ->getMock();

        $this->addUsercentrisScriptObserver = $this->objectManager->getObject(AddUsercentricsScript::class, [
            'config'     => $this->configHelper
        ]);
    }

    /**
     * @return void
     */
    public function testExecuteEnabled()
    {
        $this->configHelper->method('isEnabled')->willReturn(true);
        $this->transport->expects($this->once())->method('setHtml');

        $this->addUsercentrisScriptObserver->execute($this->getObserverMock());
    }

    /**
     * @return void
     */
    public function testExecuteDisabled()
    {
        $this->configHelper->method('isEnabled')->willReturn(false);
        $this->transport->expects($this->never())->method('setHtml');

        $this->addUsercentrisScriptObserver->execute($this->getObserverMock());
    }

    /**
     * @return Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getObserverMock()
    {
        $observer = $this->getMockBuilder(Observer::class)
                         ->setMethods(['getTransport', 'getBlock'])
                         ->getMock();

        $block = $this->createMock(AbstractBlock::class);
        $block->method('getNameInLayout')->willReturn(AddUsercentricsScript::TARGET_BLOCK_NAME);

        $observer->method('getTransport')->willReturn($this->transport);
        $observer->method('getBlock')->willReturn($block);

        return $observer;
    }
}
