<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Prefix;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class VoitureRestController extends BaseController
{
	private $dateRetrait;
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
  	$this->token = $request->query->get('token');
  	
  	// Si token invalide : accès refusé
  	 $response = new Response();
  	 $response->setStatusCode(Response::HTTP_FORBIDDEN);
  	 if(!$this->isValid()) {
  	 $response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
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

  	
  	return $voitures;
  	
  }
  
  public function getTestAction() {
  	
  	//*************   CURL GET  *********************//
  	$url = '';
  	$api_key ='';
  	$url_final = $url.$api_key;
  	
  	$ch = curl_init();
  	
  	curl_setopt($ch, CURLOPT_URL, $url);
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