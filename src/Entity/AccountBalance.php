<?php

namespace App\Entity;

use App\Repository\AccountBalanceRepository;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AccountBalanceRepository::class)
 * @UniqueEntity("id")
 */
class AccountBalance
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Uuid
     * @Assert\NotNull
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull
     */
    private $balance;

    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="account")
     */
    private $transactions;

    /**
     * @ORM\ManyToMany(targetEntity="MaxTransactionVolume", inversedBy="accounts")
     * @ORM\JoinTable(name="accounts_max_transaction_volumes",
     *  joinColumns={@ORM\JoinColumn(name="account_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="max_transaction_volume_id", referencedColumnName="id")}
     * )
     */
    private $maxTransactionVolumes;
    
    public function __construct() {
        $this->maxTransactionVolumes = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection|MaxTransactionVolume[]
     */
    public function getMaxTransactionVolumes(): Collection
    {
        return $this->maxTransactionVolumes;
    }

    public function addMaxTransactionVolume(MaxTransactionVolume $maxTransactionVolume): self
    {
        if (!$this->maxTransactionVolumes->contains($maxTransactionVolume)) {
            $this->maxTransactionVolumes[] = $maxTransactionVolume;
        }

        return $this;
    }

    public function removeMaxTransactionVolume(MaxTransactionVolume $maxTransactionVolume): self
    {
        $this->maxTransactionVolumes->removeElement($maxTransactionVolume);

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getAccount() === $this) {
                $transaction->setAccount(null);
            }
        }

        return $this;
    }


}
