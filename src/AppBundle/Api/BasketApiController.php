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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BasketApiController extends Controller
{
    /**
     * @Route("place-order", name="api_place_order")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function placeOrderAction(Request $request)
    {
        $content = $request->getContent();
        $doctrine = $this->getDoctrine();
        if (!empty($content)) {
            $products_info = json_decode($content, true);
            $products = $doctrine->getRepository("AppBundle:Product")->createQueryBuilder("p")
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

        # если хеш заказ в куках, то пытаемя найти не подтвержденный заказ в БД
        $hash = $request->cookies->get('order_hash');
        $basket = null;
        if ($hash) {
            $basket = $doctrine->getRepository("AppBundle:Basket")->getByHash($hash, true);
            if ($basket) {
                $basket->setProducts($products);
            }
        }

        # если к этому моменту заказа не добавили то создаем новый
        if (is_null($basket)) {
            $basket = new Basket();
            $basket->setHash(uniqid("", true))
                ->setProducts($products);
        }

        $manager = $doctrine->getManager();
        $manager->persist($basket);
        $manager->flush();

        if ($request->isXmlHttpRequest()) {
            $response = new Response();
        } else {
            $response = new RedirectResponse($this->generateUrl("order"));
        }
        $response->headers->setCookie(new Cookie('order_hash', $basket->getHash()));

        return $response;
    }
}