<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller {

    public function indexAction() {
        // Notre liste d'annonce en dur
//        $listAdverts = array(
//            array(
//                'title' => 'Recherche développpeur Symfony',
//                'id' => 1,
//                'author' => 'Alexandre',
//                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
//                'date' => new \Datetime()),
//            array(
//                'title' => 'Mission de webmaster',
//                'id' => 2,
//                'author' => 'Hugo',
//                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
//                'date' => new \Datetime()),
//            array(
//                'title' => 'Offre de stage webdesigner',
//                'id' => 3,
//                'author' => 'Mathieu',
//                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
//                'date' => new \Datetime())
//        );
        //recupere l'entityManager par defaut
        $manager = $this->getDoctrine()->getManager();
        //utiliser les repository pour recuperer les entites dans la bd (raccourci NomBundle:NomEntite)
        $advertRepository = $manager->getRepository("OCPlatformBundle:Advert");
        $categories=array("Développement web", "Intégrateur");
        $listAdverts=$advertRepository->getAdvertWithCategories($categories);
        
        $appRepo=$manager->getRepository("OCPlatformBundle:Application");
        $listApplications=$appRepo->getApplicationsWithAdvert(3);
//        $listAdverts=$advertRepository->getAdvertWithApplications();
//        $listAdverts=$advertRepository->findByAuthorAndDate("Alicia", "2019");
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array("listAdverts" => $listAdverts, "listApplications"=>$listApplications));
    }

    public function viewAction($id) {
        $manager = $this->getDoctrine()->getManager();
        $advert = $manager->getRepository("OCPlatformBundle:Advert")->myFindOne($id);
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }
        $applications = $manager->getRepository("OCPlatformBundle:Application")->findBy(array("advert" => $advert));

        // On récupère maintenant la liste des AdvertSkill
        $listAdvertSkills = $manager
                ->getRepository('OCPlatformBundle:AdvertSkill')
                ->findBy(array('advert' => $advert))
        ;
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
                    'advert' => $advert,
                    'applications' => $applications,
                    'listAdvertSkills' => $listAdvertSkills
        ));
    }

    public function addAction(Request $request) {
        //créer l'advert
        $advert = new Advert;
        $advert->setTitle('Graphiste');
        $advert->setAuthor('Alicia');
        $advert->setContent("Recherche un Graphiste pour un projet de jeu vidéo en temps réel.");

        $appli = new Application;
        $appli->setAuthor('John TITOR');
        $appli->setContent('Professeur en Physique Quantique, je passe mon temps libre à créer des maquettes sous Photoshop.');

        $appli2 = new Application;
        $appli2->setAuthor('Creatix');
        $appli2->setContent('The Gimp, il n\'y a que ça de vrai !');

        $appli->setAdvert($advert);
        $appli2->setAdvert($advert);

        //image
        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');
        //récuperer le manager
        $manager = $this->getDoctrine()->getManager();
        //persister l'advert donne la responsabilité de l'objet à Doctrine

        $manager->persist($appli);
        $manager->persist($appli2);
        $manager->persist($image);

        // On récupère toutes les compétences possibles
        $listSkills = $manager->getRepository('OCPlatformBundle:Skill')->findAll();

        // Pour chaque compétence
        foreach ($listSkills as $skill) {
            // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
            $advertSkill = new AdvertSkill();

            // On la lie à l'annonce, qui est ici toujours la même
            $advertSkill->setAdvert($advert);
            // On la lie à la compétence, qui change ici dans la boucle foreach
            $advertSkill->setSkill($skill);

            // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
            $advertSkill->setLevel('Expert');

            // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
            $manager->persist($advertSkill);
        }
        $manager->persist($advert);

        //flusher l'advert (tout ce qui a ete persiste avant
        $manager->flush();

        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
        // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
        if ($request->isMethod('POST')) {
            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée.');
            // Puis on redirige vers la page de visualisation de cette annonce
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('OCPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id, Request $request) {
        // Ici, on récupérera l'annonce correspondante à $id
        // Même mécanisme que pour l'ajout
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
            return $this->redirectToRoute('oc_platform_view', array('id' => 6));
        }
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }
        $advert->setTitle("Changement de titre");
        $em->persist($advert);
        

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine
        // Étape 2 : On déclenche l'enregistrement
        $em->flush();
        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array("advert" => $advert));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }


        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine
        // Étape 2 : On déclenche l'enregistrement
        $em->flush();
        return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }

    public function menuAction() {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner')
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
                    // Tout l'intérêt est ici : le contrôleur passe
                    // les variables nécessaires au template !
                    'listAdverts' => $listAdverts
        ));
    }

}
