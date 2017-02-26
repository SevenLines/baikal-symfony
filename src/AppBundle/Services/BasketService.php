<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 25.02.17
 * Time: 21:56
 */

namespace AppBundle\Services;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;

class BasketService
{
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFromCookies()
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
}