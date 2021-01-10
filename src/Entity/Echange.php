<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Echange
 *
 * @ORM\Table(name="echange")
 * @ORM\Entity
 */
class Echange
{
    /**
     * @var int
     *
     * @ORM\Column(name="IDECHANGE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idechange;

    /**
     * @var int
     *
     * @ORM\Column(name="STATUS", type="smallint", nullable=false)
     */
    private $status;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Lecteur", mappedBy="idechange")
     */
    private $idlecteur;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idlecteur = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdechange(): ?int
    {
        return $this->idechange;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Lecteur[]
     */
    public function getIdlecteur(): Collection
    {
        return $this->idlecteur;
    }

    public function addIdlecteur(Lecteur $idlecteur): self
    {
        if (!$this->idlecteur->contains($idlecteur)) {
            $this->idlecteur[] = $idlecteur;
            $idlecteur->addIdechange($this);
        }

        return $this;
    }

    public function removeIdlecteur(Lecteur $idlecteur): self
    {
        if ($this->idlecteur->removeElement($idlecteur)) {
            $idlecteur->removeIdechange($this);
        }

        return $this;
    }

}
