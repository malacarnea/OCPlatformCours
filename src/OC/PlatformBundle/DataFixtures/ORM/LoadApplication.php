<?php

// src/OC/PlatformBundle/DataFixtures/ORM/LoadSkill.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\DataFixtures\ORM\LoadAdvert;

class LoadAppliaction extends AbstractFixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {

        for ($i = 0; $i < 20; $i++) {
            if ($i % 2 == 0) {
                $rand = rand(1, 5);
                for ($r = 0; $r < $rand; $r++) {
                    // On crée la candidature
                    $application = new Application();
                    $application->setAuthor("Author_app");
                    $application->setContent("Je suis motivé, sérieux, curieux... Embauchez moi !");
                    $application->setDate(new \DateTime(date("Y-m-d H:i:s")));
                    $application->setAdvert($this->getReference("advert_" . $i));
                    // On la persiste
                    $manager->persist($application);
                }
            }
        }

        // On déclenche l'enregistrement de toutes les compétences
        $manager->flush();
    }

    public function getDependencies() {
        return array(
            LoadAdvert::class,
        );
    }

}
