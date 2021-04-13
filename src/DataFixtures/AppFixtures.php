<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        # Preparation de Faker
        $faker = Factory::create('fr_FR');

        #------------------------| Les categories
        $politique = new Category();
        $politique->setName('Politique');
        $politique->setAlias('politique');
        $manager->persist($politique);

        $economie = new Category();
        $economie->setName('Economie');
        $economie->setAlias('economie');
        $manager->persist($economie);

        $sante = new Category();
        $sante->setName('Sante');
        $sante->setAlias('sante');
        $manager->persist($sante);

        $culture = new Category();
        $culture->setName('Culture');
        $culture->setAlias('culture');
        $manager->persist($culture);

        $sport = new Category();
        $sport->setName('Sport');
        $sport->setAlias('sport');
        $manager->persist($sport);

        // $product = new Product();
        // $manager->persist($product);

        # On sauvegarde le tout dans la BDD
        $manager->flush();

        #------------------------| Les utilisateurs
        # Creation d'un admin
            $admin = new User();
            $admin->setFirstname('Thu');
            $admin->setLastname('NGUYEN');
            $admin->setEmail('thu@email.com');
            $admin->setPassword('test');
            $admin->setRoles(['ROLE_USER']);
            $manager->persist($admin);

        # On sauvegarde le tout dans la BDD

        # ----------------- | Création d'utilisateurs normaux
        for ($i = 0; $i < 5; $i++) {

            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $user->setPassword('test');
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        # On sauvegarde le tout dans la BDD
        $manager->flush();

        #------------------------| Les articles
        for ($i = 0; $i < 5; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence());
            $post->setContent($faker->text());
            $post->setImage($faker->imageUrl(500,350));
            $post->setAlias('lorem-ipsum-dolor-este'); #TODO
            $post->setCategory($politique);  #TODO
            $post->setUser($admin); #TODO
            $manager->persist($post);

            $post2 = new Post();
            $post2->setTitle($faker->sentence());
            $post2->setContent($faker->text());
            $post2->setImage($faker->imageUrl(500,350));
            $post2->setAlias('lorem-ipsum-dolor-este'); #TODO
            $post2->setCategory($culture);  #TODO
            $post2->setUser($admin); #TODO
            $manager->persist($post2);

            $post3 = new Post();
            $post3->setTitle($faker->sentence());
            $post3->setContent($faker->text());
            $post3->setImage($faker->imageUrl(500,350));
            $post3->setAlias('lorem-ipsum-dolor-este'); #TODO
            $post3->setCategory($economie);  #TODO
            $post3->setUser($admin); #TODO
            $manager->persist($post3);

        }
        # On sauvegarde le tout dans la BDD
        $manager->flush();
    }
}