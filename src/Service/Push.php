<?php

namespace App\Service;

use App\Model;
use App\Service\Mail\Validator;
use App\Table;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\Dispatcher;
use PSX\CloudEvents\Builder;
use PSX\Framework\Config\ConfigInterface;
use PSX\Http\Exception\BadRequestException;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Http\Exception\NotImplementedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Texter;
use Symfony\Component\Notifier\Transport;
use Symfony\Component\Uid\Uuid;
use Throwable;

readonly class Push
{
    public function __construct(
        private OptionsFactory $optionsFactory,
        private Dispatcher $dispatcher,
        private ConfigInterface $config,
        private MessageBusInterface $messageBus,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function send(Model\PushMessage $push, ContextInterface $context): Model\Message
    {
        $dsn = $this->config->get('sms_dsn');
        if (empty($dsn)) {
            throw new NotImplementedException('SMS channel was not configured');
        }

        try {
            $transport = Transport::fromDsn($dsn);

            $subject = $push->getSubject() ?? throw new BadRequestException('No subject provided');
            $content = $push->getContent() ?? throw new BadRequestException('No content provided');
            $options = $this->optionsFactory->factory($push->getOptions(), $transport);

            $texter = new Texter($transport, $this->messageBus, $this->eventDispatcher);
            $texter->send(new PushMessage($subject, $content, $options));

            $this->dispatchEvent('push.sent', $push);

            $message = new Model\Message();
            $message->setSuccess(true);
            $message->setMessage('Push successfully sent');
            return $message;
        } catch (Throwable $e) {
            throw new InternalServerErrorException('Could not send message, got: ' . $e->getMessage(), $e);
        }
    }

    private function dispatchEvent(string $type, Model\PushMessage $model): void
    {
        $event = new Builder()
            ->withId(Uuid::v4())
            ->withSource('/push')
            ->withType($type)
            ->withDataContentType('application/json')
            ->withData($model)
            ->build();

        $this->dispatcher->dispatch($type, $event);
    }
}
