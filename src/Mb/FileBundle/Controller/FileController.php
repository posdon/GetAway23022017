<?php

namespace Mb\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Mb\FileBundle\Entity\FileSend;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class FileController extends Controller
{
	public function indexAction($name)
	{
		return $this->render('MbFileBundle:Default:index.html.twig', array('name' => $name));
	}
	
	/**
     * @Route("/student/deleteFile", name="mb_delete_file")
     * @Template()
    */
	public function deleteFileAction(Request $request)
	{
		if ($request->getMethod() == 'POST')
		{
			$em =$this
			->getDoctrine()
			->getManager();
			
			$repository = $em
			->getRepository('MbFileBundle:FileSend');
			
			$file = $repository->find($request->get('id'));
			$user = $file->getUser();
			$currentUser= $this->get('security.token_storage')->getToken()->getUser();
			
			if ($user == $currentUser || ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $currentUser->getDepartment() == $user->getDepartment()))
			{
				$em->remove($file);
				$em->flush();
				$request->getSession()->getFlashBag()->add('success', 'Fichier supprimé');
				
			}
		}
		
		return	$this->redirect($request->headers->get('referer'));

	}
	
}
