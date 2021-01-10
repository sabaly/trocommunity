<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte
 *
 * @ORM\Table(name="compte", indexes={@ORM\Index(name="FK_COMPTE_CREER3_LECTEUR", columns={"IDLECTEUR"})})
 * @ORM\Entity
 */
class Compte
{
    /**
     * @var int
     *
     * @ORM\Column(name="IDCOMPTE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcompte;

    /**
     * @var string
     *
     * @ORM\Column(name="IDENTIFIANT", type="string", length=50, nullable=false)
     */
    private $identifiant;

    /**
     * @var string
     *
     * @ORM\Column(name="PASSWORD", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="CREATIONDATE", type="date", nullable=true)
     */
    private $creationdate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="MODIFDATE", type="date", nullable=true)
     */
    private $modifdate;

    /**
     * @var \Lecteur
     *
     * @ORM\ManyToOne(targetEntity="Lecteur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IDLECTEUR", referencedColumnName="IDLECTEUR")
     * })
     */
    private $idlecteur;

    public function getIdcompte(): ?int
    {
        return $this->idcompte;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreationdate(): ?\DateTimeInterface
    {
        return $this->creationdate;
    }

    public function setCreationdate(?\DateTimeInterface $creationdate): self
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    public function getModifdate(): ?\DateTimeInterface
    {
        return $this->modifdate;
    }

    public function setModifdate(?\DateTimeInterface $modifdate): self
    {
        $this->modifdate = $modifdate;

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
