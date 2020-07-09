<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Snk\Usercentrics\Test\Unit\Observer;

use Magento\Framework\{
    App\Config\ScopeConfigInterface,
    Event\Observer,
    TestFramework\Unit\Helper\ObjectManager,
    View\Element\AbstractBlock
};
use PHPUnit\Framework\TestCase;
use Snk\Usercentrics\{
    Helper\Config,
    Observer\FilterBlockHtml,
    Processor\HtmlProcessor
};

class FilterBlockHtmlTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $configHelper;

    /**
     * @var HtmlProcessor
     */
    private $htmlProcessor;
    /**
     * @var object
     */
    private $filterHtmlObserver;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->configHelper = $this->createMock(Config::class);
        $this->htmlProcessor = $this->createMock(HtmlProcessor::class);

        $this->filterHtmlObserver = $this->objectManager->getObject(FilterBlockHtml::class, [
            'config'        => $this->configHelper,
            'htmlProcessor' => $this->htmlProcessor
        ]);
    }

    /**
     * @return void
     */
    public function testExecuteEnabled()
    {
        $this->configHelper->method('isEnabled')->willReturn(true);
        $this->htmlProcessor->expects($this->once())->method('process');

        $this->filterHtmlObserver->execute($this->getObserverMock());
    }

    /**
     * @return void
     */
    public function testExecuteDisabled()
    {
        $this->configHelper->method('isEnabled')->willReturn(false);
        $this->htmlProcessor->expects($this->never())->method('process');

        $this->filterHtmlObserver->execute($this->getObserverMock());
    }

    /**
     * @return Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getObserverMock()
    {
        $transport = $this->getMockBuilder(\Magento\Framework\DataObject::class)
                          ->setMethods(['getHtml'])
                          ->getMock();
        $transport->method('getHtml')->willReturn('somestring');
        $observer = $this->getMockBuilder(Observer::class)
                         ->setMethods(['getTransport', 'getBlock'])
                         ->getMock();
        $observer->method('getTransport')->willReturn($transport);
        $observer->method('getBlock')->willReturn($this->createMock(AbstractBlock::class));

        return $observer;
    }
}
