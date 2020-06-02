<?php

namespace PiedWeb\ConversationBundle\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConversationFormController extends AbstractController
{
    private $translator;

    protected $form;

    /** @var array */
    protected $possibleOrigins = [];

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

    protected function getFormManagerClass($type)
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

        $class = $this->getFormManagerClass($type);

        return $this->form = new $class(
            $this->params->get('pwc.conversation.entity_message'),
            $request,
            $this->get('doctrine'),
            $this->get('security.token_storage'),
            $this->get('form.factory'),
            $this->get('twig'),
            $this->get('router'),
            $this->get('translator')
        );
    }

    protected function getPossibleOrigins(Request $request): array
    {
        if (!empty($this->possibleOrigins)) {
            return $this->possibleOrigins;
        }

        if ($this->params->has('pwc.conversation.possible_origins')) {
            $this->possibleOrigins = explode(' ', $this->params->get('pwc.conversation.possible_origins'));
        }

        $this->possibleOrigins[] = 'https://'.$request->getHost();
        $this->possibleOrigins[] = 'http://'.$request->getHost();
        // just for dev
        $this->possibleOrigins[] = 'http://'.$request->getHost().':8000';
        $this->possibleOrigins[] = 'http://'.$request->getHost().':8001';
        $this->possibleOrigins[] = 'http://'.$request->getHost().':8002';

        if ($this->params->has('pwc.static.domain')) {
            $this->possibleOrigins[] = 'https://'.$this->params->get('pwc.static.domain');
        }

        return $this->possibleOrigins;
    }

    protected function initResponse($request)
    {
        $response = new Response();

        if (!in_array($request->headers->get('origin'), $this->getPossibleOrigins($request))) {
            return;
        }

        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token');
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('origin'));

        return $response;
    }

    public function show(string $type, Request $request)
    {
        $response = $this->initResponse($request);
        if (null === $response) {
            throw new Exception('origin not verified');
        }

        $form = $this->getFormManager($type, $request)->getCurrentStep()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            return $response->setContent($this->getFormManager($type, $request)->validCurrentStep($form));
        }

        return $response->setContent($this->getFormManager($type, $request)->showForm($form));
    }
}
