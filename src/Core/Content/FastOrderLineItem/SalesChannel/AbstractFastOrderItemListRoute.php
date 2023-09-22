<?php declare(strict_types=1);

namespace Elio\TestPlugin\Core\Content\FastOrderLineItem\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractFastOrderItemListRoute
{
    abstract public function getDecorated(): AbstractFastOrderItemListRoute;

    abstract public function load(Criteria $criteria, SalesChannelContext $context): FastOrderItemListRouteResponse;
}