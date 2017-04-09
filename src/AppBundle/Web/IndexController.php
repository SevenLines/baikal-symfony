<?php

namespace AppBundle\Web;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class IndexController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $jobs = $this->getDoctrine()->getRepository("AppBundle:Job")->findBy([
            'visible' => true
        ]);

        return $this->render('web/index.html.twig', [
            'jobs' => $jobs
        ]);
    }
}
