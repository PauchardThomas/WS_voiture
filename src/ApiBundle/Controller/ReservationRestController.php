<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Entity\Reservation;
use ApiBundle\Entity\ReservationUser;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ReservationRestController extends BaseController
{
	private $dateRetour;
	private $adresseRetrait;
	private $adresseRetour;
	private $numReservation;
	
	/**
   * @ApiDoc(
   * 	resource="/api/reservations",
   *    description="Permet de reserver une voiture",
   *    requirements={
   *      {"name"="token", "requirement"="obligatory", "dataType"="string"},
   *      {"name"="idVoiture","requirement"="obligatory", "dataType"="integer"},
   *      {"name"="adresseRetour","requirement"="obligatory", "dataType"="string"},
   *      {"name"="adresseRetrait","requirement"="obligatory", "dataType"="string"},
   *      {"name"="dateRetour","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 02:12:2017:10:30"},
   *      {"name"="dateRetrait","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 04:12:2017:19:00"}
   *  },
   *  statusCodes={
   *  	200 = "Successfull",
   *  	400 = "Bad request | Certains champs sont manquants ou les dates sont mals renseign�es",
   *  	403 = "Forbidden | Connexion refus�, token invalide | Voiture invalide"
   *  },
   *  output={
        "class"   = "ApiBundle\Entity\Reservation",
        "groups"={"resa"}
    }
   * )
  *
	 * R�alise une r�servation
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response|string
	 */
  public function postReservationVoitureAction(Request $request){
  	$response = new Response();
  	//** R�cup�ration des param�tres **//
   $this->token = $request->request->get('token');
   $this->idVoiture = $request->request->get('idVoiture');
   $this->adresseRetrait = $request->request->get('adresseRetrait');
   $this->adresseRetour = $request->request->get('adresseRetour');
   $this->dateRetour =  $this->createDate($request->request->get('dateRetour'));
   $this->dateRetrait = $this->createDate($request->request->get('dateRetrait'));
   $this->idVoiture = $request->request->get('idVoiture');
   
   //** Test si la date de retrait est valide **//
  if(!$this->dateRetrait) {
  	$response->setStatusCode(Response::HTTP_BAD_REQUEST);
  	$response->setContent("La date de retrait est mal renseign�e ou manquante. format : jj:mm:yyyy:hh:mm");
  	return $response;
  }
  
  //** Test si la date de detour est valide **//
  if(!$this->dateRetour) {
  	$response->setStatusCode(Response::HTTP_BAD_REQUEST);
  	$response->setContent("La date de retour est mal renseign�e ou manquante. format : jj:mm:yyyy:hh:m");
  	return $response;
  }
   
   //** Test si le token est valide **//
   // Si token invalide : acc�s refus�
   $response = new Response();
   if(!$this->isValid()) {
   	$response->setStatusCode(Response::HTTP_FORBIDDEN);
   	$response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
   	return $response;
   }
   //** Test si id la voiture existe **//   
   if(!$this->isVoitureDispo()) {
   	$response->setStatusCode(Response::HTTP_FORBIDDEN);
   	$response->setContent("Voiture non disponible pour cette periode");
   	return $response;
   }
   //** Si tout est OK => on valide la r�servation + renvoie id de r�servation **//
   if($this->adresseRetrait == null || $this->adresseRetrait == "" || $this->adresseRetour == null || $this->adresseRetour == "" || $this->idVoiture =="" || $this->idVoiture == null) {
   	$response->setStatusCode(Response::HTTP_BAD_REQUEST);
   	$response->setContent("Il manque des �l�ments pour assurer la r�servation");
   	return $response;
   }
   
   // Cr�ation de la r�servation
   $resa = new Reservation();
   $resa->setIdUser($this->idUser);
   $resa->setIdVoiture($this->idVoiture);
   $resa->setDateDebutRes($this->dateRetrait);
   $resa->setDateFinRes($this->dateRetour);
   $resa->setLieuRetrait($this->adresseRetrait);
   $resa->setLieuRetour($this->adresseRetour);
   $numResa = $this->generateString(5);
   $resa->setNumReservation($numResa);
   
   $em = $this->getDoctrine()->getManager();
   $em->persist($resa);
   $em->flush();
   
   $resaUser = new ReservationUser();
   $resaUser->setIdUser($this->idUser);
   $resaUser->setIdReservation($resa->getId());
   $em->persist($resaUser);
   $em->flush();
   
   // Renvoie du num�ro de r�servation au client
   $obj = new \stdClass();
   $obj->numeroReservation=$numResa;
   return json_encode($obj);
  }
  
  /**
   * @ApiDoc(
   * 	resource="/api/reservation",
   *    description="Permet de consulter la liste de ses reservations",
   *    requirements={
   *      {"name"="token", "requirement"="obligatory", "dataType"="string"}
   *  },
   *  statusCodes={
   *  	200 = "Successfull",
   *  	403 = "Forbidden | Connexion refus�e, token invalide"
   *  },
   *  output={
        "class"   = "ApiBundle\Entity\Reservation",
        "groups"={"resa"}
    }
   * )
   */
  public function getReservationMesreservationsAction(Request $request) {
  	
  	$this->token = $request->query->get('token');
  	
  	//** Test si le token est valide **//
  	// Si token invalide : acc�s refus�
  	$response = new Response();
  	if(!$this->isValid()) {
  		$response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
  		$response->setStatusCode(Response::HTTP_FORBIDDEN);
  		return $response;
  	}

  	$UserReservations = $this->getUserReservations();

  	return $UserReservations;
  }
  
  /**
   * @ApiDoc(
   * 	resource ="/api/reservation",
   *    description="Permet de consulter les informations d'une reservation",
   *    requirements={
   *      {"name"="token", "requirement"="obligatory", "dataType"="string"},
   *      {"name"="numeroReservation", "requirement"="obligatory", "dataType"="string"}
   *  },
   *  statusCodes={
   *  	200 = "Successfull",
   *  	403 = "Forbidden | Connexion refus�e, token invalide"
   *  },
   *  output={
        "class"   = "ApiBundle\Entity\Reservation",
        "groups"={"infos"}
    }
   * )
   * R�cup�re les informations d'une r�servation
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response|unknown
   */
  public function getReservationInfoAction(Request $request) {
  	
  	$this->token = $request->query->get('token');
  	$this->numReservation = $request->query->get('numeroReservation');
  	 
  	//** Test si le token est valide **//
  	// Si token invalide : acc�s refus�
  	$response = new Response();
  	if(!$this->isValid()) {
  		$response->setStatusCode(Response::HTTP_FORBIDDEN);
  		$response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
  		return $response;
  	}
  	
  	return $this->getinfoReservation();

  }
  /**
   * Test si la voiture recherch� existe
   * @return boolean
   */
  private function isVoitureExist() {
  	$em = $this->getDoctrine()->getManager();
  	$query = $em->createQuery('SELECT v.id FROM ApiBundle:Voiture v WHERE v.id = :id')
  	->setParameter('id', $this->idVoiture);
  	
  	$result = $query->getResult();
  	// Si la voiture n'existe pas
  	if(empty($result)) {
  		return false;
  	}
  	return true;
  }
  /**
   * R�cup�re les r�servation d'un utilisateur
   * @return unknown
   */
  private function getUserReservations() {
  	$em = $this->getDoctrine()->getManager();
  	$query = $em->createQuery('SELECT r.numReservation FROM ApiBundle:Reservation r WHERE r.idUser = :idUser')
  	->setParameter('idUser', $this->idUser);
  	
  	$result = $query->getResult();
  	return $result;
  }
  /**
   * R�cup�re les informations d'une r�servation
   * @return unknown
   */
  private function getinfoReservation() {
  	$em = $this->getDoctrine()->getManager();
  	$query = $em->createQuery('SELECT r.dateDebutRes, r.dateFinRes , r.lieuRetrait, r.lieuRetour , r.numReservation , v.description , v.prix,v.boite,v.marque,v.modele,v.categorie,v.nbPorte,v.nbPassage FROM ApiBundle:Reservation r , ApiBundle:Voiture v WHERE r.numReservation = :num AND v.id = r.idVoiture ')
  	->setParameter('num', $this->numReservation);
  	$result = $query->getResult();
  	return $result;
  }
}