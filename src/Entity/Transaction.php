<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ORM\Table(name="`transaction`")
 * @UniqueEntity("id")
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Uuid
     * @Assert\NotNull
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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
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
