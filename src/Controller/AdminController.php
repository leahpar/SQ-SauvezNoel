<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $messages = $em->getRepository(Message::class)->findBy(
            [],
            ["id" => "desc"]
        );
        return $this->render('admin/index.html.twig', [
            'messages' => $messages,
        ]);
    }
}
