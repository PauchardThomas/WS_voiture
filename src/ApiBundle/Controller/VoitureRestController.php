<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Prefix;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ApiBundle\Entity\Voiture;

class VoitureRestController extends BaseController
{
	private $dateRetour;
	private $adresseRetrait;
	private $adresseRetour;
	private $marque;
	private $modele;
	private $clim;
	private $boite;
	private $categorie;
	private $nbPorte;
	private $nbPassage;
	
	private $idAgence;
	private $idAgDepart;
	private $idAgArrive;
	
  
  /**
   *
   * @ApiDoc(
   * 	  resource="/api/voitures",
   *    description="Permet de consulter la liste des voitures",
   *    requirements={
   *      {"name"="token","requirement"="obligatory", "dataType"="string"},
   *      {"name"="dateRetour","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 02:12:2017:10:30"},
   *      {"name"="dateRetrait","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 04:12:2017:19:00"},
   *      {"name"="adresseRetrait","requirement"="obligatory", "dataType"="string"},
   *      {"name"="adresseRetour","requirement"="obligatory", "dataType"="string"},
   *      {"name"="marque", "requirement"="nonobligatory", "dataType"="string"},
   *      {"name"="modele","requirement"="nonobligatory", "dataType"="string"},
   *      {"name"="clim","requirement"="nonobligatory", "dataType"="string","description"="0 : non | 1 : oui"},
   *      {"name"="boite","requirement"="nonobligatory", "dataType"="string","description"="manuelle | automatique"},
   *      {"name"="categorie","requirement"="nonobligatory", "dataType"="string"},
   *      {"name"="nbPorte","requirement"="nonobligatory", "dataType"="string"},
   *      {"name"="nbPassage","requirement"="nonobligatory", "dataType"="string"},
   *  },
   *  statusCodes={
   *  	200 = "Successfull",
   *  	403 = "Forbidden | Connexion refusée, token invalide"
   *  },
   *  output={
   "class"   = "ApiBundle\Entity\Voiture"
   }
   * )
   */
  public function getVoituresAction(Request $request){
  	 
  
  	$this->marque = $request->query->get('marque');
  	$this->modele = $request->query->get('modele');
  	$this->clim = $request->query->get('clim');
  	$this->boite = $request->query->get('boite');
  	$this->categorie = $request->query->get('categorie');
  	$this->nbPorte = $request->query->get('nbPorte');
  	$this->nbPassage = $request->query->get('nbPassage');
  	$this->adresseRetrait = $request->query->get('adresseRetrait');
  	$this->adresseRetour = $request->query->get('adresseRetour');
  	$this->dateRetour =  $this->createDate($request->query->get('dateRetour'));
  	$this->dateRetrait = $this->createDate($request->query->get('dateRetrait'));
  	$this->token = $request->query->get('token');
  	 
  	$this->idAgence = $request->query->get('idAgence');
  	$this->idAgDepart = $request->query->get('idAgDepart');
  	$this->idAgArrive = $request->query->get('idAgArrive');
  	 
  
  	// Si token invalide : accès refusé
  	$response = new Response();
  	$response->setStatusCode(Response::HTTP_FORBIDDEN);
  	if(!$this->isValid()) {
  		$response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
  		return $response;
  	}
  
  	//** Test si la date de retrait est valide **//
  	if(!$this->dateRetrait) {
  		$response->setStatusCode(Response::HTTP_BAD_REQUEST);
  		$response->setContent("La date de retrait est mal renseignée ou manquante. format : jj:mm:yyyy:hh:mm");
  		return $response;
  	}
  		
  	//** Test si la date de detour est valide **//
  	if(!$this->dateRetour) {
  		$response->setStatusCode(Response::HTTP_BAD_REQUEST);
  		$response->setContent("La date de retour est mal renseignée ou manquante. format : jj:mm:yyyy:hh:m");
  		return $response;
  	}
  
  
  	$voituresDispo = array();
  
  	// Récupère les voitures du WS local
  	$voituresLocals = $this->getVoituresLocalApi();
  
  	foreach($voituresLocals as $key => $localVoiture) {
  		array_push($voituresDispo, $localVoiture);
  	}
  
  	// Récupère les voitures de WS externe n° 1
  	$external_voitures = $this->getWSVoitures();
  	  
  	if($external_voitures != null) {
  	foreach ($external_voitures as $key => $voitureExterne) {
  	array_push($voituresDispo, $voitureExterne);
  	}
  	}
  	 
  	// Récupère les voitures de WS externe n°2
  	$external_voitures2 = $this->getWSVoitures2();

  	if($external_voitures2 != null) {
  	 foreach ($external_voitures2 as $key => $voitureExterne) {
  	 	//var_dump($voitureExterne);
  	 	$temp = array();
  	 	$temp["voiture"] = (array)$voitureExterne->voiture;
  	 	$temp["marque"] = (array)$voitureExterne->marque;
  	 	$temp["modele"] = (array)$voitureExterne->modele;
  	 	$temp["agence"] = (array)$voitureExterne->agence;
  	 	$temp["formule"] = (array)$voitureExterne->formule;
  	 	$temp["prix"] = (array)$voitureExterne->prix;
  	 	
  	 array_push($voituresDispo,$temp);
  	 }
  	}
  	 
  
  	return $voituresDispo;
    
  }
  
  /**
   * Récupère les voitures disponibles du WS de Theo et Julien
   */
  private function getWSVoitures() {
  	
  	$wsdl="http://192.168.222.16:53357/CarReservationService.svc?singleWsdl";
  	
  	$service = new \SoapClient($wsdl);
  	$taballservices = $service->__soapCall("getListCarAvailable", array(
  			array(
  				'departureDateUser' => $this->dateRetour,
  				'arrivalDateUser' => $this->dateRetrait,
  				'departureCity'=>$this->adresseRetrait,
  				'arrivalCity'=>$this->adresseRetour
  			)
  			
  	));
  	
  	$result = $taballservices->getListCarAvailableResult;
  	
  	if((array)$result == null) {
  		return null;
  	}
  	
  	$voitures = $result->CarDTO;
  	$v = array();
  	foreach ($voitures as $key => $voiture) {
  		array_push($v,(array)$voiture);
  	}

  	return $v;
  }
  
  /**
   * Récupère les voitures disponibles du WS de Antoine et Valentin
   */
  private function getWSVoitures2() {
  	 
  	$url = 'http://192.168.222.23:8080/api/reservation/find/'.$this->dateRetrait->format('Ymd').'/'.$this->dateRetour->format('Ymd').'/58ca934ec57e491860bd7539';
  	$api_key ='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyIkX18iOnsic3RyaWN0TW9kZSI6dHJ1ZSwic2VsZWN0ZWQiOnt9LCJnZXR0ZXJzIjp7fSwid2FzUG9wdWxhdGVkIjpmYWxzZSwiYWN0aXZlUGF0aHMiOnsicGF0aHMiOnsiX192IjoiaW5pdCIsImFkbWluIjoiaW5pdCIsInBhc3N3b3JkIjoiaW5pdCIsIm5hbWUiOiJpbml0IiwiX2lkIjoiaW5pdCJ9LCJzdGF0ZXMiOnsiaWdub3JlIjp7fSwiZGVmYXVsdCI6e30sImluaXQiOnsiX192Ijp0cnVlLCJhZG1pbiI6dHJ1ZSwicGFzc3dvcmQiOnRydWUsIm5hbWUiOnRydWUsIl9pZCI6dHJ1ZX0sIm1vZGlmeSI6e30sInJlcXVpcmUiOnt9fSwic3RhdGVOYW1lcyI6WyJyZXF1aXJlIiwibW9kaWZ5IiwiaW5pdCIsImRlZmF1bHQiLCJpZ25vcmUiXX0sImVtaXR0ZXIiOnsiZG9tYWluIjpudWxsLCJfZXZlbnRzIjp7fSwiX21heExpc3RlbmVycyI6MH19LCJpc05ldyI6ZmFsc2UsIl9kb2MiOnsiX192IjowLCJhZG1pbiI6dHJ1ZSwicGFzc3dvcmQiOiIxMTEiLCJuYW1lIjoidXNlcjEiLCJfaWQiOiI1OGNmZDQ3N2E5ZWMxZDgzYzJjNzQ3YjYifSwiaWF0IjoxNDkwMDE2MDQ4LCJleHAiOjE0OTAwMjMyNDh9.P5rbn9YgEOxFydKNlInrgTHmKvH5PVdSs_tiMps_D8Q';
  	$url_final = $url;
  	 
  	$ch = curl_init();
  	 
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch,CURLOPT_HTTPHEADER,array('x-access-token:'.$api_key));
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  	$response = curl_exec ($ch);
  	curl_close ($ch);
  	return json_decode($response);

  }
  
  /**
   * Récupère les villes du WS de Antoine et Valentin
   * @return mixed|NULL
   */
  private function getWS2IdVille() {
  	$url = 'http://192.168.222.23:8080/api/ville/all';
  	$api_key ='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyIkX18iOnsic3RyaWN0TW9kZSI6dHJ1ZSwic2VsZWN0ZWQiOnt9LCJnZXR0ZXJzIjp7fSwid2FzUG9wdWxhdGVkIjpmYWxzZSwiYWN0aXZlUGF0aHMiOnsicGF0aHMiOnsiX192IjoiaW5pdCIsImFkbWluIjoiaW5pdCIsInBhc3N3b3JkIjoiaW5pdCIsIm5hbWUiOiJpbml0IiwiX2lkIjoiaW5pdCJ9LCJzdGF0ZXMiOnsiaWdub3JlIjp7fSwiZGVmYXVsdCI6e30sImluaXQiOnsiX192Ijp0cnVlLCJhZG1pbiI6dHJ1ZSwicGFzc3dvcmQiOnRydWUsIm5hbWUiOnRydWUsIl9pZCI6dHJ1ZX0sIm1vZGlmeSI6e30sInJlcXVpcmUiOnt9fSwic3RhdGVOYW1lcyI6WyJyZXF1aXJlIiwibW9kaWZ5IiwiaW5pdCIsImRlZmF1bHQiLCJpZ25vcmUiXX0sImVtaXR0ZXIiOnsiZG9tYWluIjpudWxsLCJfZXZlbnRzIjp7fSwiX21heExpc3RlbmVycyI6MH19LCJpc05ldyI6ZmFsc2UsIl9kb2MiOnsiX192IjowLCJhZG1pbiI6dHJ1ZSwicGFzc3dvcmQiOiIxMTEiLCJuYW1lIjoidXNlcjEiLCJfaWQiOiI1OGNmZDQ3N2E5ZWMxZDgzYzJjNzQ3YjYifSwiaWF0IjoxNDkwMDE2MDQ4LCJleHAiOjE0OTAwMjMyNDh9.P5rbn9YgEOxFydKNlInrgTHmKvH5PVdSs_tiMps_D8Q';
  	$url_final = $url;
  	
  	$ch = curl_init();
  	
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch,CURLOPT_HTTPHEADER,array('x-access-token:'.$api_key));
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  	$response = curl_exec ($ch);
  	curl_close ($ch);
  	$villes = json_decode($response);
  	
  	foreach ($villes as $key => $ville) {
  		if($ville["vil_libelle"] == $this->adresseRetrait) {
  			return $ville["_id"];
  		}
  	}
  	return null;
  }
  
/**
 * Récupère les voitures du WS local
 */
  private function getVoituresLocalApi() {
  	
  	$repository = $this->getDoctrine()->getRepository('ApiBundle:Voiture');
  	$query = $repository->createQueryBuilder('v');
  	 
  	$params = array();
  	 
  	if($this->marque != null && $this->marque != "") {
  		$query->andWhere("v.marque = :marque");
  		$params["marque"] = $this->marque;
  	}
  	if($this->modele != null && $this->modele != "") {
  		$query->andWhere("v.modele = :modele");
  		$params["modele"] = $this->modele;
  	}
  	if($this->clim != null && $this->clim != "") {
  		$query->andWhere("v.clim = :clim");
  		$params["clim"] = $this->clim;
  	}
  	if($this->boite != null && $this->boite != "") {
  		$query->andWhere("v.boite = :boite");
  		$params["boite"] = $this->boite;
  	}
  	if($this->categorie != null && $this->categorie != "") {
  		$query->andWhere("v.categorie = :categorie");
  		$params["categorie"] = $this->categorie;
  	}
  	if($this->nbPorte != null && $this->nbPorte != "") {
  		$query->andWhere("v.nbPorte = :nbPorte");
  		$params["nbPorte"] = $this->nbPorte;
  	}
  	if($this->nbPassage != null && $this->nbPassage != "") {
  		$query->andWhere("v.nbPassage = :nbPassage");
  		$params["nbPassage"] = $this->nbPassage;
  	}
  	// Récupère les voitures du WS
  	$voitures = $query->setParameters($params)->getQuery()->getResult();
  	 
  	$voituresDispo = array();
  	 
  	// Récupère les voitures disponibles
  	foreach ($voitures as $key => $voiture) {
  	
  		$this->idVoiture = $voiture->getId();
  		//** Test si id la voiture existe **//
  		if($this->isVoitureDispo()) {
  			array_push($voituresDispo, $voiture);
  		}
  	}
  	return $voituresDispo;
  }
}