<?php

// src/OC/PlatformBundle/DataFixtures/ORM/LoadSkill.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;

class LoadAdvert extends AbstractFixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {

        $lorem="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sodales sapien volutpat, ornare tellus id, suscipit velit. Vivamus et nibh iaculis, venenatis est at, vestibulum ligula. Suspendisse in varius ligula. Morbi cursus lectus maximus egestas congue. Suspendisse finibus leo eget neque tristique, in porta leo volutpat. Integer pretium cursus condimentum. In hac habitasse platea dictumst. Nulla ultrices, augue sed consectetur venenatis, arcu nibh luctus velit, vitae lobortis velit sapien at metus. Proin nisl nunc, efficitur a dapibus eu, semper id arcu.";
        for ($i=0; $i<20; $i++) {
            // On crée l'annonce
            $advert = new Advert();
            $advert->setAuthor("Author-".$i);
            $advert->setContent($lorem);
            $advert->setDate(new \DateTime(date("Y-m-d H:i:s", time()-($i*(3600*24)))));
            $advert->setTitle("Lorem ipsum");
            //ajout des catégories
            $advert->addCategory($this->getReference("Graphisme"));
            $advert->addCategory($this->getReference("Programmation"));
            $advert->addCategory($this->getReference("Bases_de_donnees"));
            // On la persiste
            $manager->persist($advert);
            $name = "advert_" . $i;
            echo "#### DEBUG ##### ".$name."\n";
            $this->addReference($name, $advert);
        }
        // On déclenche l'enregistrement de toutes les compétences
        $manager->flush();
    }

    public function getDependencies() {
        return array(
            LoadCategory::class,
        );
    }

}
