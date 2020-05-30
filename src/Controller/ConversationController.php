<?php

namespace PiedWeb\ConversationBundle\Controller;

use PiedWeb\ConversationBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConversationController extends AbstractController
{
    private $translator;

    protected $form;

    /**
     * @var Conversation
     */
    protected $message;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return current form manager depending on `type` (request).
     */
    protected function getFormManager(string $type, Request $request)
    {
        if (null !== $this->form) {
            return $this->form;
        }

        $class = 'PiedWeb\\ConversationBundle\\Form\\'.ucfirst($type).'Form';
        // todo add a config alias => className
        if (!class_exists($class)) {
            throw new \Exception('`'.$type.'` does\'nt exist.');
        }

        return $this->form = new $class(
            $request,
            $this->get('doctrine'),
            $this->get('security.token_storage'),
            $this->get('form.factory'),
            $this->get('twig'),
            $this->get('router'),
            $this->get('translator')
        );
    }

    public function show(string $type, Request $request)
    {
        $response = new Response();

        $form = $this->getFormManager($type, $request)->getCurrentStep()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            return $response->setContent($this->getFormManager($type, $request)->validCurrentStep($form));
        }

        return $response->setContent($this->getFormManager($type, $request)->showForm($form));
    }

    /*
    protected function sendMessage(Contact $contact)
    {
        $message = (new \Swift_Message())
                ->setSubject($this->translator->trans('contact.send.prefix_subject').' '.$contact->getName())
                ->setFrom($this->container->getParameter('app.email.sender'))
                ->setReplyTo($contact->getFr0m())
                ->setTo($this->container->getParameter('app_contact_email'))
                ->setBody($this->renderView('@PiedWebContact/contact/sendmail.html.twig'
                , ['message' => $contact->getMessage()]), 'text/html');
        $this->get('mailer')->send($message);

        return;
    }/**/
}
