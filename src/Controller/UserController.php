<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    private $encoder;
    public function __construct( UserPasswordEncoderInterface $encoder )
    {
        $this->encoder = $encoder;
        
    }
    
    /**
     * @Route("/create", name="create_user", methods={"POST"})
     */
    public function create(Request $req,UserRepository $urepo)
    {
        //get request content
        $json = $req->getContent();
        //decode to json
        $data = json_decode($json, true);
        $email =$data['email'];
        $password = $data['password'];
        if ($urepo->findOneBy(['email'=>$email])) {
           return new JsonResponse([
               'type'=>'error',
               'message'=> 'utilisateur existant'
           ],400);
        }
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->encoder->encodePassword($user,$password));
        $em =$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return new JsonResponse([
            'type'=>'success',
            'user'=> $user->getId()
        ]);
    }
}
