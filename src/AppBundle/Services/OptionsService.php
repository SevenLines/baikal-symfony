<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 24.02.17
 * Time: 20:28
 */

namespace AppBundle\Services;

use AppBundle\Entity\Options;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class OptionsService
{
    /**
     * @var RedisAdapter
     */
    protected $cache;

    /**
     * @var Registry
     */
    protected $doctrine;

    public function __construct(RedisAdapter $cache, Registry $doctrine)
    {
        $this->cache = $cache;
        $this->doctrine = $doctrine;
    }

    public function getOptions() : Options
    {
        $options = $this->cache->getItem("options");
        if (!$options->isHit()) {
            $repo = $this->doctrine->getRepository("AppBundle:Options");

            $options_item = $repo->createQueryBuilder('op')
                ->select("op")
                ->orderBy("op.id", "ASC")
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $options_item) {
                $options_item = new Options();
                $this->doctrine->getManager()->persist($options_item);
                $this->doctrine->getManager()->flush();
            }

            $options->set($options_item);
            $this->cache->save($options);
        }
        return $options->get();
    }

    public function invalidate()
    {
        $this->cache->deleteItem("options");
    }

}