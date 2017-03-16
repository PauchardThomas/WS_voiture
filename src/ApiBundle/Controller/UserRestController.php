<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ApiBundle\Entity\User;

class UserRestController extends BaseController
{

	private $username;
	private $password;
	private $salt;
	
	private $ADDITION = "addition";
	private $SOUSTRACTION = "soustraction";
	private $MULTIPLICATION = "multiplication";
	private $DIVISION = "division";
	
	/**
	 * Enregistrement d'un utilisateur
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response|string
	 * @ApiDoc(
	 *    description="Permet de s'inscrire",
	 *    requirements={
	 *      {"name"="username", "requirement"="obligatory", "dataType"="string"},
	 *      {"name"="password","requirement"="obligatory", "dataType"="string"}
	 * 	}
	 * )
	 */
	public function postRegisterAction(Request $request)
	{
		$this->username = $request->request->get('username');
		$this->password = $request->request->get('password');
	
		$response = new Response();
	
		// Test les paramètres reçus
		if(!$this->isUsernameAvailable()){
			$response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
			$response->setContent("Username already exist");
			return $response;
		}else if ($this->password == "" || $this->password == null) {
			$response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
			$response->setContent("Password null");
			return $response;
		}
	
		// Création de l'utilisateur
		$token = $this->createUser();
	
		$obj = new \stdClass();
		$obj->token=$token;
	
		return json_encode($obj);
	
	}
	/**
	 * Connexion d'un utilisateur
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response|string
	 * @ApiDoc(
	 *    description="Permet de ce connecter",
	 *    requirements={
	 *      {"name"="username", "requirement"="obligatory", "dataType"="string"},
	 *      {"name"="password","requirement"="obligatory", "dataType"="string"}
	 * 	}
	 * )
	 */
	public function postLoginAction(Request $request)
	{
		$this->username = $request->request->get('username');
		$this->password = $request->request->get('password');
	
		$user = $this->getUserByIdentifiants();
		$response = new Response();
		if(empty($user)) {
			$response->setStatusCode(Response::HTTP_FORBIDDEN);
			return $response;
		}
		$token = $user[0]["token"];
	
		$obj = new \stdClass();
		$obj->token=$token;
	
		return json_encode($obj);
	}
	
	/**
	 * Test si le usernampe est disponible
	 * @return boolean
	 */
	private function isUsernameAvailable() {
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery('SELECT u.id FROM ApiBundle:User u WHERE u.username = :username')
		->setParameter('username', $this->username);
		$result = $query->getResult();
		// Si le username n'existe pas
		if(empty($result)) {
			return true;
		}
		return false;
	}
	/**
	 * Création d'un utilisateur
	 * @return string
	 */
	private function createUser() {
	
		$user = new User();
		$user->setUsername($this->username);
		$this->salt = $this->generateString(5);
		$user->setSalt($this->salt);
	
		$user->setPassword($this->hashPassword());
		$user->setToken($this->generateString(40));
		$user->setDateToken(new \DateTime(date('Y-m-d H:i:s')));
	
		$em = $this->getDoctrine()->getManager();
	
		$em->persist($user);
		$em->flush();
	
		return $user->getToken();
	}
	/**
	 * Retourne un utilisateur en fonction de son username et password
	 * @return User Utilisateur
	 */
	private function getUserByIdentifiants() {
	
		$em = $this->getDoctrine()->getManager();
		$this->salt = $this->getUserSalt();
		$params = array(
				'username' => $this->username,
				'password' => $this->hashPassword()
		);
		$query = $em->createQuery('SELECT u.id , u.username,u.password,u.token,u.dateToken,u.salt FROM ApiBundle:User u WHERE u.username = :username AND u.password = :password')
		->setParameters($params);
		$result = $query->getResult();
		return $result;
	}
	
	/**
	 * Récupère le salt de l'utilisateur
	 * @return unknown
	 */
	private function getUserSalt() {
		$em = $this->getDoctrine()->getManager();
	
		$query = $em->createQuery('SELECT u.salt FROM ApiBundle:User u WHERE u.username = :username')
		->setParameter("username",$this->username);
		$result = $query->getResult();
		return $result[0]["salt"];
	}
	
	/**
	 * Hash string
	 * @return string
	 */
	private function hashPassword() {
		return hash('sha256', $this->salt . $this->password . $this->salt);
	}
}