<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScreenController extends AbstractController
{
    /**
     * @Route("/screen", name="screen")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('screen/index.html.twig');
    }

    /**
     * @Route("/screen/sse", name="screen_sse")
     * @param EntityManagerInterface $em
     */
    public function sse(EntityManagerInterface $em)
    {
        // disable default disconnect checks
        ignore_user_abort(true);

        // set headers for stream
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Access-Control-Allow-Origin: *");

        echo "retry: 2000\n";

        // Is this a new stream or an existing one?
        $lastEventId = floatval($_SERVER["HTTP_LAST_EVENT_ID"] ?? 0);
        if ($lastEventId == 0) {
            $lastEventId = floatval($_GET["lastEventId"] ?? 0);
        }

        // Dernier message
        /** @var Message $message */
        $message = $em->getRepository(Message::class)->lastMessage() ?? new Message();
        $current_uc = floatval($_GET["lastMessageAccepted"] ?? $message->accepted);

        $count = $em->getRepository(Message::class)->count([]);

        // Infos initiales
        echo "id: " . $lastEventId++ . "\n";
        echo "data: " . json_encode([
                "messages" => [],
                "count" => $count,
            ]) . " \n\n";
        ob_flush();
        flush();


        // start stream
        while (true) {

            if (connection_aborted()) die();

            // Derniers messages
            $messages = $em->getRepository(Message::class)->newMessages($current_uc);
            dump($messages);

            if (count($messages) > 0) {

                // Messages triés par id ASC
                $current_uc = end($messages)->accepted;

                $count = $em->getRepository(Message::class)->count([]);

                echo "id: " . $lastEventId++ . "\n";
                echo "data: " . json_encode([
                    "messages" => $messages,
                    "count" => $count,
                ]) . " \n\n";
                ob_flush();
                flush();
            }

            else {
                // no new data to send
                echo ": heartbeat\n\n";
                ob_flush();
                flush();
            }

            // X second sleep then carry on
            sleep(2);

            // for debug
            //return new Response();

        }

    }

}
