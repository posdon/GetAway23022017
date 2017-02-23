<?php

namespace Mb\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request;

use Mb\FileBundle\DependencyInjection\FileExtension;
use Symfony\Component\HttpFoundation\Response;
use Mb\MainBundle\Entity\CronTask;
use Mb\FileBundle\Entity\FileSend;



class MainController extends Controller
{
	/*Redirige la page d'acceuil*/
	/**
	 * @Route("/", name="mb_main")
	 * @Template()
	 */
	public function indexAction()
	{
		if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirectToRoute('login');
		}
		if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
			return $this->redirectToRoute('mb_home_page_admin');
		}
		if($this->get('security.authorization_checker')->isGranted('ROLE_STUDENT')){
			return $this->redirectToRoute('mb_home_page_user');
		}
	}

	/*Page d'acceuil des admins*/
	/**
	 * @Route("/admin/", name="mb_home_page_admin")
	 * @Template()
	 */
	public function indexadminAction(Request $request)
	{
		$em = $this
		->getDoctrine()
		->getManager();

		$user=$this->get('security.token_storage')->getToken()->getUser();

		$department = $user->getDepartment();

		$repository = $em->getRepository('MbUserBundle:User');

		if ($request->getMethod() == 'POST')
		{
			$listUsers = $repository->getAllUsers($department,false);			
		}else{
			$listUsers = $repository->getAllUsers($department,true);
		}
		$repository = $em->getRepository('MbMainBundle:Year');
		$year = $repository->getCurrentYear();

		$repository = $em->getRepository('MbMainBundle:Department');
		$departments = $repository->findAll();

		return $this->render('MbMainBundle:Admin:index.html.twig', array('listUsers' => $listUsers, 'year' => $year, 'departments' => $departments));
	}

	/*page d'acceuil des étudiants, ou vue d'un admin sur l'étudiant*/
	/**
	 * @Route("/student/", name="mb_home_page_user")
	 * @Route("/admin/{student}", name="mb_home_page_user_admin",requirements={
	 *     "student": "\d+"
     * })
	 * @Template()
	 */
	public function indexUserAction($student = null, Request $request)
	{
		$em = $this
		->getDoctrine()
		->getManager();

		//Si c'est un étudiant qui viens sur la page
		if ($student == null)
		{
			$user=$this->get('security.token_storage')->getToken()->getUser();
		}
		//Si c'est un admin qui viens sur la page
		else
		{	
			$repository = $em->getRepository('MbUserBundle:User');
			$user = $repository->find($student);
			
			$currentUser= $this->get('security.token_storage')->getToken()->getUser();
			//On vérifie que l'admin est du département de l'élève
			if($currentUser->getDepartment() != $user->getDepartment()){	
				return $this->redirectToRoute('mb_main');
			}

			$user->setIsUpdate(false);
			$em->flush();
		}
		
		$repository = $em->getRepository('MbMainBundle:Year');
		$year = $repository->getCurrentYear();
		
		$repository = $em->getRepository('MbAffectBundle:Wish');
		//On récupère les voeux de l'année courante
		$wishes = $repository->getWishesByOrdrer($user,$year);
		
		//Création du formulaire d'upload de fichiers	
		$document = new FileSend();
		$form = $this->createFormBuilder($document)
		->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
			'label' => 'Nom',
			'data' => 'LeaningAgreement'))
		->add('file', 'Symfony\Component\Form\Extension\Core\Type\FileType')
		->getForm();

		$form->handleRequest($request);
		
		//Si le formulaire est valide,
		if ($form->isValid()) {
			if($document->getFile()!=null){
				$extension = substr($document->getFile()->getClientOriginalName(), -3, 3);
				if($extension=="pdf"){
					
					$em = $this->getDoctrine()->getManager();
					
					$document->upload();
					
					
					$user->addFilesSend($document);
					$document->setDepartment($user->getDepartment());
					$document->setYear($em->getRepository('MbMainBundle:Year')->getCurrentYear());
					$user->setIsUpdate(true);
					$date = new \DateTime();
					$document->setDate($date);
					
					//On renomme le fichier
					$fs = new Filesystem();
					$fs->rename($document->getAbsolutePath(),$document->getUploadRootDir().'/'.$date->format('U')."_".$document->getUser()->getName()."_".$document->getPath());
					$document->setPath($date->format('U')."_".$document->getUser()->getName()."_".$document->getPath());
					
					$em->persist($document);
					$em->flush();
					
				}
			}

			return	$this->redirect($request->headers->get('referer'));
		}
		
		
		return $this->render('MbMainBundle:User:index.html.twig', array('user' => $user, 'wishes' => $wishes,'form' => $form->createView()));
	}

	/* Sers à crée le cron qui gére l'update de l'année*/
    /**
     * @Route("/supAdmin/createCron", name="createCron")
     */
    // public function createCronAction()
    // {
        // $entity = new CronTask();

        // $entity
            // ->setName('Create New Year')
            // ->setInterval(60*60*24*365.25)
            // ->setCommands(array(
                // 'main:newYear'
            // ))
			// ->setLastRun(new \DateTime("2015-08-25 01:42:42"));

        // $em = $this->getDoctrine()->getManager();
        // $em->persist($entity);
        // $em->flush();

        // return new Response('OK!');
    // }
}
