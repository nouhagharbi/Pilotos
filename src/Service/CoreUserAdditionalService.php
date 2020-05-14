<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CoreUserAdditionalService
{

    private $container;
    private $em;
    private $emInj;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->emInj = $em;
    }

    public function persist(Request $request, $method, User $user = null)
    {
        $content = json_decode($request->getContent());

        if ($method === 'create') {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $user = new User();
            $user = $this->em->getRepository(User::class)->find($user->getId());
            $user ->setPlainPassword($content->password);
            $user ->addRole('ROLE_USER_ADDITIONAL');
            $user ->setStatus(Constants::STATUS_VALID);
            $user ->setEnabled(true);
        } else {
            //delete all affected organizations then add selected one's
            foreach ($user->getCoreOrganizations() as $org) {
                $user->removeCoreOrganization($org);
            }
            $this->em->flush();
            //delete all affected agencies then add selected one's
            foreach ($user->getCoreAgencies() as $agency) {
                $user->removeCoreAgency($agency);
            }
            $this->em->flush();
            //delete all affected userRoles then add selected one's
            foreach ($user->getCoreUserRoles() as $userRole) {
                $this->em->remove($userRole);
            }
            $this->em->flush();
            if (!is_null($content->password)) {
                $user->setPlainPassword($content->password);
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updatePassword($user);
            }
        }

        $user->setIdErp($content->idErp)
            ->setEmail($content->email)
            ->setCivility($content->civility)
            ->setFirstName($content->firstName)
            ->setLastName($content->lastName)
            ->setFunction($content->function)
            ->setPhone($content->phone)
            ->setUsername($user->getEmail());

        foreach ($content->coreOrganizations as $coreOrganization) {
            $coreOrganization = $this->em->getRepository(CoreOrganization::class)->find($coreOrganization);
            $user->addCoreOrganization($coreOrganization);
        }
        foreach ($content->coreAgencies as $coreAgency) {
            $coreAgency = $this->em->getRepository(CoreAgency::class)->find($coreAgency);
            $user->addCoreAgency($coreAgency);
        }
        if ($method === 'create') {
            foreach ($content->coreRoles as $role) {
                $coreRole = $this->em->getRepository(CoreRole::class)->find($role->roleId);
                $coreUserRole = new CoreUserRole();

                $coreUserRole->setCoreRole($coreRole);
                $coreUserRole->setCoreOrganizationId($role->organizationId);
                $coreUserRole->setCoreOrganizationTypeId($role->organizationTypeId);
                $coreUserRole->setCoreUser($user);
                $user->addCoreUserRole($coreUserRole);
            }
        } else {
            foreach ($content->coreRoles as $role) {
                $role = explode('-', $role);
                $coreRole = $this->em->getRepository(CoreRole::class)->find($role[2]);
                $coreUserRole = new CoreUserRole();

                $coreUserRole->setCoreRole($coreRole);
                $coreUserRole->setCoreOrganizationId($role[0]);
                $coreUserRole->setCoreOrganizationTypeId($role[1]);
                $coreUserRole->setCoreUser($coreUserAdditional);
                $coreUserAdditional->addCoreUserRole($coreUserRole);
            }
        }

        $this->em->persist($coreUserAdditional);
        $this->em->flush();
        return new Response('', 201);
    }

    public function listOfUser($status, Request $request)
    {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $coreUserAdditionals = array();
        $coreUserAdditionalTries = array();
        $i = 0;


        // If user is an adminAdditional return users of his organizations
        if ($user instanceof CoreAdminAdditional) {
            /* @var $coreOrganization CoreOrganization */
            foreach ($user->getCoreOrganizations() as $coreOrganization) {
                /* @var $userAdditional CoreUserAdditional */
                foreach ($coreOrganization->getCoreUserAdditionals() as $userAdditional) {
                    // some users exists in many organizations so return one instance
                    if (!in_array($userAdditional, $coreUserAdditionals) && $userAdditional->getStatus() === $status) {
                        $coreUserAdditionals[] = $userAdditional;
                    }
                }
            }
            // If user is a userAdditional return users of his agencies
        } else if ($user instanceof CoreUserAdditional) {
            /* @var $coreAgency CoreAgency */
            foreach ($user->getCoreAgencies() as $coreAgency) {
                /* @var $userAdditional CoreUserAdditional */
                foreach ($coreAgency->getCoreUserAdditionals() as $userAdditional) {
                    // some users exists in many agencies so return one instance
                    if (!in_array($userAdditional, $coreUserAdditionals) && $userAdditional->getStatus() === $status) {
                        $coreUserAdditionals[] = $userAdditional;
                    }
                }
            }
        }
        /* @var $coreUserAdditional CoreUserAdditional */
        foreach ($coreUserAdditionals as $coreUserAdditional) {
            $coreUserAdditionalTries[$i]['name'] = $coreUserAdditional->getFirstName() . ' ' . $coreUserAdditional->getLastName();
            $coreUserAdditionalTries[$i]['id'] = $coreUserAdditional->getId();
            $coreUserAdditionalTries[$i]['civility'] = $coreUserAdditional->getCivility();
            $coreUserAdditionalTries[$i]['firstName'] = $coreUserAdditional->getFirstName();
            $coreUserAdditionalTries[$i]['lastName'] = $coreUserAdditional->getLastName();
            $coreUserAdditionalTries[$i]['email'] = $coreUserAdditional->getEmail();
            $coreUserAdditionalTries[$i]['function'] = $coreUserAdditional->getFunction();
            $coreUserAdditionalTries[$i]['phone'] = $coreUserAdditional->getPhone();
            $coreUserAdditionalTries[$i]['idErp'] = $coreUserAdditional->getIdErp();
            $coreUserAdditionalTries[$i]['enabled'] = $coreUserAdditional->isEnabled();

            if ($user instanceof CoreAdminAdditional) {
                $j = 0;
                $coreUserAdditionalTries[$i]['coreRoles'] = array();
                /* @var $coreUserRole CoreUserRole */
                foreach ($coreUserAdditional->getCoreUserRoles() as $coreUserRole) {
                    if (!is_null($coreUserRole->getCoreOrganizationId()) && !is_null($coreUserRole->getCoreOrganizationTypeId())) {
                        $coreUserAdditionalTries[$i]['coreRoles'][$j]['roleName'] = $coreUserRole->getCoreRole()->getName();

                        $coreOrganization = $this->em->find(CoreOrganization::class, $coreUserRole->getCoreOrganizationId());

                        $coreOrganizationType = $this->em->find(CoreOrganizationType::class, $coreUserRole->getCoreOrganizationTypeId());

                        $coreUserAdditionalTries[$i]['coreRoles'][$j]['companyName'] = $coreOrganization->getCompanyName();
                        $coreUserAdditionalTries[$i]['coreRoles'][$j]['organizationTypeName'] = $coreOrganizationType->getName();
                        $j++;
                    }

                }
            } else if ($user instanceof CoreUserAdditional) {
                $j = 0;
                $coreUserAdditionalTries[$i]['coreRoles'] = array();

                $coreOrganization = $coreUserAdditional->getCoreOrganizations()->get(
                    array_search($request->get('selectedOrganization'),
                        (array)json_decode(json_encode($coreUserAdditional->getCoreOrganizations()))));
                /* @var $coreUserRole CoreUserRole */
                foreach ($coreUserAdditional->getCoreUserRoles() as $coreUserRole) {

                    if ($coreUserRole->getCoreOrganizationId() == $request->get('selectedOrganization')
                        && $coreUserRole->getCoreOrganizationTypeId() == $request->get('selectedOrganizationType')) {

                        $coreUserAdditionalTries[$i]['coreRoles'][$j]['roleName'] = $coreUserRole->getCoreRole()->getName();

                        $coreOrganizationType = $coreOrganization->getCoreOrganizationTypes()->get(

                            array_search($request->get('selectedOrganizationType'),
                                (array)json_decode(json_encode($coreOrganization->getCoreOrganizationTypes()))));

                        $coreUserAdditionalTries[$i]['coreRoles'][$j]['companyName'] = $coreOrganization->getCompanyName();
                        $coreUserAdditionalTries[$i]['coreRoles'][$j]['organizationTypeName'] = $coreOrganizationType->getName();
                    }

                    $j++;
                }
            }
            $i++;
        }
        $coreUserAdditionalTrie = $this->sort_arr_of_obj($coreUserAdditionalTries, 'name', 'asc');
        return $coreUserAdditionalTrie;
    }

    function sort_arr_of_obj($array, $sortby, $direction = 'asc')
    {

        $sortedArr = array();
        $tmp_Array = array();

        foreach ($array as $k => $v) {
            $tmp_Array[] = strtolower($v[$sortby]);
        }

        if ($direction == 'asc') {
            asort($tmp_Array);
        } else {
            arsort($tmp_Array);
        }

        foreach ($tmp_Array as $k => $tmp) {
            $sortedArr[] = $array[$k];
        }

        return $sortedArr;
    }

    public function getUserAdditionalsForPagination(string $status, Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        return $this->emInj->getRepository(CoreUserAdditional::class)
            ->getUserAdditionalsForPagination($user, $request, $status);
    }
    /* allows to check presence of users*/
    public function checkPresenceOfUsers()
    {
        $this->emInj->getRepository(CoreUserAdditional::class)->checkPresenceOfUsers();
        return new JsonResponse(array('success' => true));
    }

}