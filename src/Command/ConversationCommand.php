<?php
/**
 * Command to send report (best use with cron).
 */

namespace PiedWeb\ConversationBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment as Twig;

class ConversationCommand extends Command
{
    /**
     * @var ParameterBagInterface
     */
    protected $params;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var MailerInterface
     */
    protected $mailer;

    public function __construct(
        EntityManagerInterface $em,
        Twig $twig,
        ParameterBagInterface $params,
        MailerInterface $mailer
    ) {
        $this->em = $em;
        $this->twig = $twig;
        $this->params = $params;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setName('conversation:report:send')
            ->setDescription('Send a report from the last conversation')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // getTodayConversation (h-24)
        // if new then send notif
        // else nothing
    }

    /*
    protected function sendMessage()
    {
        $message = (new Email())
            ->subject(
                //'$this->translator->trans('contact.send.prefix_subject').' '.
                $conversation->getAuthorName().': '.substr($conversation->getMessage(), 50)
            )
            ->from($this->params->get('app.email.sender'))
            ->replyTo($conversation->getAuthorEmail())
            ->to($this->params->get('app_contact_email'))
            ->htmlTemplate('@PiedWebConversation/sendmail.html.twig')
            ->context(['message' => $conversation->getMessage()]);
        $this->mailer->send($message);
    }/**/
}
