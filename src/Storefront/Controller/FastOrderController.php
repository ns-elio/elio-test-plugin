<?php declare(strict_types=1);

namespace Elio\TestPlugin\Storefront\Controller;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedHook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @internal
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class FastOrderController extends StorefrontController
{
    /**
     * @internal
     */
    public function __construct(
    ) {
    }

    /**
     * @Route("/fast_order", name="frontend.fast-order.page", methods={"GET"})
     */
    public function fastOrderPage(Request $request, SalesChannelContext $context): Response
    {
        return $this->renderStorefront('@Storefront/storefront/page/fastOrder/index.html.twig');
    }
}
