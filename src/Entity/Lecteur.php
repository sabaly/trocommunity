<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lecteur
 *
 * @ORM\Table(name="lecteur")
 * @ORM\Entity
 */
class Lecteur
{
    /**
     * @var int
     *
     * @ORM\Column(name="IDLECTEUR", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idlecteur;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMLECTEUR", type="string", length=50, nullable=false)
     */
    private $nomlecteur;

    /**
     * @var string
     *
     * @ORM\Column(name="PRENOMLECTEUR", type="string", length=150, nullable=false)
     */
    private $prenomlecteur;

    /**
     * @var string
     *
     * @ORM\Column(name="TELLECTEUR", type="string", length=20, nullable=false)
     */
    private $tellecteur;

    /**
     * @var string
     *
     * @ORM\Column(name="ADRESSELECTEUR", type="string", length=150, nullable=false)
     */
    private $adresselecteur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Echange", inversedBy="idlecteur")
     * @ORM\JoinTable(name="valider",
     *   joinColumns={
     *     @ORM\JoinColumn(name="IDLECTEUR", referencedColumnName="IDLECTEUR")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="IDECHANGE", referencedColumnName="IDECHANGE")
     *   }
     * )
     */
    private $idechange;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idechange = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdlecteur(): ?int
    {
        return $this->idlecteur;
    }

    public function getNomlecteur(): ?string
    {
        return $this->nomlecteur;
    }

    public function setNomlecteur(string $nomlecteur): self
    {
        $this->nomlecteur = $nomlecteur;

        return $this;
    }

    public function getPrenomlecteur(): ?string
    {
        return $this->prenomlecteur;
    }

    public function setPrenomlecteur(string $prenomlecteur): self
    {
        $this->prenomlecteur = $prenomlecteur;

        return $this;
    }

    public function getTellecteur(): ?string
    {
        return $this->tellecteur;
    }

    public function setTellecteur(string $tellecteur): self
    {
        $this->tellecteur = $tellecteur;

        return $this;
    }

    public function getAdresselecteur(): ?string
    {
        return $this->adresselecteur;
    }

    public function setAdresselecteur(string $adresselecteur): self
    {
        $this->adresselecteur = $adresselecteur;

        return $this;
    }

    /**
     * @return Collection|Echange[]
     */
    public function getIdechange(): Collection
    {
        return $this->idechange;
    }

    public function addIdechange(Echange $idechange): self
    {
        if (!$this->idechange->contains($idechange)) {
            $this->idechange[] = $idechange;
        }

        return $this;
    }

    public function removeIdechange(Echange $idechange): self
    {
        $this->idechange->removeElement($idechange);

        return $this;
    }

}
