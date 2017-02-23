<?php

namespace Mb\UniversityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mb\AffectBundle\Controller\AffectController;

class UniversityController extends Controller
{
	/*Affiche la page d'une universitée*/
	/**
	 * @Route("/student/university-view/{university}", name="mb_university_student",requirements={
	 *     "university": "\d+"
     * })
	 * @Route("/admin/university-view/{university}", name="mb_university_admin",requirements={
	 *     "university": "\d+"
     * })
	 * @Template()
	 */
	public function indexUnivAction($university = 1)
	{
		$usr= $this->get('security.token_storage')->getToken()->getUser();
		$em =$this->getDoctrine()->getManager();
		$repository = $em->getRepository('MbUniversityBundle:University');
		$univ = $repository->find($university);   
		$repository = $em->getRepository('MbMainBundle:Year');
		$year = $repository->getCurrentYear();
		$repository = $em->getRepository('MbUserBundle:User');
		$usersWish = $repository->findUsersWishes($university,$usr->getDepartment());   
		$usersHadGo = $repository->findUsersHadGo($university);   	
		
		return $this->render('MbUniversityBundle:University:index.html.twig', array('university' => $univ, 'usersWish'=>$usersWish,'year'=> $year,  'usersHadGo'=>$usersHadGo));
		
	}
	
	
	/*Supprime une université et en cascade ses liaisons*/
	/**
     * @Route("/supAdmin/university/action/deleteUniv", name="mb_univ_delete_univ")
     * @Template()
	*/
	public function deleteUnivAction(Request $request){
		if ($request->getMethod() == 'POST')
		{
			$id = $request->get('id');
			$em =$this->getDoctrine()
			->getManager();			
			$repository = $em->getRepository('MbUniversityBundle:University');			
			$university = $repository->find($id);
			$repository = $em->getRepository('MbAffectBundle:Placement');
			$places = $university->getPlaces();
			foreach($places as $place){
				$placements = $repository->findBy(
					array('place' => $place)
					);
				
				foreach($placements as $placement){
					$placement->getUser()->removePlacement();
				}
				
				$em->remove($place);
			}
			
			$country = $university->getCountry();
			$em->remove($university);

			$repository = $em->getRepository('MbUniversityBundle:University');
			if(count($repository->findBy(array('country' => $country)))==1){
				$em->remove($country);
			}
			
			
			$em->flush();	
			$request->getSession()->getFlashBag()->add('success', 'Université supprimée');
		}
		return	$this->redirect($request->headers->get('referer'));
	}
	
}
