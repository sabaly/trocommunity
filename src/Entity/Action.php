<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table(name="action", indexes={@ORM\Index(name="FK_ACTION_AGIR_LECTEUR", columns={"IDLECTEUR"})})
 * @ORM\Entity
 */
class Action
{
    /**
     * @var int
     *
     * @ORM\Column(name="IDACTION", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idaction;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPEACTION", type="string", length=150, nullable=false)
     */
    private $typeaction;

    /**
     * @var \Lecteur
     *
     * @ORM\ManyToOne(targetEntity="Lecteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IDLECTEUR", referencedColumnName="IDLECTEUR")
     * })
     */
    private $idlecteur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Livre", mappedBy="idaction")
     */
    private $idlivre;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idlivre = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdaction(): ?int
    {
        return $this->idaction;
    }

    public function getTypeaction(): ?string
    {
        return $this->typeaction;
    }

    public function setTypeaction(string $typeaction): self
    {
        $this->typeaction = $typeaction;

        return $this;
    }

    public function getIdlecteur(): ?Lecteur
    {
        return $this->idlecteur;
    }

    public function setIdlecteur(?Lecteur $idlecteur): self
    {
        $this->idlecteur = $idlecteur;

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getIdlivre(): Collection
    {
        return $this->idlivre;
    }

    public function addIdlivre(Livre $idlivre): self
    {
        if (!$this->idlivre->contains($idlivre)) {
            $this->idlivre[] = $idlivre;
            $idlivre->addIdaction($this);
        }

        return $this;
    }

    public function removeIdlivre(Livre $idlivre): self
    {
        if ($this->idlivre->removeElement($idlivre)) {
            $idlivre->removeIdaction($this);
        }

        return $this;
    }

}
