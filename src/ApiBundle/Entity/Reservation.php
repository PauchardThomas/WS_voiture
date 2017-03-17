<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idUser", type="integer")
     */
    private $idUser;

    /**
     * @var int
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="idVoiture", type="integer")
     */
    private $idVoiture;

    /**
     * @var \DateTime
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="dateDebutRes", type="datetime")
     */
    private $dateDebutRes;

    /**
     * @var \DateTime
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="dateFinRes", type="datetime")
     */
    private $dateFinRes;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="lieuRetrait", type="string", length=255)
     */
    private $lieuRetrait;

    /**
     * @var string
     * @JMS\Groups({"infos" })
     * @ORM\Column(name="lieuRetour", type="string", length=255)
     */
    private $lieuRetour;

     /**
     * @var string
     * @JMS\Groups({ "resa","infos" })
     * @ORM\Column(name="numReservation", type="string", length=255)
     */
    private $numReservation;


    /**
     * Get numresa
     *
     * @return int
     */
    public function getNumReservation()
    {
    	return $this->numReservation;
    }
    
    /**
     * Set numReservation
     *
     * @param integer $numreservation
     *
     * @return Reservation
     */
    public function setNumReservation($numReservation)
    {
    	$this->numReservation = $numReservation;
    
    	return $this;
    }
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
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Reservation
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idVoiture
     *
     * @param integer $idVoiture
     *
     * @return Reservation
     */
    public function setIdVoiture($idVoiture)
    {
        $this->idVoiture = $idVoiture;

        return $this;
    }

    /**
     * Get idVoiture
     *
     * @return int
     */
    public function getIdVoiture()
    {
        return $this->idVoiture;
    }

    /**
     * Set dateDebutRes
     *
     * @param \DateTime $dateDebutRes
     *
     * @return Reservation
     */
    public function setDateDebutRes($dateDebutRes)
    {
        $this->dateDebutRes = $dateDebutRes;

        return $this;
    }

    /**
     * Get dateDebutRes
     *
     * @return \DateTime
     */
    public function getDateDebutRes()
    {
        return $this->dateDebutRes;
    }

    /**
     * Set dateFinRes
     *
     * @param \DateTime $dateFinRes
     *
     * @return Reservation
     */
    public function setDateFinRes($dateFinRes)
    {
        $this->dateFinRes = $dateFinRes;

        return $this;
    }

    /**
     * Get dateFinRes
     *
     * @return \DateTime
     */
    public function getDateFinRes()
    {
        return $this->dateFinRes;
    }

    /**
     * Set lieuRetrait
     *
     * @param string $lieuRetrait
     *
     * @return Reservation
     */
    public function setLieuRetrait($lieuRetrait)
    {
        $this->lieuRetrait = $lieuRetrait;

        return $this;
    }

    /**
     * Get lieuRetrait
     *
     * @return string
     */
    public function getLieuRetrait()
    {
        return $this->lieuRetrait;
    }

    /**
     * Set lieuRetour
     *
     * @param string $lieuRetour
     *
     * @return Reservation
     */
    public function setLieuRetour($lieuRetour)
    {
        $this->lieuRetour = $lieuRetour;

        return $this;
    }

    /**
     * Get lieuRetour
     *
     * @return string
     */
    public function getLieuRetour()
    {
        return $this->lieuRetour;
    }
}

