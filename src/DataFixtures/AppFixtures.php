<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * l'encodeur de mot de passe
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i=0; $i < 10; $i++) { 
            $user = new User();

            $chrono = 1;
            
            $hash = $this->encoder->hashPassword($user, "password");

            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($hash);
            $manager->persist($user);
    
            for ($c=0; $c < mt_rand(5, 20); $c++) { 
                $customer = new Customer();
                $customer->setfirstName($faker->firstName())
                    ->setLastName($faker->lastName)
                    ->setCompagny($faker->company())
                    ->setEmail($faker->email())
                    ->setUser($user);
                $manager->persist($customer);

                for ($i=0; $i < mt_rand(3, 10); $i++) { 
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($chrono);
                    $chrono++;
                    $manager->persist($invoice);
                }
            }
        }
        
        $manager->flush();
    }
}
