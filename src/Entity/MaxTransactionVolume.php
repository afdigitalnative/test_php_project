<?php

namespace App\Entity;

use App\Repository\MaxTransactionVolumeRepository;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="integer")
     */
    private $maxVolume;

    public function getId(): ?int
    {
        return $this->id;
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
}
