<?php

namespace App\DataFixtures;

use App\Entity\Pain;
use App\Entity\Oignon;
use App\Entity\Sauce;
use App\Entity\Image;
use App\Entity\Commentaire;
use App\Entity\Burger;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
      

        $pain = new Pain();
        $pain->setName('pain');
        $manager->persist($pain);

        $oignon = new Oignon();
        $oignon->setName('oignon');
        $manager->persist($oignon);

        $sauce = new Sauce();
        $sauce->setName('sauce');
        $manager->persist($sauce);

        $image = new Image();
        $image->setURL('burger.jpg');
        $image->setName('Image Burger'); 
        $manager->persist($image);

      

        $burger = new Burger();
        $burger->setName('burger');
        $burger->setPrice('10.80');
        $burger->setPain($pain);
        $burger->addOignon($oignon);
        $burger->addSauce($sauce);
        $burger->setImage($image);
        $manager->persist($burger);

        $commentaire = new Commentaire();
        $commentaire->setContenu('pas mal');
        $commentaire->setBurger($burger);
        $manager->persist($commentaire);




        $manager->flush();
    }
}
