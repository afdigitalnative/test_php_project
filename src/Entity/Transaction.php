<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ORM\Table(name="`transaction`")
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AccountBalance", inversedBy="transactions")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $account;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull
     * @Assert\Type("integer")
     */
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): ?AccountBalance
    {
        return $this->account;
    }

    public function setAccount(?AccountBalance $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
