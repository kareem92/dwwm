<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Repository\getRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\redirectToRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {

    	//$repo = $this->getDoctrine()->getRepository(Ad::class);
    	//$repo->findOne(12);
    	//$repo->findOneByTitle("Titre de l'article");
    	//$repo->findOneByX();

    	$ads = $repo->findAll();
    	dump($ads);
      return $this->render('ad/index.html.twig', [
        'ads' => $ads
    ]);
  }

    	/**
     * @Route("/ads/new", name="ads_create")
     *@IsGranted("ROLE_USER")
     */
        public function create(Request $request,ObjectManager $manager)
        {
           $ad=new Ad() ;

           $form=$this->createForm(AnnonceType::class, $ad) ;

           $form->handleRequest($request);

           if ($form->isSubmitted() && $form->isValid() ) {

                $ad->setAuthor($this->getUser());

                if (!$ad->getSlug()) {
                $slugify = new Slugify();
                $ad->setSlug($slugify->slugify($ad->getTitle()));
                }

            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            
            }


            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "l'annonce {$ad->getTitle()} à bien été enregistrée !");


            return $this->redirectToRoute('ads_show',['slug' => $ad->getSlug()]);
        }


        
        return $this->render('ad/new.html.twig' ,[
            'form' => $form->createView(),
        ] );


    }

        /**
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * permet d'afficher le formulaire d'édition
     *@Security("is_granted('ROLE_USER') and user == ad.getAuthor() " , message ="Cette annonce ne vous appartient pas !")
     */
        public function edit(Ad $ad  , Request $request,ObjectManager $manager)
        {
           
           $form=$this->createForm(AnnonceType::class, $ad) ;
          // dump($request);
          //dump($request->request->get('annonce'));
          // dump($request->request->get('annonce')['slug']);

          
            $slugify = new Slugify();
            $data = $request->request->get('annonce');
            $data['slug']=$slugify->slugify($request->request->get('annonce')['title']);
            $request->request->set('annonce',$data);           
           $form->handleRequest($request);

           if ($form->isSubmitted() && $form->isValid() ) {

                if (!$ad->getSlug()) {
                $slugify = new Slugify();
                $ad->setSlug($slugify->slugify($ad->getTitle()));
              }

            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            
            }


            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "lannonce {$ad->getTitle()} a bien etait modifiée !");


            return $this->redirectToRoute('ads_show',['slug' => $ad->getSlug()]);
        }


        
        return $this->render('ad/edit.html.twig' ,[
            'form' => $form->createView(),
            'ad' => $ad
        ] );


    }





    /**
     * @Route("/ads/{slug}", name="ads_show")
     */
    public function show(Ad $ad)
    {

    	dump($ad);
      return $this->render('ad/show.html.twig', [
        'ad' => $ad
    ]);
  }

   /**
     * @Route("/ads/{slug}/delete", name="ads_delete")
     *@Security("is_granted('ROLE_USER') and user == ad.getAuthor() " , message ="Vous ne pouvez pas supprimer cette annonce ! !")
     */
    public function delete(Ad $ad,ObjectManager $manager)
    {
        $manager->remove($ad);
        $manager->flush();

       $this->addFlash(
       'success',"L'annonce a bien été supprimée !");

       return $this->redirectToRoute('account_index');
    
  }


}




