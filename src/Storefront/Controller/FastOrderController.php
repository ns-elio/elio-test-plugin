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
        private readonly EntityRepository $fastOrderRepository,
        private readonly CartService $cartService,
        private readonly LineItemFactoryRegistry $factory
    ) {
    }

    #[Route(path: '/fast_order', name: 'frontend.fast-order.page', methods: ['GET'])]
    public function fastOrderPage(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        $inputProducts = $data->get('productId') ? $data->get('productId')->all() : [];
        $numInputProducts = count($inputProducts);

        return $this->renderStorefront('@Storefront/storefront/page/fastOrder/index.html.twig', [
            'data' => $data,
            'numInputProducts' => $numInputProducts,
        ]);
    }

    #[Route(path: '/fast_order', name: 'frontend.fast-order.submit', methods: ['POST'])]
    public function fastOrderSubmit(Request $request, RequestDataBag $data, Cart $cart, SalesChannelContext $context): Response
    {
        $items = [];
        $productIds = array_filter($data->get('productId')->all());
        $quantities = $data->get('quantity')->all();

        for($i=0; $i<count($productIds); $i++) {
            $items[] = [
                'productId' => $productIds[$i],
                'quantity' => $quantities[$i],
            ];
        }

        $errors = $this->validateInput($items);

        if(!empty($errors)) {
            foreach($errors as $error) {
                $this->addFlash(self::DANGER, $error);
            }
            return $this->forwardToRoute('frontend.fast-order.page');
        }

        foreach($items as $item) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('productNumber', $item['productId']));

            $product = $this->productRepository->search($criteria, $context->getContext())->first();

            if($product === null) {
                $errors[] = "Product " . $item['productId'] . " does not exist.";
                continue;
            }

            if($product->getAvailableStock() < $item['quantity']) {
                $errors[] = "Product " . $item['productId'] . " only has " . $product->getAvailableStock() . " items available.";
            }
        }

        if(!empty($errors)) {
            foreach($errors as $error) {
                $this->addFlash(self::DANGER, $error);
            }
            return $this->forwardToRoute('frontend.fast-order.page');
        }

        foreach($items as $item) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('productNumber', $item['productId']));

            $product = $this->productRepository->search($criteria, $context->getContext())->first();

            $itemReferencedId = $product->getId();

            $lineItem = $this->factory->create([
                'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
                'referencedId' => $itemReferencedId,
                'quantity' => (int) $item['quantity'],
            ], $context);

            $this->cartService->add($cart, $lineItem, $context);

            $sessionId = $request->getSession()->getId();

            $this->fastOrderRepository->create([
                [
                    'dateTime' => new \DateTime(),
                    'sessionId' => $sessionId,
                    'quantity' => (int) $item['quantity'],
                    'product' => [
                        'id' => $product->getId(),
                        'version_id' => $product->getVersionId(),
                    ],
                    'comment' => '',
                ]
            ], $context->getContext());
        }

        return $this->redirectToRoute('frontend.checkout.cart.page');
    }

    private function validateInput($items): ?array
    {
        $errors = [];

        $itemIds = array_column($items, 'productId');
        if(count($itemIds) !== count(array_unique($itemIds))) {
            $errors[] = "Product list must not contain duplicates.";
        }

        foreach($items as $item) {
            if(empty($item['productId'])) {
                $errors[] = "Product number is required.";
                break;
            }
            if(empty($item['quantity'])) {
                $errors[] = "Quantity is required.";
                break;
            }
        }

        if(count($errors)) {
            return $errors;
        }

        return null;
    }
}
