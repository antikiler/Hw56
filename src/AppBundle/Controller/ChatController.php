<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Messages;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/chat")
 */

class ChatController extends Controller
{
    /**
     * @Route("/")
     */
    public function chatAction()
    {
        if (!$this->get('session')->get('user')) {
            return $this->redirectToRoute('app_basic_login');
        }
        //Обновляем время последнего действия
        $this->userSetLastTime();

        $rooms = $this->getDoctrine()
            ->getRepository(Room::class)
            ->findAll(); //получим доступные для общения комнаты чата

        return $this->render('@App/Chat/chat.html.twig', array(
            'rooms' => $rooms
        ));
    }


    /**
     * @Route("/send-message")
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function saveMessageAction(Request $request)
    {
        $room = $request->get('room'); // получаем комнату чата
        $data = $request->get('data'); // получаем текст сообщения
        $user = $this->get('session')->get('user'); //вытаскиваем пользователя из

        $em = $this->getDoctrine()->getManager();

        try{
            if ($room && $user) {

                //Обновляем время последнего действия
                $this->userSetLastTime();

                $message = new Messages();

                $message->setMessage($data);
                $room = $this->getDoctrine()
                    ->getRepository(Room::class)
                    ->find($room);

                $user = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->find($user->getId());

                $message->setRoom($room);
                $message->setUser($user);
                $message->setDatetime(new \DateTime());

                $em->persist($message);
                $em->flush();

            } else {
                throw new \Exception('Error was occurred. Room not specified or User was logged out', 7070);
            }

        }catch (\Exception $e){
            $success = false;
            if ($e->getCode() == 7070) {
                $message = $e->getMessage();
            } else {
                $message = 'Server error';
            }
        }

        return new JsonResponse([
            'success' => $success ?? true,
            'message' => $message ?? ''
        ]);


    }

    /**
     * @Route("/get-messages")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getMessagesListAction(Request $request)
    {
        $room = $request->get('room');
        $offset = $request->get('offset');

        try {
            $room = $this->getDoctrine()
                ->getRepository(Room::class)
                ->find($room);

            $messages = $this->getDoctrine()
                ->getRepository(Messages::class)
                ->findBy(
                    ['room' => $room],
                    ['datetime' => 'ASC'],
                    null,
                    $offset
                );
            $block = [];
            foreach($messages as $message) {
                $block[] = [
                    'user'    => $message->getUser()->getUsername(),
                    'message' => $message->getMessage(),
                    'time'    => $message->getDatetime()->format('d.m.Y H:i:s')
                ];
            }
        } catch(\Exception $e) {
            $success = false;
            $message = 'Server Error';
            throw $e;
        }

        return new JsonResponse([
            'success' => $success ?? true,
            'data'    => $block ?? '',
            'offset'  => $offset + count($block), //начиная с какого сообщения в следующий раз загружать данные
            'message' => $message ?? ''
        ]);

    }

    /**
     * @Route("/get-users")
     * @Method("GET")
     * @return JsonResponse
     * @throws \Exception
     */
    public function getUserListAction()
    {
        try {
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(
                    [],
                    ['datetime' => 'ASC'],
                    null
                );
            $block = [];
            foreach($users as $user) {
                $lastActionTime = $user->getLastActionTime()->format('d.m.Y H:i:s');
                $block[] = [
                    'username'         => $user->getUsername(),
                    'last_action_time' => $lastActionTime,
                    'no_activity'=>$this->getUserNoActivity($lastActionTime),
                    'avatar'=>$user->getAvatar(),
                ];
            }
        } catch(\Exception $e) {
            $success = false;
            $message = 'Server Error';
            throw $e;
        }

        return new JsonResponse([
            'success' => $success ?? true,
            'data'    => $block ?? '',
            'message' => $message ?? ''
        ]);

    }

    private function userSetLastTime()
    {
        $user = $this->get('session')->get('user');
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($user->getId());
        $user->setLastActionTime(new \DateTime());

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();
    }

    /**
     * @param string $lastActionTime
     * @return int
     */
    private function getUserNoActivity(string $lastActionTime) : int
    {
        $currentDateTime = new \DateTime();
        $time = strtotime($currentDateTime->format('d.m.Y H:i:s'));
        $lastActionTime = strtotime($lastActionTime);
        $noActivity = ($time - $lastActionTime) / 60;
        return floor($noActivity);
    }


}
