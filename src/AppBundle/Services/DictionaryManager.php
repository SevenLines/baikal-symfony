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
use Symfony\Component\Cache\Adapter\RedisAdapter;

class DictionaryManager
{
    protected $router;
    protected $doctrine;
    protected $optionsService;
    /**
     * @var RedisAdapter
     */
    private $cache;

    public function __construct(Router $router,
                                Registry $doctrine,
                                OptionsService $optionsService,
                                RedisAdapter $cache)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
        $this->optionsService = $optionsService;
        $this->cache = $cache;
    }

    public function invalidate() {
        $this->cache->deleteItem("menu");
    }

    public function getAll()
    {
        $menu_cache = $this->cache->getItem("menu");
        if (!$menu_cache->isHit()) {
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

            $options = $this->optionsService->getOptions();

            $data = [
                "menu" => array_merge([[
                    "title" => "Байкал Форт АйТи",
                    "id" => 0,
                    "active" => false,
                    "url" => $this->router->generate("index"),
                ]], $menu),
                "email" => $options->getEmail(),
                "phone" => $options->getPhones(),
            ];

            $menu_cache->set($data);
            $this->cache->save($menu_cache);
        }

        return $menu_cache->get();
    }

}