<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PipelineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('url')
            ->add('securityToken')
            ->add('environment')
            // submit
            ->add('save', SubmitType::class, array(
                'label' => 'labels.label_save'
            ))
            ->add('delete', SubmitType::class, array(
                'label' => 'labels.label_delete'
            ))//
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetFormDataEvent'));

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Pipeline'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_pipeline';
    }

    public function preSetFormDataEvent(FormEvent $event)
    {
        $object = $event->getData();
        $form = $event->getForm();

        if ($object->getId()) {
            // is editing
        } else {
            // is new, remove delete button
            $form->remove('delete');
        }
    }

}
