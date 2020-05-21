<?php

namespace Oro\Bundle\DPDBundle\Tests\Unit\Handler;

use Oro\Bundle\CacheBundle\Action\DataStorage\InvalidateCacheDataStorage;
use Oro\Bundle\DPDBundle\Cache\ZipCodeRulesCache;
use Oro\Bundle\DPDBundle\Handler\InvalidateCacheActionHandler;

class InvalidateCacheActionHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ZipCodeRulesCache|\PHPUnit\Framework\MockObject\MockObject
     */
    private $upsPriceCache;

    /**
     * @var InvalidateCacheActionHandler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->upsPriceCache = $this->createMock(ZipCodeRulesCache::class);

        $this->handler = new InvalidateCacheActionHandler($this->upsPriceCache);
    }

    public function testHandle()
    {
        $dataStorage = new InvalidateCacheDataStorage([
            InvalidateCacheActionHandler::PARAM_TRANSPORT_ID => 1
        ]);

        $this->upsPriceCache->expects(static::once())
            ->method('deleteAll');

        $this->handler->handle($dataStorage);
    }
}
