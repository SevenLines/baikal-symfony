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
            "products" => array_map(function($item) {
                return $item;
            }, $products_info['products']),
            "sum_min" => $products_info ['sum_min'],
            "sum_max" => $products_info ['sum_max'],
        ];

        return $this->render(":web:basket.html.twig", $data);
    }

    /**
     * @Route("place_order", name="place_order")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function placeOrder()
    {
        $request = Request::createFromGlobals();
        $data = [];
        return $this->render(":web:order.html.twig", $data);
    }
}