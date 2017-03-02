<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 25.02.17
 * Time: 19:01
 */

namespace AppBundle\Web;


use Doctrine\ORM\Query;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderAction()
    {
        $request = Request::createFromGlobals();
        $hash = $request->query->get('hash');
        if (!empty($hash)) {
            $doctrine = $this->getDoctrine();
            $basket = $doctrine->getRepository("AppBundle:Basket")->createQueryBuilder("b")
                ->select("b")
                ->where("b.hash = :hash")
                ->setParameter("hash", $hash)
                ->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);

            $basket['totalPriceMin'] = array_sum(array_map(function($item) {
                return $item['price_min'] * $item['count'];
            }, $basket['products']));
            $basket['totalPriceMax'] = array_sum(array_map(function($item) {
                return $item['price_max'] * $item['count'];
            }, $basket['products']));

        } else {
            throw $this->createNotFoundException("");
        }

        return $this->render(":web:order.html.twig", [
            'basket' => $basket,
        ]);
    }
}