<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Message $message */
            $message = $form->getData();
            $message->date = new \DateTime();
            $em->persist($message);
            $em->flush();

            $session->set("message", $message);
            return $this->redirectToRoute("user_merci");
        }

        return $this->render('user/index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/merci", name="user_merci")
     * @param SessionInterface $session
     * @return Response
     */
    public function merci(SessionInterface $session)
    {
        if (!$session->has("message")) {
            return $this->redirectToRoute("user_index");
        }

        $message = $session->get("message");
        return $this->render('user/merci.html.twig', [
            "message" => $message,
        ]);
    }

}
