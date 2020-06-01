<?php

namespace PiedWeb\ConversationBundle\Service;

use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use PiedWeb\CMSBundle\Service\LastTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Move it to a plugin (todo).
 */
class NewMessageMailNotifier
{
    private $mailer;
    private $emailTo;
    private $emailFrom;
    private $appName;
    private $rootDir;
    private $interval;
    private $em;
    private $translator;
    private $message;

    /**
     * Undocumented function.
     *
     * @param string $message Entity
     */
    public function __construct(
        string $message,
        MailerInterface $mailer,
        string $emailFrom,
        ?string $emailTo,
        string $appName,
        string $rootDir,
        string $interval, //minIntervalBetweenTwoNotification
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->mailer = $mailer;
        $this->emailTo = $emailTo;
        $this->emailFrom = $emailFrom;
        $this->interval = $interval;
        $this->appName = $appName;
        $this->rootDir = $rootDir;
        $this->em = $entityManager;
        $this->translator = $translator;
        $this->message = $message;
    }

    protected function getMessagesPostedSince($datetime)
    {
        $query = $this->em->createQuery(
            'SELECT m FROM '.$this->message.' m WHERE m.createdAt > :lastNotificationTime'
        )->setParameter('lastNotificationTime', $datetime);

        return $query->getResult();
    }

    public function send()
    {
        if (!$this->emailTo) {
            return;
        }

        $lastTime = new LastTime($this->rootDir.'/../var/lastNewMessageNotification');
        if (false === $lastTime->wasRunSince(new DateInterval($this->interval))) {
            return;
        }

        $messages = $this->getMessagesPostedSince($lastTime->get('15 minutes ago'));
        if (empty($messages)) {
            return;
        }

        $message = (new TemplatedEmail())
            ->subject(
                $this->translator->trans(
                    'admin.conversation.notification.title.'.(count($messages) > 1 ? 'plural' : 'singular'),
                    ['%appName%' => $this->appName]
                )
            )
            ->from($this->emailFrom)
            ->to($this->emailTo)
            ->htmlTemplate('@PiedWebConversation/notification.html.twig')
            ->context([
                'appName' => $this->appName,
                'messages' => $messages,
            ]);

        $lastTime->set();
        $this->mailer->send($message);

        return true;
    }
}
