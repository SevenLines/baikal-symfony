<?php

/**
 * Created by PhpStorm.
 * User: m
 * Date: 10.02.17
 * Time: 21:12
 */

namespace AppBundle\Services;

use AppBundle\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class DictionaryManager
{
    protected $router;
    protected $doctrine;

    public function __construct(Router $router, Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    public function getAll()
    {
        $menu = array_map(function (Job $job) {
            return [
                "title" => $job->getTitle(),
                "id" => $job->getId(),
                "url" => $this->router->generate("job_description", [
                    "job_id" => $job->getId(),
                    "title" => $job->getTitle()
                ]),
            ];
        }, $this->doctrine->getRepository("AppBundle:Job")->findAll());

        $data = [
            "menu" => array_merge([[
                "title" => "Байкал Форт АйТи",
                "id" => 0,
                "active" => false,
                "url" => $this->router->generate("index"),
            ]], $menu),
            "email" => "baikalfort@mail.ru",
            "phone" => "+7 (3952) 66-64-69, 66-77-13",
        ];

        return $data;
    }

}