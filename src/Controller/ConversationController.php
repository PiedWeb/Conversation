<?php

namespace PiedWeb\ConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    /**
     * @var ParameterBagInterface
     */
    protected $params;

    public function __construct(
        TranslatorInterface $translator,
        ParameterBagInterface $params
    ) {
        $this->translator = $translator;
        $this->params = $params;
    }

    protected function getFormClass($type)
    {
        $param = 'pwc.conversation.form'.($this->params->has('pwc.conversation.form.'.$type) ? '.'.$type : '_'.$type);

        if (!$this->params->has($param)) {
            throw new \Exception('`'.$type.'` does\'nt exist (not configured).');
        }

        $class = $this->params->get($param);
        if (!class_exists($class)) {
            throw new \Exception('`'.$type.'` does\'nt exist.');
        }

        return $class;
    }

    /**
     * Return current form manager depending on `type` (request).
     */
    protected function getFormManager(string $type, Request $request)
    {
        if (null !== $this->form) {
            return $this->form;
        }

        $class = $this->getFormClass($type);

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
}
