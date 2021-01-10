<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Livre
 *
 * @ORM\Table(name="livre", indexes={@ORM\Index(name="FK_LIVRE_APPARTENI_CATEGORI", columns={"IDCATEGORIE"}), @ORM\Index(name="FK_LIVRE_CONCERNER_ECHANGE", columns={"IDECHANGE"}), @ORM\Index(name="FK_LIVRE_CONTENIR_BIBLIOTH", columns={"IDBIBLIOTHEQUE"})})
 * @ORM\Entity
 */
class Livre
{
    /**
     * @var int
     *
     * @ORM\Column(name="IDLIVRE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idlivre;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMLIVRE", type="string", length=50, nullable=false)
     */
    private $nomlivre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCLIVRE", type="text", length=16777215, nullable=true)
     */
    private $desclivre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="IMAGE", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DATEAJOUT", type="date", nullable=true)
     */
    private $dateajout;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DATEMODIF", type="date", nullable=true)
     */
    private $datemodif;

    /**
     * @var \Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IDCATEGORIE", referencedColumnName="IDCATEGORIE")
     * })
     */
    private $idcategorie;

    /**
     * @var \Echange
     *
     * @ORM\ManyToOne(targetEntity="Echange")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IDECHANGE", referencedColumnName="IDECHANGE")
     * })
     */
    private $idechange;

    /**
     * @var \Bibliotheque
     *
     * @ORM\ManyToOne(targetEntity="Bibliotheque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IDBIBLIOTHEQUE", referencedColumnName="IDBIBLIOTHEQUE")
     * })
     */
    private $idbibliotheque;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Action", inversedBy="idlivre")
     * @ORM\JoinTable(name="subir",
     *   joinColumns={
     *     @ORM\JoinColumn(name="IDLIVRE", referencedColumnName="IDLIVRE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="IDACTION", referencedColumnName="IDACTION")
     *   }
     * )
     */
    private $idaction;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idaction = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdlivre(): ?int
    {
        return $this->idlivre;
    }

    public function getNomlivre(): ?string
    {
        return $this->nomlivre;
    }

    public function setNomlivre(string $nomlivre): self
    {
        $this->nomlivre = $nomlivre;

        return $this;
    }

    public function getDesclivre(): ?string
    {
        return $this->desclivre;
    }

    public function setDesclivre(?string $desclivre): self
    {
        $this->desclivre = $desclivre;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDateajout(): ?\DateTimeInterface
    {
        return $this->dateajout;
    }

    public function setDateajout(?\DateTimeInterface $dateajout): self
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    public function getDatemodif(): ?\DateTimeInterface
    {
        return $this->datemodif;
    }

    public function setDatemodif(?\DateTimeInterface $datemodif): self
    {
        $this->datemodif = $datemodif;

        return $this;
    }

    public function getIdcategorie(): ?Categorie
    {
        return $this->idcategorie;
    }

    public function setIdcategorie(?Categorie $idcategorie): self
    {
        $this->idcategorie = $idcategorie;

        return $this;
    }

    public function getIdechange(): ?Echange
    {
        return $this->idechange;
    }

    public function setIdechange(?Echange $idechange): self
    {
        $this->idechange = $idechange;

        return $this;
    }

    public function getIdbibliotheque(): ?Bibliotheque
    {
        return $this->idbibliotheque;
    }

    public function setIdbibliotheque(?Bibliotheque $idbibliotheque): self
    {
        $this->idbibliotheque = $idbibliotheque;

        return $this;
    }

    /**
     * @return Collection|Action[]
     */
    public function getIdaction(): Collection
    {
        return $this->idaction;
    }

    public function addIdaction(Action $idaction): self
    {
        if (!$this->idaction->contains($idaction)) {
            $this->idaction[] = $idaction;
        }

        return $this;
    }

    public function removeIdaction(Action $idaction): self
    {
        $this->idaction->removeElement($idaction);

        return $this;
    }

}
