<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 10.02.17
 * Time: 21:05
 */

namespace AppBundle\Web;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JobController extends Controller
{
    /**
     * @Route("j/{job_id}/{title}", name="job_description")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function job($job_id)
    {
        $repo = $this->getDoctrine()->getRepository("AppBundle:Job");
        $job = $repo->createQueryBuilder("j")
            ->leftJoin("j.productCategories", "c")
            ->leftJoin("c.products", "p")
            ->setParameter("job_id", $job_id)
            ->where("j.id = :job_id")
            ->addSelect("c", "p")
            ->orderBy("c.title, p.title")
            ->getQuery()->getOneOrNullResult();

        return $this->render("web/job.html.twig", [
            "job" => $job,
        ]);

    }
}