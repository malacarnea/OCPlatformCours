<?php

// src/OC/PlatformBundle/DoctrineListener/ApplicationCreationListener.php

namespace OC\PlatformBundle\DoctrineListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use OC\PlatformBundle\Email\ApplicationMailer;
use OC\PlatformBundle\Entity\Application;

class ApplicationCreationListener {

    /**
     * @var ApplicationMailer
     */
    private $applicationMailer;

    public function __construct(ApplicationMailer $applicationMailer) {
        $this->applicationMailer = $applicationMailer;
    }

    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getObject();

        // On ne veut envoyer un email que pour les entités Application
        if (!$entity instanceof Application) {
            return;
        }
        //ajouter un bloc try catch pour attraper l'exception de SwiftMailer sans impacter la transaction de Doctrine
        try{
            $this->applicationMailer->sendNewNotification($entity);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        
    }

}
