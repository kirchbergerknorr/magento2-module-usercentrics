<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/MIT          MIT License
 */

namespace Snk\Usercentrics\Test\Unit\Processor;

use Magento\Framework\{
    App\Config\ScopeConfigInterface,
    TestFramework\Unit\Helper\ObjectManager,
    View\Element\AbstractBlock
};
use PHPUnit\Framework\TestCase;
use Snk\Usercentrics\{
    Block\Adminhtml\System\Config\Form\Field\Selectors\Type,
    Helper\Config,
    Processor\HtmlProcessor
};

class HtmlProcessorTest extends TestCase
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

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->configHelper = $this->createMock(Config::class);
        $this->configHelper->method('getSelectors')->willReturn($this->getSelectors());

        $this->htmlProcessor = $this->objectManager->getObject(HtmlProcessor::class, [
            'config' => $this->configHelper
        ]);
    }

    /**
     * @dataProvider dataToFilterDataProvider
     * @param $block
     * @param $html
     * @return void
     */
    public function testProcessWithDataToFilter($block, $html)
    {
        $resultHtml = $this->htmlProcessor->process($block, $html);

        $this->assertContains('type="text/plain"', $resultHtml);
        $this->assertContains('data-usercentrics', $resultHtml);
    }

    /**
     * @dataProvider dataToSkipDataProvider
     * @param $block
     * @param $html
     * @return void
     */
    public function testProcessWithDataToSkip($block, $html)
    {
        $resultHtml = $this->htmlProcessor->process($block, $html);

        $this->assertNotContains('type="text/plain"', $resultHtml);
        $this->assertNotContains('data-usercentrics', $resultHtml);
    }

    /**
     * @return array[]
     */
    public function getSelectors()
    {
        return [
            Type::SELECTOR_TEMPLATE => [
                'Magento_OtherModule::header/script.phtml' => 'Cookie Group 1',
                'Magento_Bloh::header/template.phtml'      => 'Cookie Group 2',
            ],
            Type::SELECTOR_BLOCK    => [
                'some-valid-name' => 'Cookie Group 1',
                'some-kinda-name' => 'Cookie Group 3',
            ],
            Type::SELECTOR_REGEX    => [
                '/src=".*goggle.com/' => 'Cookie Group 4',
                'some wrong regex'    => 'Cookie Group 2',
            ],
            'some invalid key'
        ];
    }

    /**
     * @return array[]
     */
    public function dataToFilterDataProvider()
    {
        return [
            [
                $this->getBlockMock(
                    'some-valid-name ',
                    ' Magento_Module::page/script.phtml'
                ),
                '<script type="text/javascript">
                    if (someFunction()) {
                        //call some other stuff
                        call_csomer_otehrStuff();
                    }
                </script>'
            ],
            [
                $this->getBlockMock(
                    'some_other_name',
                    ' Magento_OtherModule::header/script.phtml '
                ),
                '<div class="test">
                    <script>
                        if (someOtherFunction()) {
                            //call some other stuff
                            call_csomer_otehrStuff();
                        }
                    </script>
                </div>'
            ],
            [
                $this->getBlockMock(
                    'one.more.Name',
                    'Custom_Module::page/header/scripts/script.phtml'
                ),
                '<div class="test"></div>
                <div class="some other class">
                    <script src="https://goggle.com">
                        if (someFunction()) {
                            //call some other stuff
                            call_csomer_otehrStuff();
                        }
                    </script>
                </div>'
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function dataToSkipDataProvider()
    {
        return [
            [
                $this->getBlockMock(
                    'skip-valid-name',
                    'Magento_SkipModule::page/script.phtml'
                ),
                '<div class="test">
                    <script>
                        if (isTheWorldJust()) {
                            //call some other stuff
                            call_csomer_otehrStuff();
                        }
                    </script>
                </div>'
            ],
            [
                $this->getBlockMock(
                    'skip_other_name',
                    'Magento_SkipOtherModule::header/script.phtml'
                ),
                '<div class="test">
                    <script>
                        if (someFunction()) {
                            //call some other stuff
                            call_csomer_otehrStuff();
                        }
                    </script>
                </div>'
            ],
            [
                $this->getBlockMock(
                    'skip.one.more.Name',
                    'Custom_SkipModule::page/header/scripts/script.phtml'
                ),
                '<div class="test">
                    <span>Here is no script tag</span>
                </div>'
            ],
        ];
    }

    /**
     * @param $layoutName
     * @param $template
     * @return AbstractBlock|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getBlockMock($layoutName, $template)
    {
        $block = $this->getMockBuilder(AbstractBlock::class)
                      ->disableOriginalConstructor()
                      ->setMethods(['getTemplate', 'getNameInLayout'])
                      ->getMock();
        $block->method('getNameInLayout')->willReturn($layoutName);
        $block->method('getTemplate')->willReturn($template);

        return $block;
    }
}
