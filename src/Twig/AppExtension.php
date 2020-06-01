<?php

namespace PiedWeb\ConversationBundle\Twig;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment as Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $messageEntity;

    public function __construct(EntityManager $em, string $messageEntity)
    {
        $this->em = $em;
        $this->messageEntity = $messageEntity;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('listMessages', [$this, 'listMessages'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function listMessages(
        Twig $env,
        string $referring,
        string $orderBy = 'createdAt DESC',
        $limit = 0,
        string $template = '@PiedWebConversation/_messages_list.html.twig'
    ) {
        $messages = $this->em->getRepository($this->messageEntity)
            ->getMessagesPublishedByReferring($referring, $orderBy, $limit);

        return $env->render($template, ['messages' => $messages]);
    }
}
