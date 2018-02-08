<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;

class AdvertController extends Controller
{
    
    public function menuAction($limit){
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
    
    public function indexAction($page,Request $request)
    {
        if ($page<1){
            throw new NotFoundHttpException('ceci est le message');
        }
        
        $testSpam = "dvzvevetbietbjetjbiteĵbetibjetibjetibjeeiojebiêbjiebjiĝeeeeeeeeeeeeeeeeebrnnu,hgnqrngnfggjqjgjjyjytjtkqkyq";
        if($this->get('oc_platform.antispam')->isSpam($testSpam,true)){
        }
        
        


        $listAdverts = array(
            array(
                'title'   => 'Recherche développpeur Symfony',
                'id'      => 1,
                'author'  => 'jacky',
                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Mission de webmaster',
                'id'      => 2,
                'author'  => 'Pierro',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Offre de stage webdesigner',
                'id'      => 3,
                'author'  => 'Mathew',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'    => new \Datetime())
        );


        $templateParams = [
            "listAdverts"=>$listAdverts
        ];
        
        return $this->render('OCPlatformBundle:Advert:index.html.twig',$templateParams);
    }

    /**
     * @param $id
     */
    public function viewAction($id){
        
        $em = $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('OCPlatformBundle:Advert');
        $advert = $repo->find($id);
        
        if (null === $advert){
            throw new NotFoundHttpException('PAs d\'avert avec cet id');
        }

        // On récupère la liste des candidatures de cette annonce

        $listApplications = $em
            ->getRepository('OCPlatformBundle:Application')
            ->findBy(['advert' => $advert])
        ;
        
        
        return $this->render('@OCPlatform/Advert/view.html.twig',['advert'=>$advert,"listApplications"=>$listApplications]);
    }
    
    public function addAction(Request $request){
        // Création de l'entité Advert
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony2.');
        $advert->setAuthor('Jona');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Liège. Blabla…");

        // Création de l'entité Image
        $image = new Image();
        $image->setUrl('https://assets3.thrillist.com/v1/image/2547068/size/tmg-article_tall;jpeg_quality=20.jpg');
        $image->setAlt('Smiling dog');

        // On lie l'image à l'annonce
        $advert->setImage($image);

        // Création d'une première candidature

        $application1 = new Application();
        $application1->setAuthor('Martine');
        $application1->setContent("J'ai toutes les qualités requises.");
        $application1->setAdvert($advert);


        // Création d'une deuxième candidature par exemple
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent("Je suis très motivé.");
        $application2->setAdvert($advert);

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // Étape 1 : On « persiste » l'entité
        $em->persist($application1);
        $em->persist($application2);
        $em->persist($advert);
        $em->flush();
        return $this->redirectToRoute('oc_platform_view',['id'=>$advert->getId()]);
        
        return $this->render('@OCPlatform/Advert/add.html.twig');
    }
    
    public function editAction($id,Request $request){
//        $em = $this->get('doctrine')->getManager();
//        $em = $this->getDoctrine()->getManager();
        $em = $this->get('doctrine.orm.entity_manager');
        $advertRepo = $em->getRepository('OCPlatformBundle:Advert');
        $advert = $advertRepo->findOneById($id);
        
        

        if (null === $advert){
            throw new NotFoundHttpException("Pas d'advert");
        }
        
        $catRepo = $em->getRepository('OCPlatformBundle:Category');
        $categories = $catRepo->findAll();
        
        foreach ($categories as $category){
            $advert->addCategory($category);
        }
        
        $skills = $em->getRepository('OCPlatformBundle:Skill')->findAll();
        for($i=0;$i<count($skills);$i++){
            if ($i%2 == 0){
                $advertSkill = new AdvertSkill();
                $advertSkill->setSkill($skills[$i])
                    ->setLevel(AdvertSkill::LEVEL_GURU)
                ;
                $advert->addAdvertSkill($advertSkill);
//                $advertSkill->setAdvert($advert);
//                $advertSkill;
//                $em->persist($advertSkill);
            }
        }
        // $em->persist($advert); //Pas nécéssaire puisque l'entité est déjà attachée à l'EM
        $em->flush();
        
        return $this->redirectToRoute('oc_platform_view',['id'=>$advert->getId()]);
        

        
        return $this->render('@OCPlatform/Advert/edit.html.twig',['advert'=>$advert]);
    }
    
    public function deleteAction($id){
        
        $em = $this->get('doctrine.orm.entity_manager');
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        
        foreach($advert->getCategories() as $category){
            $advert->removeCategory($category);
        }
        
        $em->flush();
        
        return $this->redirectToRoute('oc_platform_view',['id'=>$advert->getId()]);
        
        return $this->render('@OCPlatform/Advert/delete.html.twig',['id'=>$id]);
    }
    
    
}
