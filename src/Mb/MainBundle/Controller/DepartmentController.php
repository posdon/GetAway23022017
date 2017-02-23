<?php

namespace Mb\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mb\MainBundle\Entity\Department;
use Mb\MainBundle\Form\DepartmentType;

/**
 * Department controller.
 *
 * @Route("/admin/department")
 */
class DepartmentController extends Controller
{
	
	/*Vérrouille les voeux pour les étudiant du départment de l'utilisateur*/
	 /**
     * @Route("/boltTrue",name="mb_bolt_true")
     * @Template()
     */
	 public function boltTrueAction(Request $request)
	 {
	 	if ($request->getMethod() == 'POST')
	 	{
	 		$user= $this->get('security.token_storage')->getToken()->getUser();
	 		$user->getDepartment()->setBolt(true);
	 		$this->getDoctrine()->getManager()->flush();
	 	}else{
	 		return $this->redirectToRoute('mb_main', array());
	 	}

	 	return	$this->redirect($request->headers->get('referer'));
	 }

	 /*Déverrouile les voeux pour les étudiant du départment de l'utilisateur*/
	/**
     * @Route("/boltFalse",name="mb_bolt_false")
     * @Template()
     */
	public function boltFalseAction(Request $request)
	{
		if ($request->getMethod() == 'POST')
		{
			$user= $this->get('security.token_storage')->getToken()->getUser();
			$user->getDepartment()->setBolt(false);
			$this->getDoctrine()->getManager()->flush();
		}
		
		return	$this->redirect($request->headers->get('referer'));
	}

}
