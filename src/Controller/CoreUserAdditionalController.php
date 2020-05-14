<?php

namespace App\Controller;

use App\Constant\Constants;
use App\Service\CoreUserAdditionalService;
use App\Service\UserManager;
use AppBundle\Entity\CoreAdminAdditional;
use AppBundle\Entity\CoreAgency;
use AppBundle\Entity\CoreRole;
use AppBundle\Entity\CoreRoleTransactionAccess;
use App\Entity\User;
use AppBundle\Entity\CoreUserAdditional;
use AppBundle\Entity\CoreUserRole;
use OAuth2\OAuth2;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class CoreUserAdditionalController extends AbstractController
{


    /**
     * @Route(
     *     name="api_core_user_create",
     *     path="/api/core-user-additionals/create",
     *     methods={"POST"},
     *     defaults={
     *          "_api_resource_class"=CoreUserAdditional::class,
     *          "_api_collection_name"="api_core_user_create"
     *      }
     *  )
     */
    public function create(Request $request, $method, User $user = null , Container $container1)
    {
        $em = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent());
            $user = new User();

            $user = $this->em->getRepository(User::class)->find($user->getId());
            $user->setPlainPassword($content->password);
            $user->addRole('ROLE_USER_ADDITIONAL');
            $user->setStatus(Constants::STATUS_VALID);
            $user->setEnabled(true);
    }

    /**
     * @Route(
     *     name="api_core_user_edit",
     *     path="/api/core-user-additionals/{id}/edit",
     *     methods={"PUT"},
     *     defaults={
     *          "_api_resource_class"=CoreUserAdditional::class,
     *          "_api_item_name"="api_core_user_edit"
     *      }
     *  )
     */
    public function edit(Request $request, $method, User $user = null , Container $container1)
    {
        $em = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent());

        if ($method === 'create') {
            $user = $this->$container1->getToken()->getUser();
            $user = new User();

            $user = $this->em->getRepository(User::class)->find($user->getId());
            $user->setPlainPassword($content->password);
            $user->addRole('ROLE_USER_ADDITIONAL');
            $user->setStatus(Constants::STATUS_VALID);
            $user->setEnabled(true);
        }
    }
    /**
     * @Route("/api/core-user-additionals/check-unique-by-email/{email}",
     *     name="core_user_additional_check_unique_email",
     *     requirements={
     *          "email"=".+"
     *      }
     * )
     * @Method({"GET"})
     */
    public function checkUniqueEmailAction($email)
    {
        $em = $this->getDoctrine()->getManager();
        $coreUser = $em->getRepository(User::class)->findOneBy(array(
            'email' => $email
        ));

        return new JsonResponse(array(
            'isUnique' => is_null($coreUser)
        ));
    }


    /**
     * @Route(
     *     name="core_user_additional_get_item",
     *     path="/api/core-user-additionals/{id}/get-item",
     *     )
     * @Method({"GET"})
     */
    public function getItemAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        return new JsonResponse($em->getRepository(User::class)->getItem($id));
    }


}