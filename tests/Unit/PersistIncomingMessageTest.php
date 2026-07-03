<?php

namespace Kstmostofa\LaravelWhatsApp\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Kstmostofa\LaravelWhatsApp\Events\Web\MessageReceived;
use Kstmostofa\LaravelWhatsApp\Listeners\PersistIncomingMessage;
use Kstmostofa\LaravelWhatsApp\Models\WaMessage;
use Kstmostofa\LaravelWhatsApp\Tests\TestCase;

class PersistIncomingMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_outbound_web_messages_under_the_recipient_chat_id(): void
    {
        (new PersistIncomingMessage())->handle(new MessageReceived('main', [
            'message' => [
                'id' => 'outbound-message-id',
                'from' => 'me@c.us',
                'to' => '126336580497657@lid',
                'body' => 'Hello',
                'type' => 'chat',
                'timestamp' => now()->timestamp,
                'fromMe' => true,
                'ack' => 1,
            ],
        ]));

        $message = WaMessage::where('wa_message_id', 'outbound-message-id')->firstOrFail();

        $this->assertSame('126336580497657@lid', $message->chat_id);
        $this->assertSame('outbound', $message->direction);
        $this->assertSame(1, $message->ack);
    }
}

