<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 06.02.17
 * Time: 6:16
 */

namespace AppBundle\Api;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class JobApiController extends Controller
{
    /**
     * @Route("jobs/{job_id}", name="job_item")
     * @Method("GET")
     */
    public function jobAction(int $job_id)
    {
        $job = $this->getDoctrine()->getRepository("AppBundle:Job")->find($job_id);
        return new JsonResponse($job);
    }

    /**
     * @Route("jobs", name="jobs_list")
     * @Method("GET")
     */
    public function jobsListAction()
    {
        $jobs = $this->getDoctrine()->getRepository("AppBundle:Job")->createQueryBuilder("j")
            ->select("j.id", "j.title", "j.shortDescription")
            ->getQuery()->getResult();
        return new JsonResponse($jobs);
    }
}