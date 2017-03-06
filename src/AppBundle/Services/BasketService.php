<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 25.02.17
 * Time: 21:56
 */

namespace AppBundle\Services;


use AppBundle\Entity\Basket;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Swift_Mailer;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;

class BasketService
{
    private $doctrine;
    private $mailer;
    private $templating;
    private $options;

    public function __construct(Registry $doctrine,
                                Swift_Mailer $mailer,
                                TwigEngine $templating,
                                OptionsService $options)
    {
        $this->doctrine = $doctrine;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->options = $options;
    }

    public function getProductsFromCookies()
    {
        $request = Request::createFromGlobals();
        $basket = json_decode($request->cookies->get("basket", "{}"), true);

        $products = $this->doctrine->getRepository("AppBundle:Product")->createQueryBuilder("p")
            ->select("p")
            ->where("p.id in (:ids)")
            ->setParameter("ids", array_filter(array_keys($basket), function ($item) {
                return is_integer($item);
            }))->getQuery()->getResult();

        $result = [
            "products" => [],
            "sum_min" => 0,
            "sum_max" => 0,
        ];

        foreach ($products as $product) {
            $count = $basket[$product->getId()];
            $result["products"][$product->getId()] = [
                "product" => $product,
                "count" => $count
            ];
            $result["sum_min"] += $product->getPriceMin() * $count;
            $result["sum_max"] += $product->getPriceMax() * $count;
        }

        return $result;
    }

    public function sendOrderSuccessMessages(Basket $basket)
    {
        # расчитываем суммарные суммы
        $basket->calculateTotalValues();

        # отправляем письмо об успешном заказе клиенту
        $message = \Swift_Message::newInstance()
            ->setSubject("Заказ номер: {$basket->getId()}")
            ->setFrom("robot@baikalfortit.ru")
            ->setTo("{$basket->getEmail()}")
            ->setBody(
                $this->templating->render(
                    ":emails:email_order.html.twig", [
                        "basket" => $basket
                    ]
                ), 'text/html'
            );
        $this->mailer->send($message);

        # отправляем письмо об заказе менеджерам
        $options = $this->options->getOptions();
        $message = \Swift_Message::newInstance()
            ->setSubject("Заказ номер: {$basket->getId()}")
            ->setFrom("robot@baikalfortit.ru")
            ->setTo($options->getManagerEmailsArray())
            ->setBody(
                $this->templating->render(
                    ":emails:email_order_manager.html.twig", [
                        "basket" => $basket
                    ]
                ), 'text/html'
            );
        $this->mailer->send($message);
    }
}