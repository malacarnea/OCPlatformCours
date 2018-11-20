<?php

namespace OC\PlatformBundle\Purger;

use Doctrine\ORM\EntityManager;

class OCPurger {

    private $em;

    public function __construct(EntityManager $manager) {
        $this->em = $manager;
    }
    
    /**
     * @param $days nombre de jours 
     * @return int nombre de lignes supprimees
     */
    public function purge($days) {
        $today = new \Datetime();
        $start = $today->sub(\DateInterval::createFromDateString($days . ' days'));
        //recuperation des adverts
        $adverts = $this->em->getRepository("OCPlatformBundle:Advert")->getPurgeAdverts($start);
        $nb=count($adverts);
        foreach ($adverts as $advert) {
            //supprimer les skills rattaches
            $adSkills = $this->em->getRepository("OCPlatformBundle:AdvertSkill")->findBy(array("advert" => $advert));
            foreach ($adSkills as $adSkill) {
                $this->em->remove($adSkill);
            }
            //supprimer l'annonce
            $this->em->remove($advert);
        }
        $this->em->flush();
        return $nb;
    }

}
