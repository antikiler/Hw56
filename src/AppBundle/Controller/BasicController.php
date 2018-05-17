<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        if ($this->get('session')->get('user')) {
            return $this->redirectToRoute('app_chat_chat');
        } else {
            return $this->redirectToRoute('app_basic_login');
        }
    }

    /**
     * @Route("/registration")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $password = sha1($user->getPassword());
            $user->setPassword($password);


            /**
             * @var UploadedFile $file
             */
            $file = $user->getAvatar();

            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );

            $user->setAvatar($fileName);
            $user->setDateTime(new \DateTime());
            $user->setLastActionTime(new \DateTime());

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_basic_login', array());
        }

        return $this->render('@App/Basic/register.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/login")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function loginAction(Request $request)
    {
        $form_builder = $this->createFormBuilder();
        $form_builder->add('login');
        $form_builder->add('password', PasswordType::class);
        $form_builder->add('check', SubmitType::class);
        $form = $form_builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'username' => $form->getData()['login'],
                    'password' => sha1($form->getData()['password'])
                ]);

            if($user) {
                $this->get('session')->set('user', $user);
                return $this->redirectToRoute('app_chat_chat');
            }

            $error = 'Login/Password is not valid';
        }

        return $this->render('@App/Basic/login.html.twig', array(
            'form' => $form->createView(),
            'error' => $error ?? null
        ));
    }


}
