<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bibliotheque
 *
 * @ORM\Table(name="bibliotheque", indexes={@ORM\Index(name="FK_BIBLIOTH_POSSEDER3_LECTEUR", columns={"IDLECTEUR"})})
 * @ORM\Entity
 */
class Bibliotheque
{
    /**
     * @var int
     *
     * @ORM\Column(name="IDBIBLIOTHEQUE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idbibliotheque;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBIBLIOTHEQUE", type="string", length=50, nullable=true)
     */
    private $nombibliotheque;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCBIBLIOTHEQUE", type="text", length=16777215, nullable=true)
     */
    private $descbibliotheque;

    /**
     * @var \Lecteur
     *
     * @ORM\ManyToOne(targetEntity="Lecteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IDLECTEUR", referencedColumnName="IDLECTEUR")
     * })
     */
    private $idlecteur;

    public function getIdbibliotheque(): ?int
    {
        return $this->idbibliotheque;
    }

    public function getNombibliotheque(): ?string
    {
        return $this->nombibliotheque;
    }

    public function setNombibliotheque(?string $nombibliotheque): self
    {
        $this->nombibliotheque = $nombibliotheque;

        return $this;
    }

    public function getDescbibliotheque(): ?string
    {
        return $this->descbibliotheque;
    }

    public function setDescbibliotheque(?string $descbibliotheque): self
    {
        $this->descbibliotheque = $descbibliotheque;

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


}
