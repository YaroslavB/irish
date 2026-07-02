<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\StaticStorage\OrderStaticStorage;
use App\Form\Admin\EditOrderFormType;
use App\Form\Handler\OrderFormHandler;
use App\Repository\OrderRepository;
use App\Utils\Manager\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{HttpFoundation\Request,
    HttpFoundation\Response,
    Routing\Annotation\Route};


#[Route(path: '/admin/order', name: 'admin_order_')]
class OrderController extends AbstractController
{
    /**
     * Show all orders
     */
    #[Route(path: '/list', name: 'list')]
    public function list(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['isDeleted' => false],
            ['id' => 'DESC']);

        return $this->render(
            'admin/order/list.html.twig',
            [
                'orders'      => $orders,
                'orderStatus' => OrderStaticStorage::getOrderStatusList(),
            ]
        );
    }

    #[Route(path: '/edit/{id}', name: 'edit')]
    #[Route(path: '/add', name: 'add')]
    public function edit(
        Request $request,
        OrderFormHandler $orderFormHandler,
        Order $order = null
    ): Response {
        if (!$order) {
            $order = new Order();
        }

        $form = $this->createForm(EditOrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $orderFormHandler->processEditForm($order);
            $this->addFlash('success', 'Order  saved');

            return $this->redirectToRoute(
                'admin_order_edit',
                ['id' => $order->getId()]
            );
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Order not saved');
        }

        return $this->render('admin/order/edit.html.twig', [
            'form'  => $form->createView(),
            'order' => $order,
        ]);
    }


    /**
     * Delete order
     */
    #[Route(path: '/delete/{id}', name: 'delete')]
    public function delete(
        Order $order,
        OrderManager $orderManager
    ): Response {
        $orderManager->remove($order);
        $this->addFlash('warning', 'Order delete');

        return $this->redirectToRoute('admin_order_list');
    }
}
