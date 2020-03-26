<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{

    /**
     * L'encodeur de mot de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder; 


    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('de');


        for($u = 0; $u < 5; $u++){

            $chrono = 1; //à chaque fois qu'on repasse sur un nouvel utilisateur on defnit le chrono(identifiant de la facture) à 1.

            $user = new User();
            $hash = $this->encoder->encodePassword($user, 'secret');
            $user->setEmail($faker->email)
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setPassword($hash);
            $manager->persist($user);

            
            for ($c = 0; $c < mt_rand(5, 50); $c++) {

                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName)
                    ->setEmail($faker->email)
                    ->setCompany($faker->company)
                    ->setUser($user);
                $manager->persist($customer);



                for ($i = 0; $i < mt_rand(3, 10); $i++) {

                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2, 250, 7000))
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setChrono($chrono)
                        ->setCustomer($customer);
                    $chrono++;
                    $manager->persist($invoice);
                }
            }

        }


       

        $manager->flush();
    }
}
