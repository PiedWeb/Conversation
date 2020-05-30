<?php

namespace PiedWeb\ConversationBundle;

use PiedWeb\ConversationBundle\DependencyInjection\PiedWebConversationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PiedWebConversationBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new PiedWebConversationExtension();
        }

        return $this->extension;
    }
}
