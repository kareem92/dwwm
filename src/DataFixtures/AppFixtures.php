<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

	private $encoder;

	public function __construct(UserPasswordEncoderInterface $encoder) {

		$this->encoder=$encoder ;
	}

	


	public function load(ObjectManager $manager)
	{
        // $product = new Product();
        // $manager->persist($product);

		$adminRole = new Role();
		$adminRole->setTitle('ROLE_ADMIN');
		$manager->persist($adminRole);

		$adminUser = new User();
		$adminUser->setFirstName('Eric')
				  ->setLastName('Devolder')
				  ->setEmail('eric.devolder@ac-nice.fr')
				  ->setHash($this->encoder->encodePassword($adminUser, "password"))
				  ->setPicture('https://via.placeholder.com/64')
				  ->setIntroduction('Introduction Eric')
				  ->setDescription('Description Eric')
				  ->setSlug('eric-devolder')
				  ->addUserRole($adminRole);
				  
		$manager->persist($adminUser);

		$slugify = new Slugify();
		// $slugify->slugify('Hello World!') on aura alors hello-world
		$title="Titre de l'annonce n°";
		//$slug=$slugify->slugify($title);	

		for ($k=1; $k <5 ; $k++) {
		$user = new User();
		$user->setSlug($slugify->slugify("prenom$k-nom$k"))
			 ->setFirstName("prenom$k")
			 ->setLastName("nom$k")
			 ->setPicture("https://via.placeholder.com/64")
			 ->setEmail("test$k@test.fr")
			 ->setIntroduction("introduction$k")
			 ->setDescription("Description$k");
			 $encoded = $this->encoder->encodePassword($user, "pass");
			 $user->setHash("$encoded");

			 $manager->persist($user);

		for ($i=1; $i <mt_rand(4,7) ; $i++) {
			$ad= new Ad();
			$ad->setTitle($title.$i.$k)
			->setSlug($slugify->slugify($ad->getTitle()))
			->setCoverImage("https://via.placeholder.com/350")
			->setIntroduction("C'est une introduction")
			->setContent("<p>Je suis le contenu</p>")
			->setPrice(mt_rand(60,500))
			->setRooms(mt_rand(1,8))
			->setAuthor($user);

			for ($j=1; $j <=mt_rand(1,5) ; $j++) {
				$image = new Image() ;
				$image->setUrl("https://via.placeholder.com/350")
					  -> setCaption("Légende de l'image $j")
					  -> setAd($ad);

					$manager->persist($image);


			}	

			for ($j=1; $j <=mt_rand(1,5) ; $j++) {

				$booking=new Booking();
				$booking->setCreatedAt(new \DateTime())
				         ->setStartDate(new \DateTime("+5 days"))
				         ->setEndDate(new \DateTime("+15 days"))
				         ->setAmount($ad->getPrice()*10)
				         ->setComment("commentaire de la réservation $j")
				         ->setAd($ad)
				         ->setBooker($user);

				        $manager->persist($booking);

			}



			$manager->persist($ad);
		}
	}
		$manager->flush();
	}
}












