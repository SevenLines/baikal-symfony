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
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("basket/calculate", name="basket_calc")
     */
    public function basketCalculateAction()
    {
        $request = Request::createFromGlobals();
        $basket = json_decode($request->cookies->get("basket", "{}"), true);

        $basket_info = $this->getDoctrine()->getRepository("AppBundle:Product")->createQueryBuilder("p")
            ->select('SUM(p.price_min) as sum_min, SUM(p.price_max) as sum_max')
            ->where("p.id in (:ids)")
            ->setParameter("ids", array_filter(array_keys($basket), function ($item) {
                return is_integer($item);
            }))->getQuery()->getOneOrNullResult();

        return new JsonResponse([
            "sum_min" => intval($basket_info['sum_min']),
            "sum_max" => intval($basket_info['sum_max'])
        ]);
    }
}