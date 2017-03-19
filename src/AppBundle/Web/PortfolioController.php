<?php

namespace AppBundle\Web;

use AppBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PortfolioController extends Controller
{
    /**
     * @Route("portfolio/{job_id}/{title}", name="portfolio")
     * @Route("portfolio/{job_id}", name="portfolio", defaults={"job_id": null})
     * @param $job_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($job_id)
    {
        $form = $this->createForm('AppBundle\Form\PortfolioImageType', null, [
            'job_id' => $job_id
        ]);

        $doctrine = $this->getDoctrine();
        $categories = $doctrine->getRepository("AppBundle:ProductCategory")->createQueryBuilder("c")
            ->select("c.title as text, c.id as id")
            ->innerJoin("c.job", "j");

        $job = $doctrine->getRepository("AppBundle:Job")->find($job_id);

        $images = $doctrine->getRepository("AppBundle:PortfolioImage")->createQueryBuilder("i")
            ->innerJoin("i.categories", 'c')
            ->innerJoin("c.job", 'j')
            ->orderBy("i.updatedAt");

        if (!is_null($job_id)) {
            $images = $images->where("j.id = :job_id")->setParameter("job_id", $job_id);
            $categories = $categories->where("j.id = :job_id")->setParameter("job_id", $job_id);
        }

        $categories = $categories->getQuery()->getArrayResult();
        $images = $images->getQuery()->getResult();

        return $this->render('web/portfolio/portfolio_index.html.twig', [
            'categories' => $categories,
            'images' =>$images,
            'job' => $job,
            'form' => $form->createView(),
            'form_name' => $form->getName(),
        ]);
    }
}
