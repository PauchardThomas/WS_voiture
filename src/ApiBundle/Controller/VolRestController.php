<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class VolRestController extends BaseController
{
    private $token;

    /**
     *
     * @ApiDoc(
     * 	  resource="/api/vols",
     *    description="Permet de consulter les vols disponibles",
     *    requirements={
     *      {"name"="token","requirement"="obligatory", "dataType"="string"},
     *      {"name"="dateRetour","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 02:12:2017:10:30"},
     *      {"name"="dateRetrait","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 04:12:2017:19:00"},
     *      {"name"="adresseRetrait","requirement"="obligatory", "dataType"="string"},
     *      {"name"="voyageurs","requirement"="nonobligatory", "dataType"="string"},
     *      {"name"="sort","requirement"="nonobligatory", "dataType"="string"},
     *  },
     *  statusCodes={
     *  	200 = "Successfull",
     *  	403 = "Forbidden | Connexion refusée, token invalide"
     *  }
     * )
     */
    public function postVolsAction(Request $request)
    {
    	
    	$this->token = $request->request->get('token');
    	
    	// Si token invalide : accès refusé
    	$response = new Response();
    	$response->setStatusCode(Response::HTTP_FORBIDDEN);
    	if(!$this->isValid()) {
    		$response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
    		return $response;
    	}
    	
        $this->getAuthAvion();

        $res = $this->token;
        $json = json_decode($res, true);
        
        $completedToken = 'JWT '.$json['access_token'];

        // Get cURL resource
        $ul = 'https://iia.damien-pereira.fr/flights/search';

        $params = "origin=".urlencode($request->request->get('origin'));
        $params .= "&destination=".urlencode($request->request->get('destination'));
        $params .= "&travelers=".urlencode($request->request->get('travelers'));
        $params .= "&sort=".urlencode($request->request->get('sort'));


        $ch = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $ul.'?'.$params
            )
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: '.$completedToken
            )                                                                     
        );

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                  
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);

        
        $response = curl_exec($ch);

        //Get flights
        return $response;
    }

    /**
     *
     * @ApiDoc(
     * 	  resource="/api/reservation",
     *    description="Permet de reserver un vol",
     *    requirements={
     *      {"name"="token","requirement"="obligatory", "dataType"="string"},
     *      {"name"="dateRetour","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 02:12:2017:10:30"},
     *      {"name"="dateRetrait","requirement"="obligatory", "dataType"="string","description"="jj:mm:aaaa:hh:mm Exemple : 04:12:2017:19:00"},
     *      {"name"="adresseRetrait","requirement"="obligatory", "dataType"="string"},
     *      {"name"="voyageurs","requirement"="nonobligatory", "dataType"="string"},
     *      {"name"="sort","requirement"="nonobligatory", "dataType"="string"},
     *  },
     *  statusCodes={
     *  	200 = "Successfull",
     *  	403 = "Forbidden | Connexion refusée, token invalide"
     *  }
     * )
     */
    public function putReservationsVolsAction(Request $request)
    {
    	
    	$this->token = $request->request->get('token');
    	// Si token invalide : accès refusé
    	$response = new Response();
    	$response->setStatusCode(Response::HTTP_FORBIDDEN);
    	if(!$this->isValid()) {
    		$response->setContent("Connexion refusee, veuillez vous authentifier avec un token valide");
    		return $response;
    	}
    	
        $personne = array();
        $data = array();
        $params;
        foreach ($request->request->get('travelers')  as $values ) {
            $params = $values['name'].';'.$values['firstname'];
            $personne[] = $params;
        }

        $res;
        $temp = "";
        for ($i=0; $i < count($personne) ; $i++) { 
            $res = explode(";", $personne[$i]);
            $result = '{"name":"'.$res[0].'","firstname":"'.$res[1].'"}';

            if($temp == ""){
                $temp = $temp.$result;
            }else{
                $temp = $temp.','.$result;
            }
           
        }
        
        //Formalise la chaine JSON

        $data = '{"flight_id":"'.$request->request->get('flight_id').'",';
        $data .= '"travelers":['.$temp.']}';

        $this->getAuthAvion();

        $res = $this->token;
        $json = json_decode($res, true);
        
        $completedToken = 'JWT '.$json['access_token'];

         // Get cURL resource
        $ul = 'https://iia.damien-pereira.fr/flights/book';

        $ch = curl_init($ul);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                     
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Authorization:'.$completedToken)                                                                       
        );

         $result = curl_exec($ch);
        
        //RÃ©ponce du web-service
        return $result;
    }
    /**
     * Permet de s'authentifier auprès du webservice
     */
    private function getAuthAvion()
    {
    
    	$url = 'https://iia.damien-pereira.fr/auth';
    	$data = array("username" => "id1", "password" => "mdp");
    	$data_string = json_encode($data);
    	 
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    			'Content-Type: application/json',
    			'Content-Length: ' . strlen($data_string))
    			);
    
    	$result = curl_exec($ch);
    
    	$this->token = $result;
    }
}