<?php

namespace Oro\Bundle\DPDBundle\Method\Factory;

use Oro\Bundle\DPDBundle\Entity\ShippingService;
use Oro\Bundle\DPDBundle\Method\DPDShippingMethodType;
use Oro\Bundle\IntegrationBundle\Entity\Channel;

interface DPDShippingMethodTypeFactoryInterface
{
    /**
     * @param Channel         $channel
     * @param ShippingService $service
     *
     * @return DPDShippingMethodType
     */
    public function create(Channel $channel, ShippingService $service);
}
