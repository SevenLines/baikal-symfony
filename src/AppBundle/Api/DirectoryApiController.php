<?php

namespace AppBundle\Api;

use AppBundle\Entity\Job;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DirectoryApiController extends Controller
{
    /**
     * @Route("menu", name="api_menu")
     */
    public function menuAction()
    {
        $menu = array_map(function (Job $job) {
            return [
                "title" => $job->getTitle(),
                "id" => $job->getId(),
                "url" => $this->generateUrl("job_item", ["job_id" => $job->getId()]),
            ];
        }, $this->getDoctrine()->getRepository("AppBundle:Job")->findAll());

        $data = [
            "menu" => array_merge([[
                "title" => "О нас",
                "id" => 0,
                "url" => $this->generateUrl("index"),
            ]], $menu),
            "email" => "mmailm@mail.ru",
            "phone" => "2-12-85-06",
        ];

        return new JsonResponse($data);
    }


}
