<?php

namespace AppBundle\Web;

use AppBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PortfolioController extends Controller
{
    /**
     * @Route("portfolio", name="portfolio")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $form = $this->createForm('AppBundle\Form\PortfolioImageType');

        $categories = $this->getDoctrine()->getRepository("AppBundle:ProductCategory")->createQueryBuilder("c")
            ->select("c.title as text, c.id as id")
            ->getQuery()->getArrayResult();
        return $this->render('web/portfolio/index.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }
}
