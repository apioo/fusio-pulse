<?php

namespace App\Service;

use App\Model;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\Dispatcher;
use PSX\CloudEvents\Builder;
use PSX\Framework\Config\ConfigInterface;
use PSX\Http\Exception\BadRequestException;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Http\Exception\NotImplementedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Chatter;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Transport;
use Symfony\Component\Uid\Uuid;
use Throwable;

readonly class Chat
{
    public function __construct(
        private OptionsFactory $optionsFactory,
        private Dispatcher $dispatcher,
        private ConfigInterface $config,
        private MessageBusInterface $messageBus,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function send(Model\ChatMessage $chat, ContextInterface $context): Model\Message
    {
        $dsn = $this->config->get('chat_dsn');
        if (empty($dsn)) {
            throw new NotImplementedException('Chat channel was not configured');
        }

        try {
            $transport = Transport::fromDsn($dsn);

            $subject = $chat->getSubject() ?? throw new BadRequestException('No subject provided');
            $options = $this->optionsFactory->factory($chat->getOptions(), $transport);

            $texter = new Chatter($transport, $this->messageBus, $this->eventDispatcher);
            $texter->send(new ChatMessage($subject, $options));

            $this->dispatchEvent('chat.sent', $chat);

            $message = new Model\Message();
            $message->setSuccess(true);
            $message->setMessage('Chat successfully sent');
            return $message;
        } catch (Throwable $e) {
            throw new InternalServerErrorException('Could not send message, got: ' . $e->getMessage(), $e);
        }
    }

    private function dispatchEvent(string $type, Model\ChatMessage $model): void
    {
        $event = new Builder()
            ->withId(Uuid::v4())
            ->withSource('/chat')
            ->withType($type)
            ->withDataContentType('application/json')
            ->withData($model)
            ->build();

        $this->dispatcher->dispatch($type, $event);
    }
}
