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
     * @Route("jobs/{job_id}", name="job_description")
     * @param $job_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function job($job_id)
    {
        $job = $this->getDoctrine()->getRepository("AppBundle:Job")->find($job_id);

        return $this->render("web/job.html.twig", ["job" => $job]);

    }
}