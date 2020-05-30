<?php

namespace PiedWeb\ConversationBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class NewsletterForm
{
    use FormTrait;

    protected function getStepOne(): FormBuilderInterface
    {
        $form = $this->initForm();
        $form->add('authorEmail', EmailType::class);
        $this->message->setMessage($this->translator->trans('conversation.suscribeToNewsletter'));

        return $form;
    }

    protected function getStepTwo(): FormBuilderInterface
    {
        $form = $this->initForm();

        $form->add('authorName', null, ['constraints' => $this->getAuthorNameConstraints()]);

        return $form;
    }
}
