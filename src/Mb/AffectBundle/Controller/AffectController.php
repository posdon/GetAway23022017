<?php

namespace Mb\AffectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mb\AffectBundle\Entity\Placement;
use Mb\UserBundle\Filter\UserFilter;
use Mb\FileBundle\DependencyInjection\FileExtension;

/**
* @Route("/admin/affectation", name="mb_algo")
*/
class AffectController extends Controller
{
	
     /**
     * @Route("/", name="mb_algo")
     * @Template()
     */
     public function algoAction()
     {
     	$repository = $this
     	->getDoctrine()
     	->getManager()
     	->getRepository('MbUserBundle:User');

     	$dept = $this->get('security.token_storage')->getToken()->getUser()->getDepartment();
     	$listStudents = $repository->getAllWishUser2($dept);

     	return $this->render('MbAffectBundle:Affect:voeux.html.twig', array('listStudents' => $listStudents));
     }

	 /**
     * @Route("/runAlgo/{anchor}",name="mb_run_algo",requirements={
	 *     "anchor": "\d+"
     * })
     * @Template()
     */
	 public function runAlgoAction($anchor = null, Request $request)
	 {

	 	$em =$this
	 	->getDoctrine()
	 	->getManager();

	 	$user= $this->get('security.token_storage')->getToken()->getUser();	
	 	$department = $user->getDepartment();

	 	$repository = $em
	 	->getRepository('MbUserBundle:User');

		//On change l'algorithme utilisé par le départment
	 	if ($request->getMethod() == 'POST')
	 	{
	 		$algo = $request->get('algo');
	 		$department->setAlgo($algo);
	 		$em->flush();
	 	}	

		//On récupère les étudiants concernés
	 	$listStudents = $repository->getAllWishUser2($department);

		//Liste des anciens placement
	 	$listPlacements = array();
		//Liste des placements qui ont changé après l'algorithme
	 	$placementIsChange = array();

	 	$repository = $em
	 	->getRepository('MbMainBundle:Year');
	 	$year = $repository->getCurrentYear();

	 	if ($request->getMethod() == 'POST')
	 	{
			//Mise en place du verrou
	 		$bolt=$user->getDepartment()->getBolt();
	 		$user->getDepartment()->setBolt(true);
	 		$this->getDoctrine()->getManager()->flush();

			//On traite les placements fixés à la main
	 		foreach($listStudents as $student){
	 			$placement = $student->getPlacement();
	 			if($placement != null){
	 				if($placement->getTemp() == 1 || $placement->getTemp() == 0){
	 					$place = $placement->getPlace();
	 					$place->setTempPlaces($place->getTempPlaces()-1);
	 				}else if($placement->getTemp() == 2){
	 					$placement->getUser()->removePlacement();
	 					$em->remove($placement);
	 				}
	 				array_push($listPlacements,$placement);
	 			}else{
	 				array_push($listPlacements,null);
	 			}
	 		}


			//Boucle de l'algorithme
	 		foreach($listStudents as $index => $student){
	 			$placement = $student->getPlacement();
	 			if($placement != null || $student->getExcludeFromAlgo()){
					//A été affecté manuellement ou est exclue de l'algorithme donc le placement ne peux pas changer
	 				array_push($placementIsChange,false);
	 				continue;
	 			}
	 			$wishes = $student->getWishes();
	 			foreach($wishes as $wish ){
	 				if($wish->getYear()==$year){
	 					$place = $wish->getPlace();
	 					if($place->getTempPlaces() > 0){
	 						$place->setTempPlaces($place->getTempPlaces()-1);

	 						$mobilityPeriod = $wish->getMobilityPeriod();

	 						$placement = new Placement;
	 						$placement->setYear($year);
	 						$student->setPlacement($placement);
	 						$placement->setPlace($place);
	 						$placement->setMobilityPeriod($mobilityPeriod);
	 						$placement->setTemp(2);
	 						$em->persist($placement);
	 						$priority = $wish->getPriority();


							//Test si le placement a changé
	 						if($listPlacements[$index] == null || ($listPlacements[$index]->getPlace() != $place || $listPlacements[$index]->getMobilityPeriod() != $mobilityPeriod)){
	 							array_push($placementIsChange,true);
	 						}else{
	 							array_push($placementIsChange,false);
	 						}

	 						break;
	 					}	
	 				}
	 			}

				//N'as pas été affecté suite à la moulinette, donc soit il ne l'étais déjà pas, soit il l'étais et donc il y a changement
	 			if($student->getPlacement() == null && $listPlacements[$index] == null){
	 				array_push($placementIsChange,false);
	 			}elseif($student->getPlacement() == null){
	 				array_push($placementIsChange,true);
	 			}
	 		}

	 		$em->flush();

	 		$user= $this->get('security.token_storage')->getToken()->getUser();
	 		$user->getDepartment()->setBolt($bolt);
	 		$this->getDoctrine()->getManager()->flush();

	 		$em->flush();
	 	}

	 	$listProblem = $this->updatePlacesRestAction();

	 	return $this->render('MbAffectBundle:Affect:affectation.html.twig', array('listStudents' => $listStudents,'placementIsChange'=>$placementIsChange,'listProblem'=>$listProblem, 'anchor'=>$anchor));
	 }

	 /*Met à jour les places restantes pour chaque place*/
	 public function updatePlacesRestAction()
	 {
	 	$em =$this
	 	->getDoctrine()
	 	->getManager();

	 	$user= $this->get('security.token_storage')->getToken()->getUser();	
	 	$department = $user->getDepartment();

	 	$repository = $em
	 	->getRepository('MbUniversityBundle:Place');

	 	return $repository->updatePlacesRestantes($department);
	 }



	 /*Affecte manuellement tout les placements qui ont été généré par l'algorithme d'affectation du département de l'utilisateur*/
	 /**
     * @Route("/persistAll",name="mb_affect_all")
     * @Template()
     */
	 public function affectAllAction(Request $request)
	 {
	 	if ($request->getMethod() == 'POST')
	 	{
	 		$em =$this
	 		->getDoctrine()
	 		->getManager();

	 		$user= $this->get('security.token_storage')->getToken()->getUser();
	 		$department = $user->getDepartment();
	 		$repository = $em
	 		->getRepository('MbAffectBundle:Placement');

	 		$listPlacements = $repository->getPlacementTempWithDepart(2, $department);

	 		foreach($listPlacements as  $placement){
	 			$placement->setTemp(1);
	 		}

	 		$em->flush();

	 	}
	 	return $this->redirectToRoute('mb_run_algo', array());
	 }

	 /*Désaffecte  tout les placements qui ont été affecté manuellement du département de l'utilisateur*/
	 /**
     * @Route("/depersistAll",name="mb_desaffect_all")
     * @Template()
     */
	 public function desaffectAllAction(Request $request)
	 {
	 	if ($request->getMethod() == 'POST')
	 	{
	 		$em =$this
	 		->getDoctrine()
	 		->getManager();

	 		$user= $this->get('security.token_storage')->getToken()->getUser();
	 		$department = $user->getDepartment();


	 		$repository = $em
	 		->getRepository('MbAffectBundle:Placement');

	 		$listPlacements = $repository->getPlacementTempWithDepart(1,$department);

	 		foreach($listPlacements as  $placement){
	 			$placement->setTemp(2);
	 		}
	 		$em->flush();

	 	}
	 	return $this->redirectToRoute('mb_run_algo', array());
	 }


	 /*Supprime ou valide par la commission RI un placement*/
	/**
     * @Route("/deleteOrValidatePlacement",name="mb_affect_delete_validate_placement")
     * @Template()
     */
	public function deleteOrValidatePlacementAction(Request $request)
	{
		if ($request->getMethod() == 'POST')
		{
			$em =$this
			->getDoctrine()
			->getManager();

			$repository = $em
			->getRepository('MbAffectBundle:Placement');

			$placement = $repository->find($request->get('id'));
			
			$user = $placement->getUser();
			$currentUser= $this->get('security.token_storage')->getToken()->getUser();

			//On vérifie que l'admin est du département de l'élève
			if($currentUser->getDepartment() == $user->getDepartment()){			
				if($request->get('action')=='delete'){
					$placement->getUser()->removePlacement();
					$em->remove($placement);
				}else{
					$placement->setTemp(0);
				}
				$em->flush();
			}
			
			//pour rediriger au bon endroit sur la page
			$anchor=$user->getId();
		}
		return $this->redirect($this->generateUrl('mb_run_algo', array('anchor'=>$anchor)));
	}
	
	
	/*Affecte manuellement ou désaffecte un placement donné, qui est soit afecté manuellement soit affecté par l'algorithme de placement*/
	 /**
     * @Route("/persistThis",name="mb_affect_this")
     * @Template()
     */
	 public function affectThisAction(Request $request)
	 {
	 	if ($request->getMethod() == 'POST')
	 	{
	 		$id= $request->get('id');
	 		$anchor= $request->get('anchor');
	 		$em =$this
	 		->getDoctrine()
	 		->getManager();

	 		$repository = $em
	 		->getRepository('MbAffectBundle:Placement');		
	 		$placement= $repository->find($id);

	 		$user = $placement->getUser();
	 		$currentUser= $this->get('security.token_storage')->getToken()->getUser();

			//On vérifie que l'admin est du département de l'élève
	 		if($currentUser->getDepartment() == $user->getDepartment()){			
	 			if($placement->getTemp() == 2){
	 				$placement->setTemp(1);
	 			}else if($placement->getTemp() == 1){
	 				$placement->setTemp(2);
	 			}
	 		}

	 		$em->flush();

	 	}

	 	return $this->redirect($this->generateUrl('mb_run_algo', array('anchor'=>$anchor)));

	 }

	 /*Affecte manuellement un étudiant à un voeux*/
	 /**
      * @Route("/persistWish",name="mb_affect_wish")
      * @Template()
      */
	 public function affectWishAction(Request $request)
	 {
	 	if ($request->getMethod() == 'POST')
	 	{
	 		$em =$this
	 		->getDoctrine()
	 		->getManager();

	 		$id= $request->get('id');

	 		$repository = $em
	 		->getRepository('MbAffectBundle:Wish');

	 		$wish = $repository->find($id);

	 		$repository = $em
	 		->getRepository('MbMainBundle:Year');
	 		$year = $repository->getCurrentYear();


	 		$currentUser= $this->get('security.token_storage')->getToken()->getUser();
	 		$user = $wish->getUser();

	 		$placement = $user->getPlacement();

			//On vérifie que l'admin est du département de l'élève
	 		if($currentUser->getDepartment() == $user->getDepartment()){	

	 			$test = true;
	 			if($placement != null){
	 				if($placement->getPlace() == $wish->getPlace() && $placement->getMobilityPeriod() == $wish->getMobilityPeriod()){
	 					$test =false;				
	 				}
	 				$placement->getUser()->removePlacement();
	 				$em->remove($placement);
	 			}

	 			if($test){
	 				$placement = new Placement;
	 				$placement->setYear($year);
	 				$wish->getUser()->setPlacement($placement);
	 				$placement->setPlace($wish->getPlace());
	 				$placement->setMobilityPeriod($wish->getMobilityPeriod());
	 				$placement->setTemp(1);
	 				$em->persist($placement);
	 			}
	 		}

	 		$em->flush();

	 	}

	 	return	$this->redirect($request->headers->get('referer'));
	 }

	 /*Exclue un étudiant de l'algorithme*/
	 /**
      * @Route("/excludeHim",name="mb_exclude_him")
      * @Template()
      */
	 public function affectExcludeHimAction(Request $request)
	 {
	 	if ($request->getMethod() == 'POST')
	 	{
	 		$em =$this
	 		->getDoctrine()
	 		->getManager();

	 		$id= $request->get('id');

	 		$repository = $em
	 		->getRepository('MbUserBundle:User');

	 		$user = $repository->find($id);

	 		$currentUser= $this->get('security.token_storage')->getToken()->getUser();			

			//On vérifie que l'admin est du département de l'élève
	 		if($currentUser->getDepartment() == $user->getDepartment()){	
	 			$placement = $user->getPlacement();
	 			if($placement != null){
	 				$placement->getUser()->removePlacement();
	 				$em->remove($placement);
	 			}
	 			$user->setExcludeFromAlgo(!$user->getExcludeFromAlgo());

	 		}

	 		$em->flush();

	 	}

	 	return $this->redirect($this->generateUrl('mb_run_algo', array('anchor'=>$id)));
	 }	


	 /*Fiche de jury*/
	 /**
     * @Route("/jury",name="mb_jury")
     * @Template()
     */
	 public function affectJuryAction(Request $request)
	 {
	 	$em =$this
	 	->getDoctrine()
	 	->getManager();

	 	$listStudents =array();

	 	$user=$this->get('security.token_storage')->getToken()->getUser();

	 	$department = $user->getDepartment();

	 	$repository = $em
	 	->getRepository('MbMainBundle:Year');

	 	$years = $repository->findBy(array(), array('year' => 'ASC'));
		
		$period = "-1";
		$yearId = "-1";

	 	if ($request->getMethod() == 'POST')
	 	{
	 		$yearId = $request->get('year');
	 		$year = $repository->find($yearId);

	 		$period = $request->get('period');

	 		$repository = $em
	 		->getRepository('MbUserBundle:User');

	 		$listStudents = $repository->getJury($department,$year,$period);

	 	}

	 	return $this->render('MbAffectBundle:Affect:jury.html.twig', array('listStudents' => $listStudents,'years' => $years, 'annee' => $yearId, 'periode' => $period));
	 }



	/**
	 * @Route("/csv/export", name="mb_csv_export")
	 * @Template()
	*/
	public function csvExportAction()
	{
		$repository = $this
		->getDoctrine()
		->getManager()
		->getRepository('MbUserBundle:User');
		$department = $this->get('security.token_storage')->getToken()->getUser()->getDepartment();

		$list = $repository->transformToExportableArray($repository->getAllWishUser2($department));
		

		$ul = new FileExtension();
		return $ul->exportFromArray($list);
	}


}
