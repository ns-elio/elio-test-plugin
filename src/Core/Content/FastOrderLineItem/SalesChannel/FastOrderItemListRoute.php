<?php declare(strict_types=1);

namespace Elio\TestPlugin\Core\Content\FastOrderLineItem\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class FastOrderItemListRoute extends AbstractFastOrderItemListRoute
{
    public function __construct(
        protected EntityRepository $fastOrderLineItemRepository
    ) {
    }

    public function getDecorated(): AbstractFastOrderItemListRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/fast-order', name: 'store-api.fast-order.search', methods: ['GET', 'POST'], defaults: ['_entity' => 'elio_fast_order_line_item'])]
    public function load(Criteria $criteria, SalesChannelContext $context): FastOrderItemListRouteResponse
    {
        return new FastOrderItemListRouteResponse($this->fastOrderLineItemRepository->search($criteria, $context->getContext()));
    }
}