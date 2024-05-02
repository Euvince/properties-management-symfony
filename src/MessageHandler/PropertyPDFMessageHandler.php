<?php

namespace App\MessageHandler;

use App\Message\PropertyPDFMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PropertyPDFMessageHandler{
    public function __invoke(PropertyPDFMessage $message)
    {
        // do something with your message
    }
}
