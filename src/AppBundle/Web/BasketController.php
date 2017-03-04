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
        $hash = $request->query->get('hash');

        if (!empty($hash)) {
            $doctrine = $this->getDoctrine();
            $basket = $doctrine->getRepository("AppBundle:Basket")->createQueryBuilder("b")
                ->select("b")
                ->where("b.hash = :hash")
                ->setParameter("hash", $hash)
                ->getQuery()->getOneOrNullResult();

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

            if ($form->isSubmitted() && $form->isValid()) {
                $basket = $form->getData();
                $vm = $doctrine->getManager();
                $vm->persist($basket);
                $vm->flush();
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
}