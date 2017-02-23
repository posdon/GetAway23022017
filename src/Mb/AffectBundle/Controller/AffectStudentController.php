<?php

namespace Mb\AffectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mb\AffectBundle\Entity\Placement;

/**
* @Route("/student/affectation")
*/
class AffectStudentController extends Controller
{
    /**
     * @Route("/", name="mb_student_affect")
     * @Template()
     */
    public function algoAction()
    {

        //A MODIFIER POUR LA GESTION MULTI DEPARTEMENT
        $repository = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('MbUserBundle:User') ;

        $dept = $this->get('security.token_storage')->getToken()->getUser()->getDepartment();
        $listStudents = $repository->getAllWishUser2($dept);


        return $this->render('MbAffectBundle:Affect:voeux.html.twig', array('listStudents' => $listStudents));
    }

}
