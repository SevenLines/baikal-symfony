<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 16.04.17
 * Time: 12:16
 */

namespace AppBundle\Web;

use AppBundle\Entity\Job;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SitemapController extends Controller
{
    /**
     *  @Route("/sitemap.xml", name="sitemap")
     */
    public function index()
    {
        $jobs = $this->getDoctrine()->getRepository("AppBundle:Job")->findBy([
            'visible' => true
        ], ['order'=> 'ASC', 'title' => 'ASC']);

        $router = $this->get("router");

        $price_urls = array_map(function (Job $job) use ($router) {
            return [
                'url' => $router->generate("job_description", [
                "job_id" => $job->getId(),
                "title" => $job->getTitle(),
                ]),
                'title' => $job->getTitle(),
                'changefreq' => 'weekly'
            ];
        }, $jobs);

        $portfolio_urls = array_map(function (Job $job) use ($router) {
            return [
                'url' => $router->generate("portfolio", [
                    "job_id" => $job->getId(),
                    "title" => $job->getTitle(),
                ]),
                'title' => $job->getTitle(),
                'changefreq' => 'weekly'
            ];
        }, $jobs);

        $index_url = [
            'url' => $router->generate("index"),
            'changefreq' => 'monthly'
        ];

        $privacy_url = [
            'url' => $router->generate("app_privacy"),
            'changefreq' => 'monthly'
        ];

        $response = $this->render("sitemap.xml.twig", [
            'urls' => array_merge([$index_url, $privacy_url], $price_urls, $portfolio_urls)
        ]);
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
}