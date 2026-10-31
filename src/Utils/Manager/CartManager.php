<?php

declare(strict_types=1);

namespace App\Utils\Manager;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * The cart id lives in the server-side session, never in a client-supplied cookie,
 * so it cannot be guessed/forged and survives session-id regeneration on login.
 */
class CartManager extends AbstractManager
{
    public const SESSION_KEY = 'cart_id';

    public function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(Cart::class);
    }

    public function findSessionCart(SessionInterface $session): ?Cart
    {
        $cartId = $session->get(self::SESSION_KEY);
        if (!is_int($cartId)) {
            return null;
        }

        $cart = $this->getRepository()->find($cartId);

        return $cart instanceof Cart ? $cart : null;
    }

    public function addProduct(SessionInterface $session, Product $product): Cart
    {
        $cart = $this->findSessionCart($session);
        if ($cart === null) {
            $cart = new Cart();
            $cart->setSessionId($session->getId());
        }

        $cartProduct = null;
        foreach ($cart->getCartProducts() as $existing) {
            if ($existing->getProduct() === $product) {
                $cartProduct = $existing;
                break;
            }
        }

        if ($cartProduct === null) {
            $cartProduct = new CartProduct();
            $cartProduct->setProduct($product);
            $cartProduct->setQuantity(1);
            $cart->addCartProduct($cartProduct);
            $this->entityManager->persist($cartProduct);
        } else {
            $cartProduct->setQuantity((int) $cartProduct->getQuantity() + 1);
        }

        $this->save($cart);
        $session->set(self::SESSION_KEY, $cart->getId());

        return $cart;
    }

    /**
     * Deletes carts created before $before (abandoned anonymous carts).
     *
     * @return int number of removed carts
     */
    public function purgeCreatedBefore(DateTimeImmutable $before): int
    {
        $carts = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Cart::class, 'c')
            ->where('c.createdAt < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->toIterable();

        // ponytail: one flush per 100 carts via ORM so orphanRemoval cleans cart_product; switch to SQL DELETE if this gets slow
        $count = 0;
        foreach ($carts as $cart) {
            $this->entityManager->remove($cart);
            if (++$count % 100 === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();

        return $count;
    }
}
