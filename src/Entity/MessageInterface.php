<?php

namespace PiedWeb\ConversationBundle\Entity;

interface MessageInterface
{
    public function getAuthorName();

    public function getAuthorEmail();

    public function getAuthorIp();

    public function getContent();
}
