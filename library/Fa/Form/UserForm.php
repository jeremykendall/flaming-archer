<?php

namespace Fa\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', array(
            'required' => true,
            'label' => 'First name',
        ));
        $builder->add('lastName', 'text', array(
            'required' => true,
            'label' => 'Last name',
        ));
        $builder->add('email', 'email', array(
            'required' => true,
            'label' => 'Email',
        ));
        $builder->add('passwordHash', 'password', array(
            'required' => true,
            'label' => 'Password',
        ));
        $builder->add('flickrUsername', 'text', array(
            'required' => true,
            'label' => 'Flickr username',
        ));
        $builder->add('flickrApiKey', 'text', array(
            'required' => true,
            'label' => 'Flickr API Key',
        ));
        $builder->add('externalUrl', 'url', array(
            'required' => true,
            'label' => 'Website',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fa\Entity\User',
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
