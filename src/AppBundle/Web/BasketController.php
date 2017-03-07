<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 25.02.17
 * Time: 19:01
 */

namespace AppBundle\Web;


use AppBundle\Form\BasketType;
use AppBundle\Form\OrderViewConfirmationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BasketController extends Controller
{

    /**
     * @Route("basket", name="basket_pre_order")
     */
    public function preOrderAction()
    {
        $products_info = $this->get("basket_service")->getProductsFromCookies();

        $data = [
            "products" => array_map(function ($item) {
                return $item;
            }, $products_info['products']),
            "sum_min" => $products_info ['sum_min'],
            "sum_max" => $products_info ['sum_max'],
        ];

        return $this->render(":web:basket.html.twig", $data);
    }

    /**
     * @Route("order", name="order")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderAction(Request $request)
    {
        $hash = $request->cookies->get('order_hash');

        if (!empty($hash)) {
            $doctrine = $this->getDoctrine();
            $basket = $doctrine->getRepository("AppBundle:Basket")->getByHash($hash);

            # создаем форму
            $form = $this->createForm(BasketType::class, $basket);
            $form->handleRequest($request);

            # обработка отправленной формы
            if ($form->isSubmitted() && $form->isValid()) {
                $basket = $form->getData();
                $basket->setConfirmed(true);
                $vm = $doctrine->getManager();
                $vm->persist($basket);
                $vm->flush();

                $response = $this->redirectToRoute("order_success");
                return $response;
            }

            # расчитываем суммарные суммы
            $basket->calculateTotalValues();
        } else {
            throw $this->createNotFoundException("");
        }

        $form_view = $form->createView();
        return $this->render(":web/order:order.html.twig", [
            'basket' => $basket,
            'form' => $form_view
        ]);
    }


    /**
     * @Route("/order/success", name="order_success")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderSuccessAction(Request $request)
    {
        $hash = $request->cookies->get('order_hash');
        if (!empty($hash)) {
            $doctrine = $this->getDoctrine();
            $basket = $doctrine->getRepository("AppBundle:Basket")->getByHash($hash);
        } else {
            return $this->redirectToRoute("index");
        }

        # рассылаем сообщения
        $this->get("basket_service")->sendOrderSuccessMessages($basket);

        # рендерим ответ
        $response = $this->render(":web/order:order_success.html.twig", [
            'basket' => $basket
        ]);

        # очищаем куки корзины и хеша заказа
        $response->headers->clearCookie("order_hash");
        $response->headers->clearCookie("basket");

        return $response;
    }


    /**
     * @Route("order/view/{hash}", name="order_view")
     * @param Request $request
     * @param $hash
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderViewAction(Request $request, $hash)
    {
        $basket = $this->getDoctrine()->getRepository("AppBundle:Basket")->getByHash($hash);

        if (is_null($basket)) {
            throw $this->createNotFoundException("");
        }

        $basket->calculateTotalValues();
        return $this->render(":web/order:order_view.html.twig", [
            'basket' => $basket
        ]);
    }
}