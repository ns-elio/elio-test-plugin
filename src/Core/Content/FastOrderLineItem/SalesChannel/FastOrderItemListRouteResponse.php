<?php declare(strict_types=1);

namespace Elio\TestPlugin\Core\Content\FastOrderLineItem\SalesChannel;

use Elio\TestPlugin\Core\Content\FastOrderLineItem\FastOrderLineItemCollection;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class FastOrderItemListRouteResponse extends StoreApiResponse
{
    public function getLineItems(): FastOrderLineItemCollection
    {
        return $this->object->getEntities();
    }
}