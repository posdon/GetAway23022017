<?php

namespace Mb\UniversityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mb\AffectBundle\Controller\AffectController;
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



class PlaceController extends Controller
{
	
	
	/*Ajoute une place*/
	/**
     * @Route("/admin/university/action/addOneUniv", name="mb_add_one_univ")
     * @Template()
	*/
	public function addOneUnivAction(Request $request){
		$em =$this->getDoctrine()
		->getManager();
		if ($request->getMethod() == 'POST')
		{
			$repository = $em->getRepository('MbUniversityBundle:MobilityPeriod');
			$listMobilityPeriods = $repository->findAll();			
			$university = $request->get('university');
			$country = $request->get('country');
			$places = $request->get('places');
			$mobilityPeriodArray = array();
			foreach($listMobilityPeriods as $mobilityPeriod){
				$id = $mobilityPeriod->getId();
				if($request->get($id)){
					array_push($mobilityPeriodArray, $mobilityPeriod);
				}
			}
			
			$department= $this->get('security.token_storage')->getToken()->getUser()->getDepartment();
			$repository = $em->getRepository('MbUniversityBundle:Place');
			if(is_numeric($places) && $university != null && $country != null){
				$repository->createPlace($university,$country,$places,$mobilityPeriodArray, $department);
				$request->getSession()->getFlashBag()->add('success', 'Université ajoutée');
			}else{
				$request->getSession()->getFlashBag()->add('error', "Un problème est survenu !");
			} 
			$em->flush();
		}
		return	$this->redirect($request->headers->get('referer'));
	}
	
	
	/*Edite une place*/
	/**
     * @Route("/admin/university/action/editPlace", name="mb_univ_edit_mb")
     * @Template()
	*/
	public function editPlaceAction(Request $request){
		if ($request->getMethod() == 'POST')
		{
			$id = $request->get('id');
			$em =$this->getDoctrine()
			->getManager();			
			$repository = $em->getRepository('MbUniversityBundle:Place');			
			$place = $repository->find($id);
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$department = $place->getDepartment();
			if($department == $user->getDepartment()){			
				$repository = $em->getRepository('MbUniversityBundle:MobilityPeriod');
				
				$listMobilityPeriods = $repository->findAll();
				
				foreach($listMobilityPeriods as $mobilityPeriod){
					if($request->get($mobilityPeriod->getId())){
						$place->addMobilityPeriod($mobilityPeriod);
					}else{
						$place->removeMobilityPeriod($mobilityPeriod);
					}
				}
				$place->setPlaces($request->get('places'));
				$em->flush();

				$repository = $em->getRepository('MbUniversityBundle:Place');		
				$repository->updatePlacesRestantes($department);
				$request->getSession()->getFlashBag()->add('success', 'Nombre de place modifié');
			}			
		}
		if(!$id){
			$id=null;
		}
		return $this->redirect($this->generateUrl('mb_places_list_admin', array('anchor'=>$id)));
	}
	
	/*Supprime une place et en cascade ses liaisons*/
	/**
     * @Route("/admin/university/action/deletePlace", name="mb_univ_delete_place")
     * @Template()
	*/
	public function deletePlaceAction(Request $request){
		
		if ($request->getMethod() == 'POST')
		{
			$id = $request->get('id');
			/* On ne supprime pas l'université pour l'instant 
			
			$em =$this->getDoctrine()
			->getManager();			
			$repository = $em->getRepository('MbUniversityBundle:Place');			
			$place = $repository->find($id);
			$repository = $em->getRepository('MbAffectBundle:Placement');			
			$placements = $repository->findBy(
				array('place' => $place)
				);
			
			foreach($placements as $placement){
				$placement->getUser()->removePlacement();
			}
			
			$em->remove($place);
			
			$university= $place->getUniversity();
			$repository = $em->getRepository('MbUniversityBundle:Place');
			
			if(count($repository->findBy(array('university' => $university)))==1){
				//S'il n'y as pas d'autres places pour cette université on la supprime
				$em->remove($university);
				
				$country = $university->getCountry();

				//si on l'a delete et qu'il n'y as pas d'autres universitées dans le pays, on le supprime
				$repository = $em->getRepository('MbUniversityBundle:University');
				if(count($repository->findBy(array('country' => $country)))==1){
					$em->remove($country);
				}
				
			}
			$em->flush();
			*/
			$request->getSession()->getFlashBag()->add('success', 'Fonctionnalité désactivé');	

		}
		return	$this->redirect($request->headers->get('referer'));
	}

	
	
	/* Importe des places depuis un fichier csv*/
	/**
     * @Route("/admin/university/action/import", name="Mb_univ_import")
     * @Template()
	*/
	public function importUnivPerCSVAction(Request $request){
		$em = $this->getDoctrine()->getManager();
		
		//getting all the needed repos	
		$repositories = array(
			'country' => $em->getRepository('MbUniversityBundle:Country'),
			'university' => $em->getRepository('MbUniversityBundle:University'),
			'department' => $em->getRepository('MbMainBundle:Department'),
			'place' => $em->getRepository('MbUniversityBundle:Place'),
			'mobilityPeriod' => $em->getRepository('MbUniversityBundle:MobilityPeriod'));
		
		//create the formulary
		$form = $this->createFormBuilder()
		->add('attachment', 'Symfony\Component\Form\Extension\Core\Type\FileType', array('label' => 'Fichier à uploader'))
		->add('mobilityPeriod', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
			'class'    => 'MbUniversityBundle:MobilityPeriod',
			'choice_label' => 'type',
			'label' => "Période de mobilité par défaut (ctrl + click pour plusieurs)",
			'multiple' => true
			))
		->add('separator','Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
			'choices'  => array(
				';' => "1",
				',' => "0"),
			'label' => 'Séparateur du csv',
			'choices_as_values' => true,))
		->getForm();

		$form->handleRequest($request);

		if ($form->isValid()) {
			$counter = 0;
			$ul = new FileExtension();
			//read from file the result csv file with the csv separator
			$ul->readFromFile($form["separator"],$form["attachment"], 
				function(array $columns) use ($em,$repositories,$counter,$form) {
					//action to execute for each line
					if (count($columns) >= 3) {//under 3 rows the column is not valid
						$country = $repositories["country"]->getFirstOrCreate($columns[1],$em);
						$em->flush();
						$university = $repositories["university"]->getFirstOrCreate($columns[0],$country,$em);
						$em->flush();// needed now to next computation 
						$department =  $this->get('security.token_storage')->getToken()->getUser()->getDepartment(); //$repositories["department"]->getFirst($columns[2],$em);
						
						//compute the number of place (-1 === infinity)
						$numberOfPlace = -1;	
						if($columns[2])
							$numberOfPlace = $columns[2];
						if($numberOfPlace < -1 || preg_match("#^\s*x\s*$#", strtolower($numberOfPlace))|| preg_match("#^\s*inf\s*$#", strtolower($numberOfPlace)))
							$numberOfPlace = -1;
						
						//compute the mobility periods
						$a = array(); //an array with all the mobility periods to modify
						if(count($columns)==3 ||count($columns)>=3 && !$columns[3] ){ //if semesters are unspecified
							$a = $form["mobilityPeriod"]->getData()->toArray();
						}
						else
						{
							for ($i=4; $i < count($columns); $i++) {//each remaining rows are mobility periods
								if($columns[$i]){
									$mobilityPeriod = $repositories["mobilityPeriod"]->getFirst($columns[$i],$em);
									if($mobilityPeriod)
										array_push($a, $mobilityPeriod);
								}
							}
						}	
						$repositories["place"]->getFirstOrCreate($numberOfPlace,$a,$university,$department,$em);
					}
				});

			$em->flush();

			$request->getSession()->getFlashBag()->add('success', 'Places importées');
			return $this->redirect($this->generateUrl('mb_places_list_admin'));
		}


		return $this->render('MbUniversityBundle:University:importFromList.html.twig', array(
			'form' => $form->createView(),
			));


	}

}
