<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 25.02.17
 * Time: 18:24
 */

namespace AppBundle\Test;


use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\CssSelector\CssSelectorConverter;

class BaseTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var ObjectManager
     */
    public $em;
    /**
     * @var CssSelectorConverter
     */
    protected $cssConvertor;

    protected function setUp()
    {
        self::bootKernel();

        $doctrine = static::$kernel->getContainer()->get('doctrine');
        $this->em = $doctrine->getManager();
        $this->client = static::createClient();
        $this->cssConvertor = new CssSelectorConverter();

        // очищаем БД
        $meta_all = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($meta_all as $meta) {
            try {
                $repositoryClassName = $meta->customRepositoryClassName
                    ?: $this->em->getConfiguration()->getDefaultRepositoryClassName();
                (new $repositoryClassName($this->em, $meta))->createQueryBuilder("p")->delete()->getQuery()->execute();
            } catch (Exception $_e) {
            }
        }
        $this->em->flush();
    }

    protected function tearDown()
    {
    }

    protected function css($css, $crawler)
    {
        return $crawler->filterXPath($this->cssConvertor->toXPath($css));
    }

    protected function api($path, $method = "GET", $success=true)
    {
        $crawler = $this->client->request($method, $path);
        $response = $this->client->getResponse();

        if ($success) {
            $this->assertTrue($response->isSuccessful(),
                "method $method for '$path' must be succeed: {$response->getStatusCode()}");
        } else {
            $this->assertFalse($response->isSuccessful(),
                "method $method for '$path' must be failed: {$response->getStatusCode()}");
        }

        return $crawler;
    }

}