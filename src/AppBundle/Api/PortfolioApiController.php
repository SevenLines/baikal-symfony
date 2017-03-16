<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 14.03.17
 * Time: 21:46
 */

namespace AppBundle\Api;


use AppBundle\Entity\PortfolioImage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortfolioApiController extends Controller
{
    /**
     * @Route("portfolio/upload", name="api_portfolio_upload")
     * @Method("POST")
     */
    public function uploadPortfolio(Request $request)
    {
        $image = new PortfolioImage();
        $form = $this->createForm('AppBundle\Form\PortfolioImageType', $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->getData();
            $vm = $this->getDoctrine()->getManager();
            $vm->persist($image);
            $vm->flush();
        }
        return new Response();
    }
}