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
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class EditRolesController
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
    private $registry;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ServiceProvider
     */
    private $roleManagementConfigServiceProvider;

    /**
     * EditRolesController constructor.
     *
     * @param Environment           $twig
     * @param FormFactoryInterface  $formFactory
     * @param ManagerRegistry       $registry
     * @param UrlGeneratorInterface $urlGenerator
     * @param ServiceProvider       $roleManagementConfigServiceProvider
     */
    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        ManagerRegistry $registry,
        UrlGeneratorInterface $urlGenerator,
        ServiceProvider $roleManagementConfigServiceProvider
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->urlGenerator = $urlGenerator;
        $this->roleManagementConfigServiceProvider = $roleManagementConfigServiceProvider;
    }

    /**
     * Edit target entity's roles (The entity class should implement RoleEditableInterface)
     *
     * Request Query:
     *   - class: Entity class name (e.g. Foo\Bar\RoleEditableClassName)
     *   - pk: Primary key of the target entity, accept an integer for an id column, or an array of multiple columns
     *         e.g.
     *         - 1234 (for an id column)
     *         - ['foo' => 1234, 'bar' => 2234]
     *
     * TODO: need a twig function: nadia_simple_security_edit_roles_url($firewallName, $entityObject, $pk)
     *
     * @param Request $request
     * @param string  $firewallName
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, $firewallName)
    {
        $class = $request->query->get('class');
        $pk = $request->query->get('pk');

        $om = $this->getDoctrine()->getManagerForClass($class);
        /** @var RoleEditableInterface $target */
        $target = $om->find($class, $pk);

        $roleManagementConfig = $this->roleManagementConfigServiceProvider->get($firewallName);
        $roleGroups = $roleManagementConfig->getRoleGroups();
        $form = $this->createForm($roleGroups, ['roles' => $this->getTargetRoles($target)]);

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
                foreach ($target->getRoles() as $role) {
                    $role = (string) $role;

                    if (isset($newRoles[$role])) {
                        unset($newRoles[$role]);
                    }
                    if (isset($notSelectedRoles[$role])) {
                        $deleteRoles[] = $role;
                    }
                }

                $roleClassName = $roleManagementConfig->getRoleClassName();

                if (empty($roleClassName)) {
                    $roles = array_flip($target->getRoles());

                    foreach ($newRoles as $role) {
                        $roles[$role] = 1;
                    }
                    foreach ($deleteRoles as $role) {
                        unset($roles[$role]);
                    }

                    $target->setRoles(array_keys($roles));
                } else {
                    $roleRepo = $om->getRepository($roleClassName);

                    foreach ($newRoles as $role) {
                        $target->addRole($roleRepo->findOneBy(['role' => $role]));
                    }
                    foreach ($deleteRoles as $role) {
                        $target->removeRole($roleRepo->findOneBy(['role' => $role]));
                    }
                }

                $om->persist($target);
                $om->flush();

                $redirectUrl = $this->urlGenerator->generate(
                    '_nadia_simple_security_edit_roles',
                    compact('firewallName', 'class', 'pk')
                );

                return new RedirectResponse($redirectUrl);
            }
        }

        $formView = $form->createView();
        $viewData = [
            'roleGroups' => $roleGroups,
            'form' => $formView,
            'groupedRoleForms' => $this->createGroupedRoleForms($formView, $roleGroups),
        ];

        return new Response($this->twig->render('@NadiaSimpleSecurity/edit-user-roles/edit.html.twig', $viewData));
    }

    /**
     * @param RoleEditableInterface $target
     *
     * @return string[]
     */
    private function getTargetRoles(RoleEditableInterface $target)
    {
        $roles = [];

        foreach ($target->getRoles() as $role) {
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

    /**
     * @return ManagerRegistry
     */
    private function getDoctrine()
    {
        return $this->registry;
    }
}
