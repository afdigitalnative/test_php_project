<?php
// src/DataFixtures/TransactionFixture.php
namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Transaction;
use App\Entity\AccountBalance;
use App\Entity\MaxTransactionVolume;
use App\Factory\TransactionFactory;

class TransactionFixture extends Fixture
{
    protected $faker;
    private $accountUuidList = [
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
    private $transactionUuidList = [
        "5b860238-7273-4fff-9dc1-59fe14bf6cc5",
        "2536e7de-b5df-4717-8a56-b18bf73ecbf4",
        "e861a5ab-c523-420d-b17f-f7c47288f66f",
        "e6b0b387-6400-431b-94a9-fc7dd2a533ce",
        "82768913-9ee4-4c2f-9e3d-99f14cb48170",
        "a284840f-0b17-4250-928a-81f4c7dc4058",
        "219285b1-4055-4d78-9dce-52a5d30a8143",
        "92e847de-0fe9-483c-b931-c7493eaa10fa",
        "d3bc0163-c8b9-4df8-81cc-ad2459841b5a",
        "448978c8-4b46-456b-adf6-c18544b9bd28",
    ];

    public function load(ObjectManager $manager)
    {
        // $this->manager = $manager;
        $this->faker = Factory::create();

        /* Accounts */
        $addedAccounts = [];
        foreach ($this->accountUuidList as $uuid) {
            $account = new AccountBalance();
            $account->setId($uuid);
            $account->setBalance($this->faker->numberBetween(1, 10000) * 100);
            $manager->persist($account);
            $addedAccounts[] = $account;

            /* Max T. Volumes */
            for ($i = 1; $i <= $this->faker->numberBetween(1, 3); $i++) {
                $maxVolume = new MaxTransactionVolume();
                $maxVolume->setMaxVolume($this->faker->numberBetween(0, 20));
                $maxVolume->addAccount($account);
                $manager->persist($maxVolume);
                $account->addMaxTransactionVolume($maxVolume);
            }
        }

        /* Transactions */
        foreach ($this->transactionUuidList as $uuid) {
            $transaction = new Transaction();
            $transaction->setId($uuid);
            $transaction->setAmount($this->faker->numberBetween(1, 10) * 100);
            $transaction->setAccount(
                $addedAccounts[$this->faker->numberBetween(
                    0,
                    count($addedAccounts) - 1
                )]
            );
            $manager->persist($transaction);
        }
        $manager->flush();

        $hasMaxVolume = true;
        $maxSavedVolume = $manager->getRepository(MaxTransactionVolume::class)
            ->findMaxTransactionVolume();

        if (!$maxSavedVolume) {
            $maxSavedVolume = new MaxTransactionVolume();
            $hasMaxVolume = false;
        }

        $transactionsVolumes = $manager->getRepository(Transaction::class)
            ->findTransactionVolume();

        $tNumber = $transactionsVolumes[0]['tNumber'];
        $maxSavedVolume->setMaxVolume($tNumber);
        foreach ($transactionsVolumes as $tVolume) {
            if ($tNumber === $tVolume['tNumber']) {
                $maxSavedVolume->addAccount(
                    $tVolume['transaction']->getAccount()
                );
                continue;
            }
            break;
        }
        if (!$hasMaxVolume) {
            $manager->persist($maxSavedVolume);
        }

        $manager->flush();
    }
}
