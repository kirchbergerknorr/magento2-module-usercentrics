<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2020 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Snk\Usercentrics\Test\Unit\Helper;

use Magento\Framework\{
    App\Config\ScopeConfigInterface,
    Serialize\SerializerInterface,
    TestFramework\Unit\Helper\ObjectManager
};
use PHPUnit\Framework\TestCase;
use Snk\Usercentrics\Helper\Config;

class ConfigTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var object
     */
    private $configHelper;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);

        $this->configHelper = $this->objectManager->getObject(Config::class, [
            'config'     => $this->scopeConfig,
            'serializer' => $this->serializer
        ]);
    }

    /**
     * @dataProvider configValueProvider
     * @return void
     */
    public function testIsEnabled($value)
    {
        $this->scopeConfig->method('getValue')->willReturn($value);
        $this->assertInternalType('bool', $this->configHelper->isEnabled());
    }

    /**
     * @dataProvider configValueProvider
     * @return void
     */
    public function testGetSettingsId($value)
    {
        $this->scopeConfig->method('getValue')->willReturn($value);
        $this->assertInternalType('string', $this->configHelper->getSettingsID());
    }

    /**
     * @dataProvider configValueProvider
     * @return void
     */
    public function testGetScriptUrl($value)
    {
        $this->scopeConfig->method('getValue')->willReturn($value);
        $this->assertInternalType('string', $this->configHelper->getScriptUrl());
    }

    /**
     * @return void
     */
    public function testGetSelectors()
    {
        $this->serializer->method('unserialize')->willReturn(
            false,
            null,
            ['some' => 'key', 'blo' => ['some other', 6]],
            [['type' => 'regex', 'selector' => 'ololo', 'name' => 'group']]
        );

        $selectors = $this->configHelper->getSelectors();
        $this->assertInternalType('array', $selectors);
        $this->assertEmpty($selectors);

        $selectors = $this->configHelper->getSelectors();
        $this->assertInternalType('array', $selectors);
        $this->assertEmpty($selectors);

        // invalid array
        $selectors = $this->configHelper->getSelectors();
        $this->assertInternalType('array', $selectors);
        $this->assertEmpty($selectors);

        // valid array
        $selectors = $this->configHelper->getSelectors();
        $this->assertInternalType('array', $selectors);
        $this->assertNotEmpty($selectors);
    }

    /**
     * @return void
     */
    public function testGetSelectorsException()
    {
        $this->serializer->method('unserialize')->willThrowException(
            new \InvalidArgumentException()
        );

        $selectors = $this->configHelper->getSelectors();

        $this->assertEmpty($selectors);
        $this->assertInternalType( 'array', $selectors);
    }

    /**
     * @return array
     */
    public function configValueProvider()
    {
        return [
            [false],
            [true],
            [null],
            ['somestring'],
            ['{"some":"key"}'],
            [['array']],
        ];
    }
}
