<?php

namespace PiedWeb\ConversationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConversationFormController extends AbstractController
{
    private $translator;

    protected $form;

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

    protected function initResponse($request): Response
    {
        $response = new Response();

        if ($this->params->has('pwc.static.domain')) {
            $possibleOrigins = array_merge(
                $this->params->has('pwc.conversation.possible_origins') ?
                    $this->params->get('pwc.conversation.possible_origins') : [],
                [
                    'https://'.$request->getHost(),
                    'https://'.$this->params->get('pwc.static.domain'),
                ]
            );

            if (in_array($request->headers->get('origin'), $possibleOrigins)) {
                $origin = $request->headers->get('origin');
            } else {
                $origin = 'https://'.$this->params->get('pwc.static.domain');
            }

            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token');
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        return $response;
    }

    public function show(string $type, Request $request)
    {
        $response = $this->initResponse($request);

        $form = $this->getFormManager($type, $request)->getCurrentStep()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            return $response->setContent($this->getFormManager($type, $request)->validCurrentStep($form));
        }

        return $response->setContent($this->getFormManager($type, $request)->showForm($form));
    }
}
