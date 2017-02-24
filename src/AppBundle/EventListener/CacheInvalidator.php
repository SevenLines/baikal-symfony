<?php

/**
 * Created by PhpStorm.
 * User: m
 * Date: 24.02.17
 * Time: 21:04
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Job;
use AppBundle\Entity\Options;
use AppBundle\Services\DictionaryManager;
use AppBundle\Services\OptionsService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;


class CacheInvalidator implements EventSubscriber
{
    /**
     * @var OptionsService
     */
    protected $optionsService;

    /**
     * @var DictionaryManager
     */
    private $dictionaryManager;

    public function __construct(OptionsService $optionsService, DictionaryManager $dictionaryManager)
    {
        $this->optionsService = $optionsService;
        $this->dictionaryManager = $dictionaryManager;
    }

    public function getSubscribedEvents()
    {
        return array(
            'postUpdate',
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Options) {
            $this->optionsService->invalidate();
            $this->dictionaryManager->invalidate();
        }

        if ($entity instanceof Job) {
            $this->dictionaryManager->invalidate();
        }
    }
}