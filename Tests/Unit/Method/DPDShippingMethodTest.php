<?php

namespace Oro\Bundle\DPDBundle\Tests\Unit\Method;

use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\DPDBundle\Factory\DPDRequestFactory;
use Oro\Bundle\DPDBundle\Method\DPDHandlerInterface;
use Oro\Bundle\DPDBundle\Method\DPDShippingMethod;
use Oro\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Oro\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;
use Oro\Component\Testing\Unit\EntityTrait;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DPDShippingMethodTest extends \PHPUnit\Framework\TestCase
{
    use EntityTrait;

    /**
     * @internal
     */
    const IDENTIFIER = 'dpd_1';

    /**
     * @internal
     */
    const LABEL = 'dpd_label';

    /**
     * @internal
     */
    const TYPE_IDENTIFIER = '59';

    /**
     * @internal
     */
    const ICON = 'dpd.png';

    /**
     * @var DPDRequestFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $dpdRequestFactory;

    /**
     * @var DPDShippingMethod
     */
    protected $dpdShippingMethod;

    protected function setUp(): void
    {
        /* @var DPDRequestFactory | \PHPUnit\Framework\MockObject\MockObject $priceRequestFactory */
        $this->dpdRequestFactory = $this->createMock(DPDRequestFactory::class);

        $type = $this->createMock(ShippingMethodTypeInterface::class);
        $type
            ->method('getIdentifier')
            ->willReturn(self::TYPE_IDENTIFIER);
        $type
            ->method('calculatePrice')
            ->willReturn(Price::create('1.0', 'USD'));

        $handler = $this->createMock(DPDHandlerInterface::class);
        $handler
            ->method('getIdentifier')
            ->willReturn(self::TYPE_IDENTIFIER);

        $this->dpdShippingMethod =
            new DPDShippingMethod(
                self::IDENTIFIER,
                self::LABEL,
                true,
                self::ICON,
                [$type],
                [$handler]
            );
    }

    public function testIsGrouped()
    {
        static::assertTrue($this->dpdShippingMethod->isGrouped());
    }

    public function testGetIdentifier()
    {
        static::assertEquals(self::IDENTIFIER, $this->dpdShippingMethod->getIdentifier());
    }

    public function testGetLabel()
    {
        static::assertEquals(self::LABEL, $this->dpdShippingMethod->getLabel());
    }

    public function testGetIcon()
    {
        static::assertEquals(self::ICON, $this->dpdShippingMethod->getIcon());
    }

    public function testGetTypes()
    {
        $types = $this->dpdShippingMethod->getTypes();

        static::assertCount(1, $types);
        static::assertEquals(self::TYPE_IDENTIFIER, $types[0]->getIdentifier());
    }

    public function testGetType()
    {
        $identifier = self::TYPE_IDENTIFIER;
        $type = $this->dpdShippingMethod->getType($identifier);

        static::assertInstanceOf(ShippingMethodTypeInterface::class, $type);
        static::assertEquals(self::TYPE_IDENTIFIER, $type->getIdentifier());
    }

    public function testGetOptionsConfigurationFormType()
    {
        $type = $this->dpdShippingMethod->getOptionsConfigurationFormType();

        static::assertEquals(HiddenType::class, $type);
    }

    public function testGetSortOrder()
    {
        static::assertEquals('20', $this->dpdShippingMethod->getSortOrder());
    }

    public function testCalculatePrices()
    {
        $context = $this->createMock(ShippingContextInterface::class);

        $methodOptions = ['handling_fee' => null];
        $optionsByTypes = [self::TYPE_IDENTIFIER => ['handling_fee' => null]];

        $prices = $this->dpdShippingMethod->calculatePrices($context, $methodOptions, $optionsByTypes);

        static::assertCount(1, $prices);
        static::assertTrue(array_key_exists(self::TYPE_IDENTIFIER, $prices));
        static::assertEquals(Price::create('1.0', 'USD'), $prices[self::TYPE_IDENTIFIER]);
    }

    /**
     * @param string      $number
     * @param string|null $resultURL
     *
     * @dataProvider trackingDataProvider
     */
    public function testGetTrackingLink($number, $resultURL)
    {
        static::assertEquals($resultURL, $this->dpdShippingMethod->getTrackingLink($number));
    }

    /**
     * @return array
     */
    public function trackingDataProvider()
    {
        return [
            'emptyTrackingNumber' => [
                'number' => '',
                'resultURL' => null,
            ],
            'wrongTrackingNumber2' => [
                'number' => '123123123123',
                'resultURL' => null,
            ],
            'rightTrackingNumber' => [
                'number' => '09980525414724',
                'resultURL' => DPDShippingMethod::TRACKING_URL.'09980525414724',
            ],
        ];
    }
}
