<?php

namespace Elio\TestPlugin\Core\Content\FastOrderLineItem;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class FastOrderLineItemCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return FastOrderLineItemEntity::class;
    }
}