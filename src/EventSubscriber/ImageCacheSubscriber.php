<?php

namespace App\EventListener;

use App\Entity\Property;
use Doctrine\ORM\Event as PreEvents;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageCacheSubscriber implements EventSubscriberInterface
{

    function __construct(
        private readonly CacheManager $cacheManager,
        private readonly UploaderHelper $uploaderHelper
    )
    {
    }

    function preUpdate (PreEvents\PreUpdateEventArgs $args) {
        $entity = $args->getEntity();
        if (!$entity instanceof Property) return;
        if ($entity->getPictureFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'pictureFile'));
        }
    }

    function preRemove (PreEvents\PreFlushEventArgs $args) {
        $entity = $args->getEntity();
        if (!$entity instanceof Property) return;
        $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'pictureFile'));
    }

    static function getSubscribedEvents () : array {
        return [
            'preUpdate',
            'preRemove'
        ];
    }
}