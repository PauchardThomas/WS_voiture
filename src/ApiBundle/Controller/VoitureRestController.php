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
	
	/**
	 * 
	 * @ApiDoc(
	 * 	  resource="/api/voiture",
	 *    description="Permet de consulter la liste des voitures",
	 *    requirements={
	 *      {"name"="token","requirement"="obligatory", "dataType"="string"},
	 *      {"name"="dateRetrait","requirement"="obligatory", "dataType"="datetime"},
	 *      {"name"="dateretour","requirement"="obligatory", "dataType"="datetime"},
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
  public function getVoitureAction(Request $request){
   

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

  	$voitures = $query->setParameters($params)->getQuery()->getResult();
  	
  	$voituresDispo = array();
  	
  	foreach ($voitures as $key => $voiture) {
  		
  		$this->idVoiture = $voiture->getId();
  		//** Test si id la voiture existe **//
  		if($this->isVoitureDispo()) {
  			array_push($voituresDispo, $voiture);
  		}
  	}

  	
  	return $voituresDispo;
  	
  }
  
  public function getSoapAction() {
  	
  	//On doit passer le fichier WSDL du Service en paramètre de l'objet SoapClient()
  	/*$wsdl="http://www.webservicex.com/globalweather.asmx?WSDL";
  	$service=new \SoapClient($wsdl);
  	
  	$params = array(
  "CountryName" => "France",
);
  	//À partir de là, on peut déjà faire appel aux méthodes du service décrites dans le WSDL
  	$taballservices=$service->__soapCall("GetCitiesByCountry", array($params));
  	
  	//On renvoie le résutat de notre méthode, pour voir....
  	print_r($taballservices);*/
  	
  	//On doit passer le fichier WSDL du Service en paramètre de l'objet SoapClient()
  	
  	
  	$context = stream_context_create([
  			'ssl' => [
  					// set some SSL/TLS specific options
  					'verify_peer' => false,
  					'verify_peer_name' => false,
  					'allow_self_signed' => true
  			]
  	]);
  	
  	$wsdl="https://192.168.222.11:44337/Service1.svc?wsdl";
  	$service = new \SoapClient($wsdl, [ 'soap_version' => 'SOAP_1_2',
         'location'=>'https://192.168.222.11:44337/Service1.svc',
  		'stream_context' => $context
  	]);
  	 
  	$params = array(
  			"nom" => "ttoo",
  			"prenom" => "titi",
  			"email" => "itti@tt.fr",
  			"tel" => "0243658741",
  	);
  	
  	
  	
  	$ville = [
  		'_id' => '20280',
  		'_nom' => 'laval',
  		'_codepostal' => '53000'
  	];
  	
  	//À partir de là, on peut déjà faire appel aux méthodes du service décrites dans le WSDL
  	$taballservices = $service->__soapCall("getListHotel", array(array('ville' => $ville)));
  	//$taballservices = $service->getVille(array('idville' => 20280));
  	 
  	//On renvoie le résutat de notre méthode, pour voir....
  	print_r($taballservices);
  	die;
  	
  	return "toto";
  	
  }
  
  public function getTestAction() {
  	
  	//*************   CURL GET  *********************//
  	$url = 'https://iia.damien-pereira.fr/flights/search?origin=lgw&destination=rak&travelers=2&sort=price_asc';
  	$api_key ='';
  	$url_final = $url.$api_key;
  	
  	$ch = curl_init();
  	
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Authorization: JWT eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZGVudGl0eSI6MSwiaWF0IjoxNDg5NzQ3NTYwLCJuYmYiOjE0ODk3NDc1NjAsImV4cCI6MTQ4OTc1MTE2MH0.lMj9jKZW5xwIFyIUt17LKdWnBIMAFMN6VxhySsFxDOA'));
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  	$response = curl_exec ($ch);
  	curl_close ($ch);
  	
  	return $response;
  	
  	//*************   CURL POST  *********************//
  	//extract data from the post
  	//set POST variables
  	$url = '';
  	
  	// set post fields
  	$post = [
  			'username' => 'user12',
  			'password' => 'passuser1',
  	];
  	
  	$ch = curl_init($url);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  	// execute!
  	$response = curl_exec($ch);
  	
  	// close the connection, release resources used
  	curl_close($ch);
  	
  	// do anything you want with your response
  	 
  	return $response;
  
  	
  }
}