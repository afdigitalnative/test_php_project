<?php

namespace App\Entity;

use App\Repository\MaxTransactionVolumeRepository;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MaxTransactionVolumeRepository::class)
 */
class MaxTransactionVolume
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="AccountBalance", mappedBy="maxTransactionVolumes")
     */
    private $accounts;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull
     */
    private $maxVolume;

    public function __construct() {
        $this->accounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|AccountBalance[]
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function getMaxVolume(): ?int
    {
        return $this->maxVolume;
    }

    public function setMaxVolume(int $maxVolume): self
    {
        $this->maxVolume = $maxVolume;

        return $this;
    }

    public function addAccount(AccountBalance $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts[] = $account;
            $account->addMaxTransactionVolume($this);
        }

        return $this;
    }

    public function removeAccount(AccountBalance $account): self
    {
        if ($this->accounts->removeElement($account)) {
            $account->removeMaxTransactionVolume($this);
        }

        return $this;
    }
}
