<?php declare(strict_types=1);

namespace Elio\TestPlugin\Storefront\Controller;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;

/**
 * @internal
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class FastOrderController extends StorefrontController
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityRepository $productRepository,
        private readonly CartService $cartService,
        private readonly LineItemFactoryRegistry $factory
    ) {
    }

    #[Route(path: '/fast_order', name: 'frontend.fast-order.page', methods: ['GET'])]
    public function fastOrderPage(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        $inputProducts = $data->get('productId') ? $data->get('productId')->all() : [];
        $numInputProducts = count(array_filter($inputProducts));

        return $this->renderStorefront('@Storefront/storefront/page/fastOrder/index.html.twig', [
            'data' => $data,
            'numInputProducts' => $numInputProducts,
        ]);
    }

    #[Route(path: '/fast_order', name: 'frontend.fast-order.submit', methods: ['POST'])]
    public function fastOrderSubmit(Request $request, RequestDataBag $data, Cart $cart, SalesChannelContext $context): Response
    {
        $hasErrors = false;
        $items = [];
        $products = array_filter($data->get('productId')->all());
        $quantities = array_filter($data->get('quantity')->all());

        for($i=0; $i<count($products); $i++) {
            if(empty($products[$i])) {
                $message = "Product number is required.";
                $this->addFlash(self::DANGER, $message);
                $hasErrors = true;
                break;
            }

            if(empty($quantities[$i])) {
                $message = "Quantity is required.";
                $this->addFlash(self::DANGER, $message);
                $hasErrors = true;
                break;
            }

            $items[] = [
                'productId' => $products[$i],
                'quantity' => (int) $quantities[$i],
            ];
        }

        if($hasErrors) {
            return $this->forwardToRoute('frontend.fast-order.page');
        }

        foreach($items as &$item) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('productNumber', $item['productId']));

            $product = $this->productRepository->search($criteria, $context->getContext())->first();

            if($product === null) {
                $message = "Product " . $item['productId'] . " does not exist.";
                $this->addFlash(self::DANGER, $message);
                $hasErrors = true;
            }

            if($product->getAvailableStock() < $item['quantity']) {
                $message = "Product " . $item['productId'] . " only has " . $product->getAvailableStock() . " items available.";
                $this->addFlash(self::DANGER, $message);
                $hasErrors = true;
            }

            $item['id'] = $product->getId();
        }

        if($hasErrors) {
            return $this->forwardToRoute('frontend.fast-order.page');
        }

        foreach($items as $item) {
            $lineItem = $this->factory->create([
                'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
                'referencedId' => $item['id'],
                'quantity' => $item['quantity'],
            ], $context);

            $this->cartService->add($cart, $lineItem, $context);
        }

        return $this->redirectToRoute('frontend.checkout.cart.page');
    }
}
