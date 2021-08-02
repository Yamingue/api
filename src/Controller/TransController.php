<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api/transaction")
 */
class TransController extends AbstractController
{

    private $urepo;
    private $encoder;

    public function __construct(UserRepository $urepo, UserPasswordEncoderInterface $encoder)
    {
        $this->urepo = $urepo;
        $this->encoder = $encoder;
    }
    /**
     * @Route("", name="trans")
     */
    public function index(Request $req): Response
    {
        //get current user
        $user = $this->getUser();
        //get request content
        $json = $req->getContent();
        //decode to json
        $data = json_decode($json, true);
        //initialise transaction
        $transaction = new Transaction();
        $transaction->setCreateAt(new \DateTimeImmutable());
        //set status 
        $transaction->setStatus($data['status']);
        //set sender
        $transaction->setSender($user);
        if (!$this->encoder->isPasswordValid($user, $data['password'])) {
            $error[] = 'mot de pass invalide';
            return new JsonResponse(['type' => 'error', 'message' => 'mot de passe invalide']);
        }
        $error = [];
        $recever = $this->urepo->findOneBy(["id" => $data['recever']]);
        if (!$recever) {
            $error[] = 'destinataire inconnu';
        } else {
            $transaction->setRecever($recever);
        }
        if ($data['amount'] > 0) {
            $transaction->setAmount($data['amount']);
        } else {
            $error[] = "le montant ne peut etre null";
        }
        if ($data['amount'] > $user->getSolde()) {
            $error[] = "solde insuffisant";
        }

        if (count($error) > 0) {
            $response = [
                'type' => 'error',
                'message' => $error,
            ];

            return new JsonResponse($response);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($transaction);
        $em->flush();
        if ($data['status'] == 3) {
            $result = $this->consumeTransaction($transaction, 3);
            if ($result == 1) {
                return new JsonResponse([
                    'type' => 'sucess',
                    'message' => 'transastion done id:' . $transaction->getId()
                ]);
            }
        }
        return new JsonResponse([
            'type' => 'sucess',
            "message" => "transaction create. transaction id:" . $transaction->getId()
        ]);

        // return new JsonResponse( );
    }

    /**
     * @Route("/consume", name="consume")
     */
    public function consume(Request $req,TransactionRepository $tr)
    {
         //get request content
         $json = $req->getContent();
         //decode to json
         $data = json_decode($json, true);
        $trans = $tr->findOneBy(['id'=>$data['transaction']]);
        //dd($trans);
         if ($trans!=null && $this->consumeTransaction($trans,3) ) {
            return new JsonResponse([
                'type' => 'sucess',
                'message' => 'transastion done id:' . $trans->getId()
            ]);
         }

         return new JsonResponse([
            'type' => 'error',
            'message' => 'transaction error'
        ]);
    }

    public function consumeTransaction(Transaction $transaction, $code = 2)
    {
        if ( (1 <= $transaction->getStatus() && 3 > $transaction->getStatus()) && $code == 3) {
            $sender = $transaction->getSender();
            $amount = $transaction->getAmount();
            $recever = $transaction->getRecever();
            $em = $this->getDoctrine()->getManager();
            if ($sender->getSolde() < $amount) {
                $transaction->setStatus(0);
                $em->persist($transaction);
                $em->flush();
                return 0;
            } else {
                $sender->setSolde($sender->getSolde() - $amount);
                $recever->setSolde($recever->getSolde() + $amount);
                $transaction->setStatus(3);
                $em->persist($sender);
                $em->persist($recever);
                $em->persist($transaction);
                $em->flush();
            }
            return 1;
        }
        return 0;
    }
}
