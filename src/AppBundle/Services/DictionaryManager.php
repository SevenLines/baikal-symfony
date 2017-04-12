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
use Symfony\Component\HttpFoundation\RequestStack;

class DictionaryManager
{
    protected $router;
    protected $doctrine;
    protected $optionsService;
    /**
     * @var RedisAdapter
     */
    private $cache;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(Router $router,
                                Registry $doctrine,
                                OptionsService $optionsService,
                                RedisAdapter $cache,
                                RequestStack $requestStack)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
        $this->optionsService = $optionsService;
        $this->cache = $cache;
        $this->requestStack = $requestStack;
    }

    public function invalidate()
    {
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
                    "url" => $this->router->generate("portfolio", [
                        "job_id" => $job->getId(),
                        "title" => $job->getTitle()
                    ]),
                    "urls" => [
                        'price_list' => [
                            "url" => $this->router->generate("job_description", [
                                "job_id" => $job->getId(),
                                "title" => $job->getTitle(),
                            ]),
                            "title" => "прейскурант цен",
                            "active" => false,
                        ],
                        'portfolio' => [
                            "url" => $this->router->generate("portfolio", [
                                "job_id" => $job->getId(),
                                "title" => $job->getTitle(),
                            ]),
                            "title" => "портфолио",
                            "active" => false,
                        ],
                    ],
                    "active" => false,
                ];
            }, $this->doctrine->getRepository("AppBundle:Job")->findBy([
                'visible' => true
            ], ['order'=> 'ASC', 'title' => 'ASC']));

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

        $data = $menu_cache->get();

        $requestUri = $this->requestStack->getCurrentRequest()->getRequestUri();
        foreach ($data['menu'] as &$item) {
            if (isset($item['urls'])) {
                foreach ($item['urls'] as &$url) {
                    if ($requestUri == $url['url']) {
                        $url['active'] = true;
                        $item['active'] = true;
                    }
                }
            }
        }

        return $data;
    }

}