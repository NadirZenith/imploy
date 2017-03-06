<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\PlatformUserInterface;
use AppBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserType extends AbstractType
{
    private $security;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->security = $options['security.authorization_checker'];

        $builder
            ->add('username', TextType::class, array(
                'label' => 'labels.label_username',
                'attr'  => array('addon' => array('icon' => 'user'))
            ))
            ->add('email', EmailType::class, array(
                'label' => 'labels.label_email',
                'attr'  => array(
                    'addon' => array('icon' => 'envelope')
                )
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options'         => array(
                    'attr' => array('addon' => array('icon' => 'lock'))
                ),
                'first_options'   => array('label' => 'labels.label_password'),
                'second_options'  => array('label' => 'labels.label_password_confirm'),
                'attr'            => array('addon' => array('icon' => 'lock'))
            ))
            ->add('enabled', CheckboxType::class, array(
                'label' => 'labels.label_enabled'
            ))
            ->add('roles', ChoiceType::class, array(
                'label'       => 'labels.label_user_roles',
                'multiple'    => true,
                'choices'     => User::$AVAILABLE_ROLES,
                'choice_attr' => function ($role, $key, $index) {
                    return (in_array($role, array(User::ROLE_DEFAULT)))
                        ? array('disabled' => 'disabled', 'readonly' => 'readonly')
                        : array();
                },
                'attr'        => array('class' => 'chosen-select')
            ))
            ->add('locale', ChoiceType::class, array(
                'label_format' => 'labels.label_%name%',
                'label'        => 'labels.label_locale',
                'choices'      => User::$AVAILABLE_LOCALES,
                'attr'         => array('class' => 'chosen-select')
            ))
            ->add('githubUsername', TextType::class, array(
                'label' => 'labels.label_github_username',
                'attr'  => array('addon' => array('icon' => 'user'))
            ))
            ->add('pipeline', EntityType::class, array(
                'label' => 'labels.label_pipeline',
                'class' => 'AppBundle\Entity\Pipeline',
                'choice_label' => 'name',
                'attr'  => array('class' => 'chosen-select')
            ))
            // submit
            ->add('save', SubmitType::class, array('label' => 'labels.label_save', 'attr' => array('parentClass' => 'test')))
            ->add('delete', SubmitType::class, array(
                'label' => 'labels.label_delete'
            ))//
        ;

        // remove form fields
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetFormDataEvent'));

        // set user provider before validating
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'submitEvent'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => User::class,
            'validation_groups' => function (FormInterface $form) {

                $groups = array('Default');

                // if new validate password
                if (null === $form->getData()->getId()) {
                    array_push($groups, 'create');
                }

                return $groups;
            }
        ));

        $resolver->setRequired('security.authorization_checker');
    }

    /**
     * Dynamic remove fields (buttons & roles)
     * @param FormEvent $event
     */
    public function preSetFormDataEvent(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (!$this->security->isGranted(User::ROLE_SUPER_ADMIN)) {
            $form->remove('roles');
            $form->remove('enabled');
//            $form->remove('delete');
        }

        if ($user->getId()) {
            // is editing

//            $form->remove('delete');
        } else {
            // is new, remove delete button
            $form->remove('delete');
        }
    }

    /**
     * Set user provider before validating
     * @param FormEvent $event
     */
    public function submitEvent(FormEvent $event)
    {
        $user = $event->getData();

        // all users must have this role
        $user->addRole(User::ROLE_DEFAULT);
    }

}