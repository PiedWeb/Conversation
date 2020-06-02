<?php

namespace PiedWeb\ConversationBundle\Form;

use Doctrine\Bundle\DoctrineBundle\Registry;
use PiedWeb\ConversationBundle\Entity\MessageInterface as Message;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as Twig;

trait FormTrait
{
    /**
     * Permit to convert integer step to text string for a better readability.
     *
     * @var array
     */
    protected static $step = [
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
    ];

    /** @var string */
    protected $successMessage = 'conversation.send.success';

    /** @var Request */
    protected $request;

    /** @var \Doctrine\Bundle\DoctrineBundle\Registry */
    protected $doctrine;

    /** @var Twig */
    protected $twig;

    /** @var TokenStorageInterface */
    protected $security;

    /** @var FormFactory */
    protected $formFactory;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var Router */
    protected $router;

    /** @var int */
    protected $currentStep;

    /** @var int */
    protected $messageId;

    /** @var string */
    protected $messageEntity;

    /** @var Message */
    protected $message;

    public function __construct(
        string $messageEntity,
        Request $request,
        Registry $doctrine,
        TokenStorageInterface $security,
        FormFactory $formFactory,
        Twig $twig,
        Router $router,
        TranslatorInterface $translator
    ) {
        $this->request = $request;
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->router = $router;
        $this->translator = $translator;
        $this->messageEntity = $messageEntity;
    }

    /**
     * Initiate Message Entity (or load data from previous message)
     * and return form builder instance.
     */
    protected function initForm(): FormBuilderInterface
    {
        if (1 === $this->getStep()) {
            $this->message = new $this->messageEntity(); // todo, permit to configure it
            $this->message->setAuthorIpRaw($this->request->getClientIp() ?? '');
            $this->message->setReferring($this->getReferring());
        } else {
            $this->message = $this->doctrine->getRepository($this->messageEntity)->find($this->getId());
            if (!$this->message) {
                throw new NotFoundHttpException('An error occured during the validation ('.$this->getId().')');
            }
            // add a security check ? Comparing current IP and previous one
            // IPUtils::checkIp($request->getClientIp()), $this->message->getAuthorIpRaw())
            // sinon, passer l'id dans la session plutôt que dans la requête
        }

        $form = $this->formFactory->createBuilder(FormType::class, $this->message, ['csrf_protection' => false]);

        $form->setAction($this->router->generate('piedweb_cms_conversation', [
            'type' => $this->getType(),
            'referring' => $this->getReferring(),
            'id' => $this->getId(),
            'step' => $this->getStep(),
        ], UrlGeneratorInterface::ABSOLUTE_URL));

        return $form;
    }

    public function getCurrentStep(): FormBuilderInterface
    {
        $currentStepMethod = 'getStep'.self::$step[$this->getStep()];

        return $this->$currentStepMethod();
    }

    abstract protected function getStepOne(): FormBuilderInterface;

    /**
     * Return rendered response (success or error).
     */
    public function validCurrentStep($form): string
    {
        $currentStepMethod = 'valid'.self::$step[$this->getStep()];
        if (method_exists($this, $currentStepMethod)) {
            return  $this->$currentStepMethod($form);
        }

        return $this->defaultStepValidator($form);
    }

    protected function validStepOne($form)
    {
        return $this->defaultStepValidator($form);
    }

    protected function defaultStepValidator($form): string
    {
        if ($form->isValid()) {
            $this->sanitizeConversation();
            $this->getDoctrine()->getManager()->persist($this->message);
            $this->getDoctrine()->getManager()->flush();
            $this->messageId = $this->message->getId();

            if (false !== $this->getNextStepFunctionName()) {
                $this->incrementStep();

                return $this->showForm($this->getCurrentStep()->getForm());
                /*
                return $this->redirectToRoute('piedweb_cms_conversation', [
                    'type' => $this->getType(),
                    'id' => $this->message->getId(),
                    'step' => $this->getStep() + 1,
                ]);*/
            }
            //$form = $form->createView();

            return $this->showSuccess();
        }

        // return the form with errors highlighted
        return $this->showForm($form);
    }

    public function getShowFormTemplate()
    {
        $template = '@PiedWebConversation/_'
            .$this->getType().$this->getReferring().'Step'.$this->getStep().'.html.twig';

        if (!$this->twig->getLoader()->exists($template)) {
            $template = '@PiedWebConversation/_'.$this->getType().'Step'.$this->getStep().'.html.twig';
        }

        if (!$this->twig->getLoader()->exists($template)) {
            $template = '@PiedWebConversation/_conversation.html.twig';
        }

        return $template;
    }

    public function showForm($form): string
    {
        return $this->twig->render($this->getShowFormTemplate(), [
            'conversation' => $form->createView(),
        ]);
    }

    protected function showSuccess(): string
    {
        return $this->twig->render('@PiedWebConversation/_alert.html.twig', [
            'message' => $this->translator->trans($this->successMessage),
            'context' => 'success',
        ]);
    }

    protected function getNextStepFunctionName()
    {
        $getFormMethod = 'getStep'.self::$step[$this->getNextStep()];
        if (!method_exists($this, $getFormMethod)) {
            return false;
        }

        return $getFormMethod;
    }

    protected function getNextStep()
    {
        return $this->getStep() + 1;
    }

    protected function sanitizeConversation()
    {
        $this->message->setContent(
            htmlentities($this->message->getContent())
        );
    }

    protected function getStep()
    {
        if (null !== $this->currentStep) {
            return $this->currentStep;
        }

        return $this->currentStep = $this->request->query->getInt('step', 1);
    }

    protected function incrementStep()
    {
        //$this->request->set('step', $this->getStep()+1)
        ++$this->currentStep;
    }

    protected function getId()
    {
        return $this->messageId ?? $this->request->query->getInt('id', 0);
    }

    protected function getReferring()
    {
        return $this->request->get('referring');
    }

    protected function getType()
    {
        return $this->request->get('type');
    }

    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    protected function getAuthorNameConstraints()
    {
        return [
            new NotBlank(),
            new Length([
                'max' => 100,
                'maxMessage' => 'conversation.name.long',
            ]),
        ];
    }

    protected function getAuthorEmailConstraints()
    {
        return [
            new NotBlank(),
            new Email([
                'message' => 'user.email.invalid',
                'checkMX' => true,
            ]),
        ];
    }
}
