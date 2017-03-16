<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Entity\Reservation;
use ApiBundle\Entity\ReservationUser;

class BaseController extends Controller
{
	protected $token;
	protected $idUser;
	/**
	 * Test si le token est valide
	 * @return boolean
	 */
	protected function isValid(){
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery('SELECT u.id, u.dateToken FROM ApiBundle:User u WHERE u.token = :token')
		->setParameter('token', $this->token);
	
		$result = $query->getResult();
		// Si le token n'existe pas
		if(empty($result)) {
			echo"Connexion refusee, veuillez vous authentifier avec un token valide";
			return false;
		}
	
		$this->idUser = $result["0"]["id"];
	
		// Comparaison de la date
		$dateToken = new \DateTime($result[0]["dateToken"]->format('Y-m-d H:i:s'));
		$now = new \DateTime(date('Y-m-d H:i:s'));
		$interval = $dateToken->diff($now)->format('%d');
		if($interval > 7) {
			return false;
		}
		return true;
	}
	protected function createDate($date) {
	
		$dateformat = \DateTime::createFromFormat('j:m:Y:H:i',$date);
		if($dateformat == false) {
			return false;
		}
		return $dateformat;
	}
	
	/**
	 * G�n�ration string random
	 * @param unknown $length
	 * @return string
	 */
	protected function generateString($length) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = '';
	
		for ($i = 0; $i < $length; $i++) {
			$string .= $characters[mt_rand(0, strlen($characters) - 1)];
		}
		return $string;
	}
}