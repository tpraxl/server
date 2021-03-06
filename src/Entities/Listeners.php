<?php

namespace ArtisanSDK\Server\Entities;

use ArtisanSDK\Server\Contracts\Command;
use ArtisanSDK\Server\Contracts\Listener;
use ArtisanSDK\Server\Contracts\Message;
use Illuminate\Support\Collection;

class Listeners extends Collection
{
    /**
     * Add a listener to the collection.
     *
     * @param ArtisanSDK\Server\Contracts\Listener $listener
     *
     * @return self
     */
    public function add(Listener $listener)
    {
        $this->push($listener);

        return $this;
    }

    /**
     * Remove a listener from the collection.
     *
     * @param ArtisanSDK\Server\Contracts\Listener $listener
     *
     * @return self
     */
    public function remove(Listener $listener)
    {
        $index = array_search($listener, $this->items, $strict = true);
        if ($index === false) {
            $this->offsetUnset($index);
        }

        return $this;
    }

    /**
     * Filter collection of listeners to those listening for the message.
     *
     * @param \ArtisanSDK\Server\Contracts\Message $message
     *
     * @return self
     */
    public function forMessage(Message $message)
    {
        return $this->filter(function ($listener) use ($message) {
            return $listener->commands($message)->count();
        });
    }

    /**
     * Filter collection of listeners to those with the command handler.
     *
     * @param \ArtisanSDK\Server\Contracts\Command $command
     *
     * @return self
     */
    public function forCommand(Command $command)
    {
        return $this->filter(function ($listener) use ($command) {
            return $listener->messages($command)->count();
        });
    }

    /**
     * Pass the message through all of the listeners.
     *
     * @param \ArtisanSDK\Server\Contracts\Message $message
     *
     * @return self
     */
    public function handle(Message $message)
    {
        return $this->forMessage($message)
            ->each(function ($listener) use ($message) {
                $listener->handle($message);
            });
    }
}
