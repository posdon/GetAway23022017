<?php

namespace Mb\AffectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mb\AffectBundle\Entity\Placement;
use Mb\AffectBundle\Entity\Agreement;
use Mb\AffectBundle\Entity\Subject;


class AgreementController extends Controller
{
	
	/* Soumet un learning agreement */
	/**
     * @Route("/student/submitAgreement",name="mb_submit_agreement")
     * @Template()
     */
	public function submitAgreementAction(Request $request)
	{
		$em =$this
		->getDoctrine()
		->getManager();

		if ($request->getMethod() == 'POST')
		{
			$test = false;
			$agreement = new Agreement;
			$agreement->setValidate(false);
			$user= $this->get('security.token_storage')->getToken()->getUser();

			$counter = $request->get('counter');
			if(!(is_numeric($counter) && $counter<50)){
				return;
			}

			$user->getPlacement()->addAgreement($agreement);

			//0 : crédit univ, 1 : credit ECTS
			$typeCredit= $request->get('typeCredit') == 1;
			$agreement->setIsECTS($typeCredit);
			for($i = 0; $i < $counter; $i++){
				$name= $request->get('name' . $i);
				$ects= $request->get('ects' . $i);
				if($name != "" && is_numeric($ects)){
					$subject = new Subject;
					$subject->setName($name);
					$subject->setEcts($ects);
					$subject->setValidate(false);
					$agreement->addSubject($subject);
					$em->persist($subject);
					$test = true;
				}

			}
			if($test){
				$agreements = $user->getPlacement()->getAgreements();
				foreach($agreements as $pastAgreement ){
					if(!$pastAgreement->getValidate()){
						$em->remove($pastAgreement);
					}
				}
				$em->persist($agreement);
				$user->setIsUpdate(true);
				$em->flush();
			}

		}
		return $this->redirectToRoute('mb_main');

	}

	/* Supprime un learning agreement */
    /**
     * @Route("/student/deleteAgreement",name="mb_delete_agreement")
     * @Template()
     */
    public function deletetAgreementAction(Request $request)
    {
    	$em =$this
    	->getDoctrine()
    	->getManager();

    	if ($request->getMethod() == 'POST')
    	{
    		$currentUser= $this->get('security.token_storage')->getToken()->getUser();
    		$id = $request->get('id');
    		$repository = $em
    		->getRepository('MbAffectBundle:Agreement');
    		$agreement = $repository->find($id);

    		$id = $request->get('iduser');
    		$repository = $em
    		->getRepository('MbUserBundle:User');
    		$user = $repository->find($id);

    		if(($user == $agreement->getPlacement()->getUser() && $user == $currentUser )||
    			($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $currentUser->getDepartment() == $user->getDepartment())){

    			$em->remove($agreement);
    		$em->flush();
    		$request->getSession()->getFlashBag()->add('warning', 'Leanring Agreement supprimé');

    	}

    }

    return	$this->redirect($request->headers->get('referer'));

}

/*Ajoute un learning agreement */
	/**
     * @Route("/admin/setAgreement",name="mb_set_agreement")
     * @Template()
     */
	public function setAgreementAction(Request $request)
	{
		$em =$this
		->getDoctrine()
		->getManager();

		if ($request->getMethod() == 'POST')
		{
			$currentUser= $this->get('security.token_storage')->getToken()->getUser();
			$id = $request->get('iduser');
			
			$counter = $request->get('counter');
			if(!(is_numeric($counter) && $counter<50)){
				return;
			}
			
			$repository = $em
			->getRepository('MbUserBundle:User');
			$user = $repository->find($id);
			$test = false;
			
			//0 : crédit univ, 1 : credit ECTS
			$typeCredit= $request->get('typeCredit') == 1;


			if($currentUser->getDepartment() == $user->getDepartment()){				
				$agreement = new Agreement;
				$agreement->setIsECTS($typeCredit);

				$agreement->setValidate(true);
				$user->getPlacement()->addAgreement($agreement);
				for($i = 0; $i < $counter; $i++){
					
					$name= $request->get('name' . $i);
					$ects= $request->get('ects' . $i);
					if($name != "" && is_numeric($ects)){
						$subject = new Subject;
						$subject->setName($name);
						$subject->setEcts($ects);
						$subject->setValidate(false);						
						$agreement->addSubject($subject);
						$em->persist($subject);
						$test = true;
					}

				}
				/*Supprime l'ancien learning agrrement*/
				if($test){
					$agreements = $user->getPlacement()->getAgreements();
					foreach($agreements as $pastAgreement ){
						if($pastAgreement->getValidate()){
							$em->remove($pastAgreement);
						}
					}
					$em->persist($agreement);
					$em->flush();
				}
			}
		}
		return	$this->redirect($request->headers->get('referer'));
	}
	
	/* Accepte ou refuse un learning agreement */
	/**
     * @Route("/admin/acceptAgreement",name="mb_accept_agreement")
     * @Template()
     */
	public function acceptAgreementAction(Request $request)
	{
		$em =$this
		->getDoctrine()
		->getManager();

		if ($request->getMethod() == 'POST')
		{
			$currentUser= $this->get('security.token_storage')->getToken()->getUser();
			$id = $request->get('id');
			$repository = $em
			->getRepository('MbAffectBundle:Agreement');
			$agreement = $repository->find($id);

			$id = $request->get('iduser');
			$repository = $em
			->getRepository('MbUserBundle:User');
			$user = $repository->find($id);	

			$com = $request->get('com');

			if($currentUser->getDepartment() == $user->getDepartment()){

				if($request->get('action') == 'negate'){
					$agreement->setCom(" " . $com);
					$request->getSession()->getFlashBag()->add('warning', 'Leanring Agreement refusé');
				}else{
					$agreements = $user->getPlacement()->getAgreements();
					foreach($agreements as $pastAgreement ){
						if($pastAgreement->getValidate()){
							$em->remove($pastAgreement);
						}
					}
					$agreement->setValidate(true);
					$request->getSession()->getFlashBag()->add('success', 'Leanring Agreement validé');
				}
				$em->flush();
			}

		}

		return $this->redirectToRoute('mb_home_page_user_admin', array('student' => $id));
	}

	/*ajoute des notes au learning agreement*/
	/**
     * @Route("/student/setMarks",name="mb_set_marks")
     * @Template()
     */
	public function setMarksAction(Request $request)
	{
		$em =$this
		->getDoctrine()
		->getManager();

		if ($request->getMethod() == 'POST')
		{
			
			$currentUser= $this->get('security.token_storage')->getToken()->getUser();
			$id = $request->get('iduser');
			
			$counter = $request->get('counter');
			
			//Sidonnés non valides
			if(!(is_numeric($counter) && $counter<50)){
				return;
			}
			
			$com = $request->get('com');
			$repository = $em
			->getRepository('MbUserBundle:User');
			$user = $repository->find($id);
			for($i = 0; $i < $counter; $i++){
				
				$id = $request->get('subject' . $i);
				if(!is_numeric($id)){
					break;
				}
				$repository = $em
				->getRepository('MbAffectBundle:Subject');
				$subject = $repository->find($id);
				$mark = $request->get('mark' . $i);
				$validate = $request->get('validate' . $i);
				if($subject !=null){
					if(($user == $subject->getAgreement()->getPlacement()->getUser() && $user == $currentUser )||
						($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $currentUser->getDepartment() == $user->getDepartment())){				
						$subject->setMark($mark);							
					$user->setIsUpdate(true);


					if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
						if($validate){
							$subject->setValidate(true);
						}else{
							$subject->setValidate(false);	
						}
						$subject->getAgreement()->setCom($com);
					}
				}
			}

		}
		$em->flush();
	}

	return	$this->redirect($request->headers->get('referer'));
}

}
