<?php
// src/DataFixtures/TransactionFixture.php
namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Transaction;
use App\Entity\AccountBalance;
use App\Entity\MaxTransactionVolume;

class TransactionFixture extends Fixture
{
    protected $faker;
    private $uuidList = [
        "55e3bd34-1755-4528-afcf-be03fefd1d98",
        "98aee232-ba1d-4237-8dbf-950f7269f4d2",
        "a2391e18-d13b-4315-9a2c-0a8b6ac1cce8",
        "de136d39-14e0-4bbb-9d98-952bb138d097",
        "f9d90134-c120-462e-b3cd-b03944e74911",
        "a7e0fefc-9ff7-454e-88c1-2f047cdea851",
        "c8edd7bb-32bb-4e5a-880d-a423c778f255",
        "b205e3b3-1818-4492-941a-2b23bc0dcc39",
        "5c9afbc1-c94f-49be-95e8-d3b44ecc0c7e",
        "101e3d1c-4faa-4b5a-9e2d-0bc86e98201b",
    ];

    public function load(ObjectManager $manager)
    {
        // $this->manager = $manager;
        $this->faker = Factory::create();

        /* Accounts */
        foreach ($this->uuidList as $uuid) {
            $account = new AccountBalance();
            $account->setId($uuid);
            $account->setBalance($this->faker->numberBetween(1, 10000) * 100);
            $manager->persist($account);

            /* Transactions */
            for ($i = 1; $i <= $this->faker->numberBetween(1, 10); $i++) {
                $transaction = new Transaction();
                $transaction->setAmount($this->faker->numberBetween(1, 10) * 100);
                $transaction->setAccount($account);
                $manager->persist($transaction);
            }

            /* Max T. Volumes */
            for ($i = 1; $i <= $this->faker->numberBetween(1, 3); $i++) {
                $maxVolume = new MaxTransactionVolume();
                $maxVolume->setMaxVolume($this->faker->numberBetween(0, 20));
                $maxVolume->addAccount($account);
                $manager->persist($maxVolume);
                $account->addMaxTransactionVolume($maxVolume);
            }
        }

        $manager->flush();
    }
}
