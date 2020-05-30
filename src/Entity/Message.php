<?php

namespace PiedWeb\ConversationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PiedWeb\CMSBundle\Entity\IdTrait;
use PiedWeb\CMSBundle\Entity\TimestampableTrait;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Message
{
    use IdTrait;
    use TimestampableTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=180)
     */
    protected $authorName = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=180)
     */
    protected $authorEmail = '';

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $authorIp;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 200000,
     *      minMessage = "conversation.message.short",
     *      maxMessage = "conversation.message.long"
     * )
     */
    protected $message;

    /**
     * Identifier referring (most of time, URI).
     *
     * @ORM\Column(type="string", length=180)
     *
     * @var string
     */
    protected $referring;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $publishedAt;

    public function __construct()
    {
        $this->updatedAt = null !== $this->updatedAt ? $this->updatedAt : new \DateTime();
        $this->createdAt = null !== $this->createdAt ? $this->createdAt : new \DateTime();
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Set message.
     *
     * @param string|null
     *
     * @return ContactForm
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the value of authorName.
     *
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * Set the value of authorName.
     *
     * @return self
     */
    public function setAuthorName(string $authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * Get the value of authorEmail.
     *
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * Set the value of authorEmail.
     *
     * @return self
     */
    public function setAuthorEmail(string $authorEmail)
    {
        $this->authorEmail = $authorEmail;

        return $this;
    }

    /**
     * Get identifier referring.
     *
     * @return string
     */
    public function getReferring()
    {
        return $this->referring;
    }

    /**
     * Set identifier referring.
     *
     * @param string $from Identifier referring
     *
     * @return self
     */
    public function setReferring(string $referring)
    {
        $this->referring = $referring;

        return $this;
    }

    /**
     * Get the value of authorIp.
     *
     * @return int
     */
    public function getAuthorIp()
    {
        return $this->authorIp;
    }

    /**
     * Set the value of authorIp.
     *
     * @return self
     */
    public function setAuthorIp(int $authorIp)
    {
        $this->authorIp = $authorIp;

        return $this;
    }

    public function setAuthorIpRaw(string $authorIp)
    {
        return $this->setAuthorIp(ip2long(IPUtils::anonymize($authorIp)));
    }

    public function getAuthorIpRaw()
    {
        return long2ip($this->getAuthorIp());
    }
}
