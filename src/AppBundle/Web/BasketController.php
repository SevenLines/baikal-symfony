<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 25.02.17
 * Time: 19:01
 */

namespace AppBundle\Web;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class BasketController extends Controller
{

    /**
     * @Route("basket", name="basket_pre_order")
     */
    public function preOrderAction()
    {
        $products_info = $this->get("basket_service")->getFromCookies();

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

            $form = $this->createFormBuilder($basket)
                ->add("full_name", TextType::class, [
                    'label' => "ФИО",
                    'attr' => ['placeholder' => "Иванов Иван Инваович"]
                ])
                ->add("email", EmailType::class, [
                    'label' => "Электронный адрес",
                    'attr' => ['placeholder' => "xxxxx@xxxxx.xxx"]
                ])
                ->add("phone", TextType::class, [
                    'required' => false,
                    'label' => "Телефон",
                    'attr' => ['placeholder' => "+7 XXX XXX XX XX"]
                ])
                ->add("comment", TextareaType::class, [
                    'required' => false,
                    'label' => "Комментарий",
                    "attr" => ['cols' => "30", 'rows' => "10"]
                ])
                ->getForm();

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

            $basket->{'totalPriceMin'} = array_sum(array_map(function ($item) {
                return $item['price_min'] * $item['count'];
            }, $basket->getProducts()));
            $basket->{'totalPriceMax'} = array_sum(array_map(function ($item) {
                return $item['price_max'] * $item['count'];
            }, $basket->getProducts()));
        } else {
            throw $this->createNotFoundException("");
        }

        $form_view = $form->createView();
        return $this->render(":web:order.html.twig", [
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

        $basket->{'totalPriceMin'} = array_sum(array_map(function ($item) {
            return $item['price_min'] * $item['count'];
        }, $basket->getProducts()));
        $basket->{'totalPriceMax'} = array_sum(array_map(function ($item) {
            return $item['price_max'] * $item['count'];
        }, $basket->getProducts()));

        # отправляем письмо об успешном заказе
        $message = \Swift_Message::newInstance()
            ->setSubject("Заказ номер: {$basket->getId()}")
            ->setFrom("robot@baikalfortit.ru")
            ->setTo("{$basket->getEmail()}")
            ->setBody(
                $this->renderView(
                    ":emails:email_order.html.twig", [
                        "basket" => $basket
                    ]
                ), 'text/html'
            );
        $this->get("mailer")->send($message);

        $response = $this->render(":web:order_success.html.twig", [
            'basket' => $basket
        ]);
        $response->headers->clearCookie("order_hash");
        $response->headers->clearCookie("basket");

        return $response;
    }
}