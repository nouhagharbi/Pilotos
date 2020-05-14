<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\AccessToken;
use App\Entity\User;
use App\Service\AouthService;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Controller\RegistrationController;
use FOS\UserBundle\Controller\ResettingController;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use OAuth2\IOAuth2;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;


class UserController extends AbstractController
{
    private $userManager;
    private $encoderFactory;
    private $ioauth;
    private $aouthservice;
    private $retryTtl;
    private $valueToCheck;

    public function __construct(UserManager $userManager, EncoderFactoryInterface $encoderFactory)
    {
        $this->userManager = $userManager;
        $this->encoderFactory = $encoderFactory;

    }




    /**
     * @Route("/my_login", name="app_login2")
     */
    public function login(Request $request,OAuth2 $oauth2): Response
    {
        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $res = $request->get("email");
            $password = $request->get('password');
            $user = $this->userManager->getUserByEmail($res);
            $encoder = $this->encoderFactory->getEncoder($user);
            $bool = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());

            if (!$user instanceof User || !$bool) {
                return new JsonResponse(['message' => 'Invalid credentials'], 500);
            }

            if (!$user->isEnabled()) {
                return new JsonResponse(['message' => 'User account is disabled.'], 500);
            }

            $request2 = new Request();
            $request2->query->add([
                'client_id' => $this->getParameter('oauth2_client_id'),
                'client_secret' => $this->getParameter('oauth2_client_secret'),
                'grant_type' => 'password',
                'username' => $user->getUsername(),
                'password' => $request->get('password')
            ]);

            try {
                return new JsonResponse(array_merge(
                    json_decode(
                        $oauth2
                            ->grantAccessToken($request2)
                            ->getContent(), true
                    ), array(
                        'expires_at' => (new \DateTime())->getTimestamp() + $this->getParameter('token_lifetime'),
                        'user_id' => $user->getId(),
                        'email' => $user->getEmail(),
                    )
                ));
            } catch (OAuth2ServerException $e) {
                return new JsonResponse($e->getHttpResponse());
            }
        }

    }






    /**
     * @Route("/my_register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder):JsonResponse
    {
        // success
        if ($request->isMethod('POST')) {
            $user = new User();

            $user->setEmail($request->request->get('email'));
            $user->setUsername($request->request->get('username'));
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);

            $em->flush();
            return new JsonResponse('success');
        }

        return new JsonResponse('error1');
    }




    /**
     * @Route("/check-email-to-reset", name="check_email_to_reset")
     */
    public function checkEmailToResetAction( \Swift_Mailer $mailer, Request $request , UserManagerInterface $userManager , TokenGeneratorInterface $tokenGenerator)
    {
        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
            $email = $request->request->get('email');
            $user = $this->userManager->getUserByEmail($email);
            if (!$user instanceof User) {
                return new JsonResponse(['isValid' => false], 200);
            }

            /** @var $tokenGenerator TokenGeneratorInterface */
            $token = $tokenGenerator->generateToken();


            try {
                $user->setConfirmationToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return new JsonResponse('false1', 500);
            }

            $data = "<a href='" . $this->getParameter("front_url") . "session/reset/" .
                $user->getConfirmationToken() . "'>Reset</a>";

            $message = (new \Swift_Message('chek email to reset '))
                ->setFrom('nouhagharbi188@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    "le token pour reseter votre mot de passe : " . $data,
                    'text/html'
                );
            $mailer->send($message);
            $this->addFlash('notice', 'Mail envoyé');
            $user->setPasswordRequestedAt(new \DateTime());
            $userManager->updateUser($user);
            return  new JsonResponse("True ".$user->getEmail(),200);

        }
        return new JsonResponse('false2',500);

    }



    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request,string $token, UserPasswordEncoderInterface $passwordEncoder)
    {


        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
            /* @var $user User */
            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
            return new JsonResponse('password updated',200);
            /* @var $user User */

            if ($user === null) {
                return new JsonResponse('Unknown Token',500);
            }
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();

            return new JsonResponse('password updated',200);
        }else {

            return new JsonResponse('Not allowed',500);

        }

    }


    /**
     * @Route("/forgotten_password", name="app_forgotten_password")
     */
    public function forgottenPassword(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    ): Response
    {

        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $this->userManager->getUserByEmail($email);
            /* @var $user User */

            if ($user === null) {
                return  new JsonResponse('Email Inconnu',500);
            }
            $token = $tokenGenerator->generateToken();
            try{
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return  new JsonResponse('false',500);
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
            $message = (new \Swift_Message('Forgot Password'))
                ->setFrom('nouhagharbi188@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    "le token pour reseter votre mot de passe : " . $url,
                    'text/html'
                );
             $mailer->send($message);



            $this->addFlash('notice', 'Mail envoyé');
            return  new JsonResponse('true',200);
        }

        return new JsonResponse('false',500);
    }

    /**
     * @Route("/activate", name="activate_user")
     */
    public function activateAction(Request $request , UserManagerInterface $userManager ,TokenGeneratorInterface  $tokenGenerator )
    {

        $token = $request->get('token');

        $entityManager = $this->getDoctrine()->getManager();
        $user = $userManager->findUserByConfirmationToken($token);
        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        /** @var $user User */
        $token1 = $tokenGenerator->generateToken();
        $user->setConfirmationToken($token1);
        $entityManager->flush();
        $user->setEnabled(true);
        $userManager->updateUser($user);

        return new Response('true', 200);
    }



    /**
     * @Route(
     *     name="verify-currentPassword",
     *     path="/core-users/verify-currentPassword/{id}",
     *     methods={"POST"},
     * )
     */
    public function verifyCurrentPassword(User $user, Request $request)
    {
        try {
            $password = $request->get('password');
            $encoder = $this->encoderFactory->getEncoder($user);

            $bool = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
            dump($bool);
            return new JsonResponse(array(
                'verify' => $bool
            ));


        } catch (\Exception $e) {
            $response = new Response();
            $response->setContent(json_encode(array(
                'message' => $e->getMessage()
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

    /**
     * @Route(
     *     name="clear_token",
     *     path="/core-users/{id}/clear_token",
     *     methods={"GET"}
     * )
     */
    public function clearTokenAction( $id )
    {
        $em = $this->getDoctrine()->getManager();
        $accessTokens = $em->getRepository(AccessToken::class)->findBy(array('user' => $id));
        if(count($accessTokens)){
            foreach ($accessTokens as $accessToken){
                $em->remove($accessToken);
                $em->flush();
            }
            return new JsonResponse('success');
        }else{
            return new JsonResponse('no user found');
        }

    }

    public function getSecureResourceAction()
    {
        # this is it
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }


    }

    }
