<?php

namespace ArtisanSDK\Server\Messages;

use ArtisanSDK\Server\Commands\RegisterConnection;
use ArtisanSDK\Server\Contracts\ClientMessage;
use ArtisanSDK\Server\Contracts\Connection;
use ArtisanSDK\Server\Contracts\SelfHandling;
use ArtisanSDK\Server\Entities\Message;
use ArtisanSDK\Server\Traits\NoProtection;

abstract class JoinAsType extends Message implements ClientMessage, SelfHandling
{
    use NoProtection;

    /**
     * Save the message arguments for later when the message is handled.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        parent::__construct($arguments);
        $this->email = array_get($arguments, 'registration.email', 'Not Available');
        $this->first_name = array_get($arguments, 'registration.name.first');
        $this->last_name = array_get($arguments, 'registration.name.last');
        $this->type = Connection::ANONYMOUS;
    }

    /**
     * Handle the message.
     */
    public function handle()
    {
        $connection = array_filter($this->attributes);
        $connection['uuid'] = $this->client()->uuid();
        $command = new RegisterConnection(compact('connection'));

        return $this->dispatcher()->run($command);
    }
}
