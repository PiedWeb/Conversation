<?php

namespace PiedWeb\ConversationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ConversationAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->with('admin.conversation.label.conversation', ['class' => 'col-md-8'])
            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 6],
                'label' => 'admin.conversation.message.label',
            ])
            ->add('referring', TextType::class, [
                'label' => 'admin.conversation.referring.label',
            ])
            ->add('createdAt', DateTimePickerType::class, [
                'label' => 'admin.conversation.createdAt.label',
            ])
            ->end();

        $formMapper->with('admin.conversation.label.author', ['class' => 'col-md-4'])
            ->add('authorEmail', null, [
                'label' => 'admin.conversation.authorEmail.label',
            ])
            ->add('authorName', null, [
                'required' => false,
                'label' => 'admin.conversation.authorName.label',
            ])
            ->add('authorIp', null, [
                'required' => false,
                'label' => 'admin.conversation.authorIp.label',
                'attr' => [
                    ($this->getSubject() ? ($this->getSubject()->getAuthorIp() ? 'disabled' : 't') : 't') => '',
                ],
            ])
            ->end();

        $formMapper->with('admin.conversation.label.publishedAt', ['class' => 'col-md-4'])
            ->add('publishedAt', DateTimePickerType::class, [
                'required' => false,
                'label' => '',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('referring', null, [
            'label' => 'admin.conversation.from.label',
        ]);
        $datagridMapper->add('authorEmail', null, [
            'label' => 'admin.conversation.authorEmail.label',
        ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('referring', TextType::class)
            ->addIdentifier('message')
            ->add('authorEmail')
            ->add('authorName')
            ->add('authorIpRaw')
            ->add('createdAt')
            ->add('publishedAt');
    }
}
