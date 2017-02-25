<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 25.02.17
 * Time: 14:53
 */

namespace AppBundle\Controller\Web;


use AppBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testJobPage()
    {
        $client = static::createClient();

        $job = new Job();
        $job->setTitle("работа");
        $this->em->getManager()->persis($job);
        $this->em->getManager()->flush();

        $crawler = $client->request('GET', '/j/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}