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

class HotelRestController extends BaseController
{
	private $dateDebut;
	private $dateFin;
	private $nbCouchages;
	private $nbPersonnes;
	private $ville;
	private $idVille;
	private $idChambre;
	private $idClient;
	
	
	
	/**
	 *
	 * @ApiDoc(
	 * 	  resource="/api/hotels",
	 *    description="Permet de consulter les chambres d'hotel disponibles",
	 *    requirements={
	 *      {"name"="token","requirement"="obligatory", "dataType"="string"},
	 *      {"name"="dateRetour","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 02:12:2017:10:30"},
	 *      {"name"="dateRetrait","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 04:12:2017:19:00"},
	 *      {"name"="adresseRetrait","requirement"="obligatory", "dataType"="string"},
	 *      {"name"="nbCouchages","requirement"="obligatory", "dataType"="string"},
	 *      {"name"="nbPersonnes","requirement"="obligatory", "dataType"="string"},
	 *      {"name"="idVille","requirement"="obligatory", "dataType"="int"},
	 *  },
	 *  statusCodes={
	 *  	200 = "Successfull",
	 *  	403 = "Forbidden | Connexion refusée, token invalide"
	 *  }
	 * )
	 */
	public function getHotelAction(Request $request) {
		

		
		$this->dateDebut = $this->createDate($request->query->get('dateDebut'));
		$this->dateFin = $this->createDate($request->query->get('dateFin'));
		$this->nbCouchages = $request->query->get('nbCouchages');
		$this->nbPersonnes = $request->query->get('nbPersonnes');
		$this->ville = $request->query->get('idVille');
		$this->token = $request->query->get('token');
		
		// Si token invalide : accès refusé
		$response = new Response();
		$response->setStatusCode(Response::HTTP_FORBIDDEN);
		if(!$this->isValid()) {
			$response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
			return $response;
		}
		
		$context = stream_context_create([
				'ssl' => [
						// set some SSL/TLS specific options
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
				]
		]);
		 
		$wsdl="http://192.168.222.11:25245/Service1.svc?singleWsdl";
		$service = new \SoapClient($wsdl, [ 'soap_version' => 'SOAP_1_2',
				//'location'=>'http://192.168.222.11:25245/Service1.svc?singleWsdl',
				'stream_context' => $context,
				'login' =>"toto",
				'password' => "tata"
		]);

		 
		//À partir de là, on peut déjà faire appel aux méthodes du service décrites dans le WSDL
		$taballservices = $service->__soapCall("getRoomAvailable", array(array(
				'datedeb' => $this->dateDebut,
				'datefin' => $this->dateFin,
				'nbcouchage' => $this->nbCouchages,
				'nbPersonnes' => $this->nbPersonnes,
				'ville' => $this->idVille
		)));
		
		return $taballservices->getRoomAvailableResult;
	}
	
	/**
	 *
	 * @ApiDoc(
	 * 	  resource="/api/reservation",
	 *    description="Permet de reserver une chambre d'hotel",
	 *    requirements={
	 *      {"name"="token","requirement"="obligatory", "dataType"="string"},
	 *      {"name"="dateRetour","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 02:12:2017:10:30"},
	 *      {"name"="dateRetrait","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 04:12:2017:19:00"},
	 *      {"name"="idChambre","requirement"="obligatory", "dataType"="int"},
	 *      {"name"="idClient","requirement"="obligatory", "dataType"="int"},
	 *      
	 *  },
	 *  statusCodes={
	 *  	200 = "Successfull",
	 *  	403 = "Forbidden | Connexion refusée, token invalide"
	 *  }
	 * )
	 */
	public function postReservationHotelAction(Request $request) {
		
		// Récupération des paramètres
		$this->dateDebut = $this->createDate($request->request->get('dateDebut'));
		$this->dateFin = $this->createDate($request->request->get('dateFin'));
		$this->idChambre = $request->request->get('idChambre');
		$this->idClient = $request->request->get('idClient');
		$this->token = $request->query->get('token');
		
		// Si token invalide : accès refusé
		$response = new Response();
		$response->setStatusCode(Response::HTTP_FORBIDDEN);
		if(!$this->isValid()) {
			$response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
			return $response;
		}
		
		// Gestion des conflits ssl
		$context = stream_context_create([
				'ssl' => [
						// set some SSL/TLS specific options
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
				]
		]);
			
		$wsdl="http://192.168.222.11:25245/Service1.svc?singleWsdl";
		$service = new \SoapClient($wsdl, [ 'soap_version' => 'SOAP_1_2',
				//'location'=>'http://192.168.222.11:25245/Service1.svc?singleWsdl',
				'stream_context' => $context,
				'login' =>"toto",
				'password' => "tata"
		]);
		
			
		// Envoie des paramètres
		$taballservices = $service->__soapCall("bookRoom", array(array(
				'datedeb' => $this->dateDebut,
				'datefin' => $this->dateFin,
				'idChambre' => $this->idChambre,
				'idClient' => $this->idClient,
		)));
		
		return $taballservices->bookRoomResult;
	}
	
}