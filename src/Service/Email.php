<?php

namespace App\Service;

use App\Model;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\Dispatcher;
use PSX\CloudEvents\Builder;
use PSX\Framework\Config\ConfigInterface;
use PSX\Http\Exception\InternalServerErrorException;
use PSX\Http\Exception\NotImplementedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Texter;
use Symfony\Component\Notifier\Transport;
use Symfony\Component\Uid\Uuid;
use Throwable;

readonly class Email
{
    public function __construct(
        private Dispatcher $dispatcher,
        private ConfigInterface $config,
        private MessageBusInterface $messageBus,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function send(Model\EmailMessage $email, ContextInterface $context): Model\Message
    {
        $dsn = $this->config->get('email_dsn');
        if (empty($dsn)) {
            throw new NotImplementedException('Email channel was not configured');
        }

        $transport = Transport::fromDsn($dsn);

        try {
            $emailMessage = new Mime\Email();

            $from = $email->getFrom();
            if ($from !== null) {
                $emailMessage->from($from);
            }

            $to = $email->getTo();
            if ($to !== null) {
                $emailMessage->to(...$to);
            }

            $cc = $email->getCc();
            if ($cc !== null) {
                $emailMessage->cc(...$cc);
            }

            $bcc = $email->getBcc();
            if ($bcc !== null) {
                $emailMessage->bcc(...$bcc);
            }

            $replyTo = $email->getReplyTo();
            if ($replyTo !== null) {
                $emailMessage->replyTo(...$replyTo);
            }

            $priority = $email->getPriority();
            if ($priority !== null) {
                $emailMessage->priority($priority);
            }

            $subject = $email->getSubject();
            if ($subject !== null) {
                $emailMessage->subject($subject);
            }

            $text = $email->getText();
            if ($text !== null) {
                $emailMessage->text($text);
            }

            $html = $email->getHtml();
            if ($html !== null) {
                $emailMessage->html($html);
            }

            $texter = new Texter($transport, $this->messageBus, $this->eventDispatcher);
            $texter->send(new EmailMessage($emailMessage));

            $this->dispatchEvent('email.sent', $email);

            $message = new Model\Message();
            $message->setSuccess(true);
            $message->setMessage('Email successfully sent');
            return $message;
        } catch (Throwable $e) {
            throw new InternalServerErrorException('Could not send message, got: ' . $e->getMessage(), $e);
        }
    }

    private function dispatchEvent(string $type, Model\EmailMessage $model): void
    {
        $event = new Builder()
            ->withId(Uuid::v4())
            ->withSource('/email')
            ->withType($type)
            ->withDataContentType('application/json')
            ->withData($model)
            ->build();

        $this->dispatcher->dispatch($type, $event);
    }
}
