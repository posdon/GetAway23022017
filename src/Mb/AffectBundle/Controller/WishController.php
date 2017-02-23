<?php

namespace Mb\AffectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mb\AffectBundle\Entity\Placement;
use Mb\AffectBundle\Entity\Wish;
use Mb\UserBundle\Filter\UserFilter;


class WishController extends Controller
{
	

	/*Ajouter un voeux*/
	/**
     * @Route("/student/university/action/addWish", name="Mb_univ_add_wish")
     * @Template()
	*/
	public function addWishAction(Request $request){
		if ($request->getMethod() == 'POST')
		{
			/*format : Period ID, PlaceID, Student ID*/
			$id= explode(" ",$request->get('id'));
			$studentId=$id[2];
			$placeId=$id[1];
			$periodId=$id[0];

			$em =$this
			->getDoctrine()
			->getManager();

			$repository = $em->getRepository('MbUserBundle:User');
			$user = $repository->find($studentId);				
			$currentUser= $this->get('security.token_storage')->getToken()->getUser();
			
			//Si l'utilisateur est déjà affecté
			$placement = $user->getPlacement();
			if($placement != null){
				if($placement->getTemp() == 0){
					$request->getSession()->getFlashBag()->add('error', "Déjà affecté, demandez à votre RI d'intervenir");
					return	$this->redirect($request->headers->get('referer'));							
				}
			}
			
			$repository = $em->getRepository('MbUniversityBundle:Place');
			$placeToAdd = $repository->find($placeId);

			$repository = $em->getRepository('MbMainBundle:Year');
			$curYear=$repository->getCurrentYear();
			
			$repository = $em->getRepository('MbUniversityBundle:MobilityPeriod');
			$mbPeriod=$repository->find($periodId);
			$maxPrio = 0;


			if ( (($user->getId() == $currentUser->getId() && !($user->getDepartment()->getBolt())) || ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $currentUser->getDepartment() == $user->getDepartment()))
				&& $placeToAdd->getDepartment() == $user ->getDepartment())
			{
				
				//Cas 3A demande S9
/*				if($user->getYearStudy() == 3 && $mbPeriod == $repository->getMbPeriod(2)){
					$request->getSession()->getFlashBag()->add('error', "Vous ne pouvez pas sélectionner S9, si vous voulez partir en S9, rendez vous l'année prochaine pour la prochaine campagne de mobilité.");
					return	$this->redirect($request->headers->get('referer'));										
				}*/

				//Cas 4A demande S7,S8
				if($user->getYearStudy() == 4 && ($mbPeriod == $repository->getMbPeriod(0) || $mbPeriod == $repository->getMbPeriod(1))){
					$request->getSession()->getFlashBag()->add('error', "Vous ne pouvez pas sélectionner S7 ou S8, car vous êtes en 4ème année, il est donc trop tard...");
					return	$this->redirect($request->headers->get('referer'));									
				}

				foreach($user ->getWishes() as $wish){
					if($wish->getYear()== $curYear){
						if($wish->getPriority()>$maxPrio){
							$maxPrio=$wish->getPriority();
						}
						/* s'il as déjà le voeux*/
						if($wish->getPlace() == $placeToAdd && $wish->getmobilityPeriod()==$mbPeriod){
							$request->getSession()->getFlashBag()->add('error', "Vous possédez déjà ce voeux.");
							return	$this->redirect($request->headers->get('referer'));				
						}
					}
				}

				$wish = new Wish;
				$wish->setPriority($maxPrio+1);
				$wish->setPlace($placeToAdd);
				$wish->setMobilityPeriod($mbPeriod);
				$wish->setYear($curYear);
				$user->addWish($wish);				
				$em->persist($wish);
				$em->flush();

			}

			return	$this->redirect($request->headers->get('referer'));							
		}
	}
	
	
	/*Rend un voeux plus prioritaire*/
	/**
	 * @Route("/student/action/up", name="mb_home_page_user_wish_up")
	 * @Template()
	*/
	public function indexUserWishUpAction(Request $request){
		if ($request->getMethod() == 'POST')
		{

			$id= $request->get('id');
			$em =$this
			->getDoctrine()
			->getManager();

			$repository = $em->getRepository('MbAffectBundle:Wish');
			$wish = $repository->find($id);
			$priority = $wish->getPriority();
			$repository = $em->getRepository('MbMainBundle:Year');
			$year= $repository->getCurrentYear();
			
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			$user = $wish->getUser();

			//On vérifie si la personne est autorisé
			if ($user->getId() == $currentUser->getId() || ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $currentUser->getDepartment() == $user->getDepartment())) 
			{
				//Si l'élève  est déjà placé avec un temp = 0, il ne peux pas modifier ses voeux
				if($user->getPlacement() != null){
					if($user->getPlacement()->getTemp() == 0){
						return	$this->redirect($request->headers->get('referer'));
					}
				}
				
				
				$repository = $em->getRepository('MbAffectBundle:Wish');
				$wishes = $repository->getWishesByOrdrer($user,$year);

				
				if(1 != $priority){
					//On met la prioritée du voeux à bouger à 0
					$wish->setPriority(0);
					$em->flush();


					//On échange le voeux avec lequel il faut
					foreach ($wishes as $w) {
						$p = $w->getPriority();		
						if($p == $priority-1){
							$w->setPriority($priority);
							$em->flush();
						}
						
					}
					
					//On met la bonne prioritée
					$wish->setPriority($priority - 1);
					$em->flush();
				}				
			}
		}
		
		return	$this->redirect($request->headers->get('referer'));
	}

	
	
	/*Rend un voeux moins prioritaire*/
	/**
	 * @Route("/student/action/down", name="mb_home_page_user_wish_down")
	 * @Template()
	*/
	public function indexUserWishDownAction(Request $request){
		if ($request->getMethod() == 'POST')
		{

			$id= $request->get('id');
			$em =$this
			->getDoctrine()
			->getManager();

			$repository = $em->getRepository('MbAffectBundle:Wish');
			$wish = $repository->find($id);
			$priority = $wish->getPriority();
			$repository = $em->getRepository('MbMainBundle:Year');
			$year= $repository->getCurrentYear();
			
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			$user = $wish->getUser();

			if ($user->getId() == $currentUser->getId() || ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $currentUser->getDepartment() == $user->getDepartment()))
			{
				
				//Si l'élève  est déjà placé avec un temp = 0, il ne peux pas modifier ses voeux
				if($user->getPlacement() != null){
					if($user->getPlacement()->getTemp() == 0){
						return	$this->redirect($request->headers->get('referer'));
					}
				}
				
				$repository = $em->getRepository('MbAffectBundle:Wish');
				$wishes = $repository->getWishesByOrdrer($user,$year);

				
				if($wishes[count($wishes)-1] != $wish){
					//On met la prioritée du voeux à bouger à 0
					$wish->setPriority(0);
					$em->flush();


					//On échange le voeux avec lequel il faut
					foreach ($wishes as $w) {
						$p = $w->getPriority();		
						if($p == $priority + 1){
							$w->setPriority($priority);
							$em->flush();
						}
						
					}
					
					//On met la bonne prioritée
					$wish->setPriority($priority + 1);
					$em->flush();
				}				
			}
		}
		
		return	$this->redirect($request->headers->get('referer'));
	}
	
	
	

	/*Supprime un voeux*/
	/**
	 * @Route("/student/action/delete", name="mb_home_page_user_delete")
	 * @Template()
	*/
	public function indexUserDeleteAction(Request $request){
		$id= $request->get('id');
		$em =$this->getDoctrine()->getManager();
		$repository = $em->getRepository('MbAffectBundle:Wish');
		$wish = $repository->find($id);
		
		
		$repository = $em->getRepository('MbMainBundle:Year');
		$year= $repository->getCurrentYear();
		
		$priority = $wish->getPriority();
		
		$currentUser = $this->get('security.token_storage')->getToken()->getUser();
		$user = $wish->getUser();
		$wishes = $user->getWishes();

		if ($user->getId() == $currentUser->getId() || ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $currentUser->getDepartment() == $user->getDepartment()))
		{
			//Si l'élève  est déjà placé avec un temp = 0, il ne peux pas modifier ses voeux
			if($user->getPlacement() != null){
				if($user->getPlacement()->getTemp() == 0){
					return	$this->redirect($request->headers->get('referer'));
				}
			}
			
			$em->remove($wish);
			$em->flush();
			foreach ($wishes as $w) {
				if($w->getYear() == $year){
					$p = $w->getPriority();
					if($p > $priority){
						$w->setPriority($p-1);
						$em->flush();
					}
				}
			}		
		}
		
		return	$this->redirect($request->headers->get('referer'));
	}

}
