<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\DataFixtures\ORM\LoadAdvert;
use OC\PlatformBundle\DataFixtures\ORM\LoadSkill;

class LoadAdvertSkill extends AbstractFixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        // Liste des noms de compétences à ajouter
        $skills = array('PHP', 'Symfony', 'C++', 'MySQL', 'HTML5','CSS3','JavaScript','JQuery','Python','Java', 'Photoshop', 'Blender', 'Bloc-note');

        for ($i=0; $i<20; $i++) {
            // On crée la compétence
            $nbSkills=rand(0, 5);
            $choix=$skills;
            for($s=0; $s<$nbSkills; $s++){
                $advertSkill = new AdvertSkill();
                $advertSkill->setAdvert($this->getReference("advert_".$i));
                $tire=array_splice($choix, array_rand($choix, 1), 1);
//                echo "#### DEBUG #### ".$tire."\n";
                $advertSkill->setSkill($this->getReference($tire[0]));
                $advertSkill->setLevel("Intermédiaire");
                // On la persiste
                $manager->persist($advertSkill);
            }  
        }

        // On déclenche l'enregistrement de toutes les compétences
        $manager->flush();
    }

    public function getDependencies() {
        return array(
            LoadAdvert::class,
            LoadSkill::class,
        );
    }

}
