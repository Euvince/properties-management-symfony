<?php

namespace App\Events;

use App\DTO\ContactDTO;

class ContactRequestEvent
{
    function __construct(
        private ContactDTO $data
    )
    {
    }

    function getData () : ?ContactDTO {
        return $this->data;
    }

    function setData (?ContactDTO $data) : ?static {
        $this->data = $data;

        return $this;
    }

}