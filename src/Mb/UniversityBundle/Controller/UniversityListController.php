<?php

namespace Mb\UniversityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mb\UniversityBundle\Entity\University;
use Mb\UniversityBundle\Entity\Country;
use Mb\UniversityBundle\Entity\CountryRepository;
use Mb\UniversityBundle\Entity\UniversityRepository;
use Mb\FileBundle\DependencyInjection\FileExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Mb\AffectBundle\Entity\Wish;
use Symfony\Component\Config\Definition\Exception\Exception;



class UniversityListController extends Controller{
	
	/* Affiche les universités pour faire les voeux */
	/**
     * @Route("/student/university", name="Mb_univ_list")
	 * @Route("/admin/university/{studentId}", name="Mb_univ_list_admin",requirements={
	*     "studentId": "\d+"
	* })
     * @Template()
     */
	public function listUnivTriAction($studentId = null, Request $request)
	{	
		$em = $this
		->getDoctrine()
		->getManager();
		
		$currentUser= $this->get('security.token_storage')->getToken()->getUser();
		$department = $currentUser->getDepartment();
		
		$repository = $em->getRepository('MbUniversityBundle:University');

		$listUnivs=$repository->getAllUniv($department);

		if($studentId == null && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
			$student = $this->get('security.token_storage')->getToken()->getUser();
		}else if($studentId != null && $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
			$repository = $em->getRepository('MbUserBundle:User');
			$student = $repository->find($studentId);
			
			//Si pas du même département, on redirige vers une erreur 404
			if($student->getDepartment() != $this->get('security.token_storage')->getToken()->getUser()->getDepartment()){	
				throw $this->createNotFoundException('Vous n\'êtes pas autorisé à accéder à cette page.');
			}
			
			
		}else{
			$student = null;
		}

		

		return $this->render('MbUniversityBundle:University:universitylist.html.twig', array('listUnivs' => $listUnivs, 'student' => $student));
	}
	
	/* "Editeur" de place */
	/**
	 * @Route("/admin/places/{anchor}", name="mb_places_list_admin",requirements={
	 *     "anchor": "\d+"
     * })
     * @Template()
     */
	public function listPlacesAction($anchor = null)
	{
		$em = $this
		->getDoctrine()
		->getManager();

		$repository = $em->getRepository('MbUniversityBundle:Place');
		
		$currentUser= $this->get('security.token_storage')->getToken()->getUser();
		$department = $currentUser->getDepartment();

		
		$listPlaces=$repository->getAllOrder($department);

		$repository = $em->getRepository('MbUniversityBundle:MobilityPeriod');		
		$listMobilityPeriods = $repository->findBy(array(), array('type' => 'ASC'));
		
		$repository = $em->getRepository('MbUniversityBundle:University');
		$universities = $repository->findAll();
		$repository = $em->getRepository('MbUniversityBundle:Country');
		$countries = $repository->findAll();
		
		$listProblem = $this->checkUniversityPlacePeriodAction();
		
		
		return $this->render('MbUniversityBundle:University:universityListAdmin.html.twig', array('listPlaces' => $listPlaces, 'listMobilityPeriods' => $listMobilityPeriods, 'universities' => $universities, 'countries' => $countries, 'listProblem'=>$listProblem, 'anchor'=>$anchor));
	}
	
	/* Vérifie s'il n'y a pas de doublons de "places dispos" pour une universitée*/
	public function checkUniversityPlacePeriodAction()
	{
		$em =$this
		->getDoctrine()
		->getManager();

		$user= $this->get('security.token_storage')->getToken()->getUser();	
		$department = $user->getDepartment();

		//On récupère les places concernés			
		$repository = $em
		->getRepository('MbUniversityBundle:Place');

		$listPlaces = $repository->findBy(
			array('department' => $department)
			);

		$listProblem = array();	
		$placesExist = array();
		
		foreach($listPlaces as $place){
			$key = $place->getUniversity()->getName() . $place->getUniversity()->getCountry()->getName();
			foreach($place->getMobilityPeriod() as $mb){
				if(array_key_exists($key.$mb->getType(),$placesExist)){
					array_push($listProblem,$place->getUniversity());
				}else{
					$placesExist[$key.$mb->getType()] = 'exist';
				}
			}
		}

		return $listProblem;
	}

	
}
