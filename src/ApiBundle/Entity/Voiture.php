<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Voiture
 *
 * @ORM\Table(name="voiture")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\VoitureRepository")
 */
class Voiture
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"infos" })
     */
    private $id;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="prix", type="decimal", precision=10, scale=0)
     */
    private $prix;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="description", type="string", length=255)
     * @JMS\Groups({"infos" })
     */
    private $description;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="boite", type="string", length=255)
     */
    private $boite;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="clim", type="boolean")
     */
    private $clim;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="marque", type="string", length=255)
     */
    private $marque;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="categorie", type="string", length=255)
     */
    private $categorie;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="modele", type="string", length=255)
     */
    private $modele;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseRetrait", type="string", length=255)
     */
    //private $adresseRetrait;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseRestitution", type="string", length=255)
     */
    //private $adresseRestitution;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRetrait", type="datetime")
     */
    //private $dateRetrait;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRestitution", type="datetime")
     */
    //private $dateRestitution;

    /**
     * @var int
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="nbPorte", type="integer")
     */
    private $nbPorte;

    /**
     * @var int
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="nbPassage", type="integer")
     */
    private $nbPassage;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set prix
     *
     * @param string $prix
     *
     * @return Voiture
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return string
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Voiture
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set boite
     *
     * @param string $boite
     *
     * @return Voiture
     */
    public function setBoite($boite)
    {
        $this->boite = $boite;

        return $this;
    }

    /**
     * Get boite
     *
     * @return string
     */
    public function getBoite()
    {
        return $this->boite;
    }

    /**
     * Set clim
     *
     * @param string $clim
     *
     * @return Voiture
     */
    public function setClim($clim)
    {
        $this->clim = $clim;

        return $this;
    }

    /**
     * Get clim
     *
     * @return string
     */
    public function getClim()
    {
        return $this->clim;
    }

    /**
     * Set marque
     *
     * @param string $marque
     *
     * @return Voiture
     */
    public function setMarque($marque)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque
     *
     * @return string
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set categorie
     *
     * @param string $categorie
     *
     * @return Voiture
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set modele
     *
     * @param string $modele
     *
     * @return Voiture
     */
    public function setModele($modele)
    {
        $this->modele = $modele;

        return $this;
    }

    /**
     * Get modele
     *
     * @return string
     */
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * Set adresseRetrait
     *
     * @param string $adresseRetrait
     *
     * @return Voiture
     */
    /*public function setAdresseRetrait($adresseRetrait)
    {
        $this->adresseRetrait = $adresseRetrait;

        return $this;
    }*/

    /**
     * Get adresseRetrait
     *
     * @return string
     */
    /*public function getAdresseRetrait()
    {
        return $this->adresseRetrait;
    }*/

    /**
     * Set adresseRestitution
     *
     * @param string $adresseRestitution
     *
     * @return Voiture
     */
    /*public function setAdresseRestitution($adresseRestitution)
    {
        $this->adresseRestitution = $adresseRestitution;

        return $this;
    }*/

    /**
     * Get adresseRestitution
     *
     * @return string
     */
   /* public function getAdresseRestitution()
    {
        return $this->adresseRestitution;
    }*/

    /**
     * Set dateRetrait
     *
     * @param \DateTime $dateRetrait
     *
     * @return Voiture
     */
    /*public function setDateRetrait($dateRetrait)
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }*/

    /**
     * Get dateRetrait
     *
     * @return \DateTime
     */
    /*public function getDateRetrait()
    {
        return $this->dateRetrait;
    }*/

    /**
     * Set dateRestitution
     *
     * @param \DateTime $dateRestitution
     *
     * @return Voiture
     */
    /*public function setDateRestitution($dateRestitution)
    {
        $this->dateRestitution = $dateRestitution;

        return $this;
    }*/

    /**
     * Get dateRestitution
     *
     * @return \DateTime
     */
    /*public function getDateRestitution()
    {
        return $this->dateRestitution;
    }*/

    /**
     * Set nbPorte
     *
     * @param integer $nbPorte
     *
     * @return Voiture
     */
    public function setNbPorte($nbPorte)
    {
        $this->nbPorte = $nbPorte;

        return $this;
    }

    /**
     * Get nbPorte
     *
     * @return int
     */
    public function getNbPorte()
    {
        return $this->nbPorte;
    }

    /**
     * Set nbPassage
     *
     * @param integer $nbPassage
     *
     * @return Voiture
     */
    public function setNbPassage($nbPassage)
    {
        $this->nbPassage = $nbPassage;

        return $this;
    }

    /**
     * Get nbPassage
     *
     * @return int
     */
    public function getNbPassage()
    {
        return $this->nbPassage;
    }
}

