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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortfolioApiController extends Controller
{
    /**
     * @Route("portfolio", name="api_portfolio_upload")
     * @Method("POST")
     */
    public function uploadPortfolioImageAction(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_PORTFOLIO_EDIT");

        $image = new PortfolioImage();
        $form = $this->createForm('AppBundle\Form\PortfolioImageType', $image);
        $form->handleRequest($request);


        $portfolio_service = $this->get("portfolio_service");
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->getData();
            $vm = $this->getDoctrine()->getManager();
            $vm->persist($image);
            $vm->flush();

            return new JsonResponse(
                $portfolio_service->toDict($image)
            );
        }

        return new JsonResponse($form->getErrors(), 422);
    }

    /**
     * @Route("portfolio/{id}", name="api_portfolio_delete")
     * @Method("DELETE")
     */
    public function removePortfolioImageAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted("ROLE_PORTFOLIO_EDIT");

        $doctrine = $this->getDoctrine();
        $image = $doctrine->getRepository("AppBundle:PortfolioImage")->find($id);
        $vm = $doctrine->getManager();
        $vm->remove($image);
        $vm->flush();

        return new JsonResponse();
    }

    /**
     * @Route("portfolio/{id}", name="api_portfolio_patch")
     * @Method("PATCH")
     */
    public function updatePortfolioImageAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted("ROLE_PORTFOLIO_EDIT");

        $doctrine = $this->getDoctrine();
        $image = $doctrine->getRepository("AppBundle:PortfolioImage")->find($id);
        $vm = $doctrine->getManager();
        $vm->remove($image);
        $vm->flush();

        return new JsonResponse();
    }
}