<?php

namespace Mb\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mb\FileBundle\DependencyInjection\FileExtension;

use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;


class UserController extends Controller
{
	
	/*Affiche la page pour importer des utilisateurs/admin*/
    /**
     * @Route("/admin/user/import", name="mb_users_import")
     * @Template()
    */
    public function importUserAction(Request $request)
    {    
        //Create a Symfony form, with all the security that it provides
      $formOne = $this->get('form.factory')->createNamedBuilder('formOne')
      ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType', array('label'=>'Numéro étudiant ou Email'))
      ->getForm();


		  //Si on est superAdmin, il y as un formulaire pour ajouter d'autres admins
      $formAdmin = $this->get('form.factory')->createNamedBuilder('formAdmin')
      ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType')
      ->getForm();


      // Importation by CSV : create of form to choose the separator of the CSV file
      $formCSV = $this->get('form.factory')->createNamedBuilder('formCSV')
      ->add('attachment', 'Symfony\Component\Form\Extension\Core\Type\FileType', array('label' => 'Fichier à upload'))
      ->getForm();   


      /********  Handle the informations from the CSV form  ********/

      $formCSV->handleRequest($request);


      if ($formCSV->isValid()) {

        // Function taken from Mb\FileBundle\DependencyInjection\FileExtension
        $em = $this->getDoctrine()->getManager();
        $counter = 0;
        $ul = new FileExtension();
        $ul->readFromFile(';',$formCSV["attachment"], 
          function(array $columns) use (&$em, $counter, $request) {
            if (count($columns) <= 2) {

              // Retrieve the users depending on the email on column 0
              $user = $this->retrieveUser($columns[0],false);

              // if no User retrieved, $user has the false value
              if(!$user)
              { 
                // a flash text is added to help the Admin know what happened
                $response = "Erreur :";
                $request->getSession()->getFlashBag()->add('error', $response);
						            // display the email that gave an error
                $request->getSession()->getFlashBag()->add('error', $columns[0]);

              } 
              else if($user->getDepartment() == $this->get('security.token_storage')->getToken()->getUser()->getDepartment())
              {
                        // If this is a user already in the database

                        // Check if a mean is present in the second column
                        // First condition : in case a student has 0 as a mean, still count
                if( count($columns) == 2 && ($columns[1] || $columns[1] == 0) && $columns[1] >=-1 && $columns[1] <= 20 ) 
                {
                  $mean = $columns[1];
                            // store the mean given
                  $user->setMean(str_replace(',','.',$mean));
                }
                else
                {
                            //Check if there was already a mean
                  if (!$user->getMean())
                  {
                                //set it to -1 if no
                    $user->setMean(-1);
                  }
                }
              }
            }
            // if the number of columns is not 2
            else 
            {
                // Add some flash messages to show the user the emails that did not work
              $tmpString = "Trop de colonnes pour " . $columns[0];
              $request->getSession()->getFlashBag()->add('error', $tmpString);
            }

          });

        $em->flush();
        
        return $this->redirect($this->generateUrl('mb_users_import'));
      }


      /********  Handle the informations from the Mail form  ********/

      $formOne->handleRequest($request);

        // Add the user if the email shape is valid
      if ($formOne->isValid()) {

        $email = $formOne["email"]->getData();        

        $this->addUser($email,false, $request);


        return $this->redirect($this->generateUrl('mb_users_import'));
      }		      


      /********  Handle the informations from the Admin Mail form  ********/

      $formAdmin->handleRequest($request);

        // Add the user if the email shape is valid
      if ($formAdmin->isValid()) {

        $email = $formAdmin["email"]->getData();        

        $this->addUser($email,true, $request);


        return $this->redirect($this->generateUrl('mb_users_import'));
      }


      return $this->render('MbUserBundle:Default:importUser.html.twig', array(
        'formCSV' => $formCSV->createView(),
        "formOne" => $formOne->createView(),
        "formAdmin" => $formAdmin->createView()
        ));


    }


    /**
     * Add a Student from the LDAP, to the Database
     * If the Student already exists, do nothing
     * Create flash messages with logs to be displayed
     *
     * @param  string $id id can be mail or studentid of student
     */	
    private function addUser($id, $admin, Request $request){
      $id=strtolower($id);
      $userTmp = $this->retrieveUser($id,$admin);

      // The retrieveUser function return false if no User has been found or an error occured
      if(!$userTmp) {
        $response = 'Erreur lors de la récupération.';
        $type = 'error';
      } else {
        $response = 'Récupération bien effectuée : ' . $userTmp->getStudentId();
        $type = 'success';
      }

      // a flash text is added to help the Admin know what happened
      $request->getSession()->getFlashBag()->add($type, $response);

    }


    /**
     * Retrieve the user with the 'email' or StudentID element in params array in the LDAP
     * Return false if the recovery of the User returned an error
     * See the file src/Mb/UserBundle/Ldap/LdapManager.php for more information
     *
     * @param  string $id email or studentId of the Student to add to the DB
     *
     * @return string|array $values Array of values to escape
     */    
    private function retrieveUser($id,$admin)
    {
      $em = $this->getDoctrine()->getManager();

        // get our specific ldap manager which extends the LdapManger class in the fr3d/ldap-bundle
        // --> see the services.yml file at UserBundle/Ressources/config ***********************************************************
      $ldapManager = $this->get('mb.ldap.ldap_manager');

        //test si un argument a bien été donné
      if($id)
      {
            //find the user in the LDAP. Hydrate all the necessary attributes

            // If the id has the "firstname.name@insa-rennes.fr" shape, search by email
        if (preg_match("#^[a-z0-9-]+\.[a-z0-9-]+@insa-rennes.fr$#", $id))
        {
                // Get the Student in the LDAP
          $user = $ldapManager->findUserByEmail($id);

                // Check if the user is present in the database
          $userInDB = $em->getRepository('MbUserBundle:User')->findOneByEmail($id);
        }
            // If the mail has the "login@insa-rennes.fr" shape, search by username
        elseif (preg_match("#^[a-z0-9]+@insa-rennes.fr$#", $id))
        {
                $login = explode("@", $id);  //return an array

                // Get the Student in the LDAP
                $user = $ldapManager->findUserByUsername($login[0]);

                // Check if the user is present in the database
                $userInDB = $em->getRepository('MbUserBundle:User')->findOneByUsername($login[0]);

			//Si ça match avec un numéro, c'est le numéro étudiant
              }elseif(preg_match("#^[0-9]+$#", $id)){
                $user = $ldapManager->findUserByStudentId($id);

                $userInDB = $em->getRepository('MbUserBundle:User')->findOneByStudentId($id);

              }
              else 
              {
                // If the shape of the id does not match the 2 accepted ones --> return false
                $user = false;
              }
            }
            else
            {
          // If the email given by the form was empty --> return false
              return false;
            }


        // If findUserByEmail did not find any User with the email given, $user value is "false"
            if(!$user) {
              return false;
            }else{
             if($user->getDepartment()->getName() == 'ALL'){
              $user->setDepartment($this->get('security.token_storage')->getToken()->getUser()->getDepartment());
            }

          }


        // If the User is already present in the Database
          if(!is_null($userInDB) && $userInDB->getStudentId() == $user->getStudentId())
          {
           $userInDB->setDepartment($user->getDepartment());
           $userInDB->setYearStudy($user->getYearStudy());

           if($admin){
			// 'role' attribute
             $userInDB->removeRole('ROLE_STUDENT');
             $userInDB->addRole('ROLE_ADMIN');

           }else{
				 // 'role' attribute
            $userInDB->removeRole('ROLE_ADMIN');
            $userInDB->addRole('ROLE_STUDENT');

          }


          $em->persist($userInDB);
          $em->flush();

          return $userInDB;
        }// End If the User is already present in the Database


        if($admin){
			// 'role' attribute
         $user->removeRole('ROLE_STUDENT');
         $user->addRole('ROLE_ADMIN');

       }else{
			 // 'role' attribute
         $user->removeRole('ROLE_ADMIN');
         $user->addRole('ROLE_STUDENT');

       }


        //save the user retrived
       $em->persist($user);
       $em->flush();

       return $user;
     }



     /*Permet à un super Admin de changer de département*/
    /**
     * @Route("/supAdmin/user/changeDept", name="mb_change_department")
     * @Template()
    */
    public function changeDeptAction(Request $request)
    { 

      $departmentId = $request->get('departmentId');

      $em =$this
      ->getDoctrine()
      ->getManager();

      $repository = $em
      ->getRepository('MbMainBundle:Department');

      $department = $repository->find($departmentId);

      $user= $this->get('security.token_storage')->getToken()->getUser();

      $user->setDepartment($department);

      $em->flush();

      return	$this->redirect($request->headers->get('referer'));
    }

    /*Supprime un utilisateur*/
	 /**
     * @Route("/admin/user/deleteUser", name="mb_delete_user")
     * @Template()
    */
  public function deleteStudentAction(Request $request)
  { 		

    $em =$this
    ->getDoctrine()
    ->getManager();

    if ($request->getMethod() == 'POST')
    {
     $userId = $request->get('userId');			
     $repository = $em
     ->getRepository('MbUserBundle:User');

     $user = $repository->find($userId);

     $currentUser = $this->get('security.token_storage')->getToken()->getUser();

     if($currentUser->getDepartment() == $user->getDepartment()){

      $em->remove($user);

      $em->flush();	

    }

  }
  return $this->redirect($this->generateUrl('login'));
}


/*Met la moyenne à un utilisateur*/
	/**
	 * @Route("/admin/action/addMean", name="mb_user_mean")
	 * @Template()
	*/
	public function addUserMeanAction(Request $request)
	{

		$id= $request->get('id');
		$mean=$request->get('mean');
		$em =$this->getDoctrine()->getManager();
		$repository = $em->getRepository('MbUserBundle:User');
		$user = $repository->find($id);
		$user->setMean($mean);
		$em->flush();
		return	$this->redirect($request->headers->get('referer'));
	}

	/*Met un commentaire à un utilisateur*/
	/**
	 * @Route("/admin/action/addCom", name="mb_user_com")
	 * @Template()
	*/
	public function addUserComAction(Request $request)
	{

		$id= $request->get('id');
		$com=$request->get('com');
		$em =$this->getDoctrine()->getManager();
		$repository = $em->getRepository('MbUserBundle:User');
		$user = $repository->find($id);
		$user->setCom($com);
		$em->flush();
		return	$this->redirect($request->headers->get('referer'));
	}


}
