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


        if (count(array_keys($basket)) > 0) {
            $expression_min = "CASE ";
            $expression_max = "CASE ";
            foreach ($basket as $id => $count) {
                if (is_int($id) and is_int($count)) {
                    $expression_min .= "WHEN p.id = $id THEN p.price_min * $count ";
                    $expression_max .= "WHEN p.id = $id THEN p.price_max * $count ";
                }
            }
            $expression_min .= "ELSE 0 END";
            $expression_max .= "ELSE 0 END";

            $query = $this->getDoctrine()->getRepository("AppBundle:Product")->createQueryBuilder("p")
                ->select("SUM($expression_min) as sum_min, SUM($expression_max) as sum_max")
                ->where("p.id in (:ids)")
                ->setParameter("ids", array_filter(array_keys($basket), function ($item) {
                    return is_integer($item);
                }))->getQuery();
            $basket_info = $query->getOneOrNullResult();
        } else {
            $basket_info = [
                "sum_min" => 0,
                "sum_max" => 0,
            ];
        }

        return new JsonResponse([
            "sum_min" => intval($basket_info['sum_min']),
            "sum_max" => intval($basket_info['sum_max'])
        ]);
    }

}