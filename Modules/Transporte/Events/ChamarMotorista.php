<?php

namespace Modules\Transporte\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Modules\Core\Models\User;

class ChamarMotorista implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    private $uuid;
    /**
     * @var
     */
    public $chamada;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $uuid, $chamada)
    {
        $this->uuid = $uuid;
        $this->chamada = $chamada;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return  ['chamada.motorista'];
    }
}

