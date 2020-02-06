<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Nadia\Bundle\NadiaSimpleSecurityBundle\DependencyInjection\Container\ServiceProvider;
use Nadia\Bundle\NadiaSimpleSecurityBundle\Model\RoleEditableInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class EditUserRolesController
 */
class EditUserRolesController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ServiceProvider
     */
    private $roleManagementConfigServiceProvider;

    /**
     * EditUserRolesController constructor.
     *
     * @param Environment           $twig
     * @param FormFactoryInterface  $formFactory
     * @param ManagerRegistry       $doctrine
     * @param UrlGeneratorInterface $urlGenerator
     * @param ServiceProvider       $roleManagementConfigServiceProvider
     */
    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        ManagerRegistry $doctrine,
        UrlGeneratorInterface $urlGenerator,
        ServiceProvider $roleManagementConfigServiceProvider
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->doctrine = $doctrine;
        $this->urlGenerator = $urlGenerator;
        $this->roleManagementConfigServiceProvider = $roleManagementConfigServiceProvider;
    }

    /**
     * @param Request $request
     * @param string  $firewallName
     * @param string  $username
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, $firewallName, $username)
    {
        $roleManagementConfig = $this->roleManagementConfigServiceProvider->get($firewallName);
        $userProvider = $roleManagementConfig->getUserProvider();
        /** @var RoleEditableInterface $user */
        $user = $userProvider->loadUserByUsername($username);
        $roleGroups = $roleManagementConfig->getRoleGroups();
        $form = $this->createForm($roleGroups, ['roles' => $this->getUserRoles($user)]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $newRoles = array_combine($data['roles'], $data['roles']);
                $notSelectedRoles = [];
                $deleteRoles = [];

                foreach ($roleGroups as $roleGroup) {
                    foreach ($roleGroup['roles'] as $role) {
                        if (!isset($newRoles[$role['role']])) {
                            $notSelectedRoles[$role['role']] = 1;
                        }
                    }
                }
                foreach ($user->getRoles() as $role) {
                    $role = (string) $role;

                    if (isset($newRoles[$role])) {
                        unset($newRoles[$role]);
                    }
                    if (isset($notSelectedRoles[$role])) {
                        $deleteRoles[] = $role;
                    }
                }

                $roleClassName = $roleManagementConfig->getRoleClassName();
                $om = $this->doctrine->getManager($roleManagementConfig->getObjectManagerName());

                if (empty($roleClassName)) {
                    $userRoles = array_flip($user->getRoles());

                    foreach ($newRoles as $role) {
                        $userRoles[$role] = 1;
                    }
                    foreach ($deleteRoles as $role) {
                        unset($userRoles[$role]);
                    }

                    $user->setRoles(array_keys($userRoles));
                } else {
                    $roleRepo = $om->getRepository($roleClassName);

                    foreach ($newRoles as $role) {
                        $user->addRole($roleRepo->findOneBy(['role' => $role]));
                    }
                    foreach ($deleteRoles as $role) {
                        $user->removeRole($roleRepo->findOneBy(['role' => $role]));
                    }
                }

                $om->persist($user);
                $om->flush();

                $redirectUrl = $this->urlGenerator->generate(
                    '_nadia_simple_security_edit_user_roles',
                    compact('firewallName', 'username')
                );

                return new RedirectResponse($redirectUrl);
            }
        }

        $formView = $form->createView();
        $viewData = [
            'user' => $user,
            'roleGroups' => $roleGroups,
            'form' => $formView,
            'groupedRoleForms' => $this->createGroupedRoleForms($formView, $roleGroups),
        ];

        return new Response($this->twig->render('@NadiaSimpleSecurity/edit-user-roles/edit.html.twig', $viewData));
    }

    /**
     * @param RoleEditableInterface $user
     *
     * @return string[]
     */
    private function getUserRoles(RoleEditableInterface $user)
    {
        $roles = [];

        foreach ($user->getRoles() as $role) {
            $roles[] = (string) $role;
        }

        return $roles;
    }

    /**
     * @param array $roleGroups
     * @param array $data
     *
     * @return FormInterface
     */
    private function createForm(array $roleGroups, array $data)
    {
        $choices = [];

        foreach ($roleGroups as $roleGroup) {
            $choices[$roleGroup['title']] = array_combine(
                array_column($roleGroup['roles'], 'title'),
                array_column($roleGroup['roles'], 'role')
            );
        }

        return $this->formFactory->createBuilder(FormType::class, $data)
            ->add('roles', ChoiceType::class, [
                'choices' => $choices,
                'expanded' => true,
                'multiple' => true,
                'label_attr' => ['class' => 'form-check-inline'],
            ])
            ->getForm()
        ;
    }

    /**
     * @param FormView $form
     * @param array    $roleGroups
     *
     * @return FormInterface[]
     */
    private function createGroupedRoleForms(FormView $form, array $roleGroups)
    {
        $groupTitles = [];
        $groupedForms = [];

        foreach ($roleGroups as $roleGroup) {
            foreach ($roleGroup['roles'] as $role) {
                $groupTitles[$role['role']] = $roleGroup['title'];
            }
        }

        foreach ($form['roles'] as $formRole) {
            $groupTitle = $groupTitles[$formRole->vars['value']];

            if (empty($groupedForms[$groupTitle])) {
                $groupedForms[$groupTitle] = [];
            }

            $groupedForms[$groupTitle][] = $formRole;
        }

        return $groupedForms;
    }
}
