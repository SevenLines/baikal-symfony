<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 01.03.17
 * Time: 20:06
 */

namespace AppBundle\Api;


use AppBundle\Entity\Basket;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BasketApiController extends Controller
{
    /**
     * @Route("place-order", name="api_place_order")
     * @Method({"POST"})
     */
    public function placeOrderAction()
    {
        $request = Request::createFromGlobals();
        $content = $request->getContent();
        if (!empty($content)) {
            $products_info = json_decode($content, true);
            $products = $this->getDoctrine()->getRepository("AppBundle:Product")->createQueryBuilder("p")
                ->select("p.unit, p.id, p.title, p.price_min, p.price_max")
                ->where("p.id in (:ids)")
                ->setParameter("ids", array_filter(array_keys($products_info), function($item) {
                    return is_integer($item);
                }))->getQuery()->getArrayResult();
            foreach ($products as &$product) {
                $product['count'] = $products_info[$product['id']];
            }
        } else {
            $products = [];
        }

        $basket = new Basket();
        $basket->setHash(uniqid("", true))
            ->setProducts($products)
            ->setDateCreated(new \DateTime())
            ->setDateUpdated(new \DateTime());

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($basket);
        $manager->flush();

        return new JsonResponse([
            "hash" => $basket->getHash()
        ]);
    }
}