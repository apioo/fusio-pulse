<?php

namespace App\Service;

use PSX\Http\Exception\BadRequestException;
use PSX\Record\RecordInterface;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\DiscordTransport;
use Symfony\Component\Notifier\Bridge\Expo\ExpoOptions;
use Symfony\Component\Notifier\Bridge\Expo\ExpoTransport;
use Symfony\Component\Notifier\Bridge\GoogleChat\GoogleChatOptions;
use Symfony\Component\Notifier\Bridge\GoogleChat\GoogleChatTransport;
use Symfony\Component\Notifier\Bridge\MicrosoftTeams\MicrosoftTeamsOptions;
use Symfony\Component\Notifier\Bridge\MicrosoftTeams\MicrosoftTeamsTransport;
use Symfony\Component\Notifier\Bridge\Novu\NovuOptions;
use Symfony\Component\Notifier\Bridge\Novu\NovuTransport;
use Symfony\Component\Notifier\Bridge\OneSignal\OneSignalOptions;
use Symfony\Component\Notifier\Bridge\OneSignal\OneSignalTransport;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramTransport;
use Symfony\Component\Notifier\Bridge\Twilio\TwilioOptions;
use Symfony\Component\Notifier\Bridge\Twilio\TwilioTransport;
use Symfony\Component\Notifier\Message\MessageOptionsInterface;
use Symfony\Component\Notifier\Transport;

readonly class OptionsFactory
{
    public function factory(RecordInterface $options, Transport\TransportInterface $transport): ?MessageOptionsInterface
    {
        if ($transport instanceof TwilioTransport) {
            return new TwilioOptions($options->getAll());
        } elseif ($transport instanceof SlackTransport) {
            return new SlackOptions($options->getAll());
        } elseif ($transport instanceof OneSignalTransport) {
            return new OneSignalOptions($options->getAll());
        } elseif ($transport instanceof NovuTransport) {
            return new NovuOptions(options: $options->getAll());
        } elseif ($transport instanceof ExpoTransport) {
            $to = $options->get('to') ?? throw new BadRequestException('No "to" provided');
            return new ExpoOptions($to, options: $options->getAll());
        } elseif ($transport instanceof DiscordTransport) {
            return new DiscordOptions($options->getAll());
        } elseif ($transport instanceof MicrosoftTeamsTransport) {
            return new MicrosoftTeamsOptions($options->getAll());
        } elseif ($transport instanceof TelegramTransport) {
            return new TelegramOptions($options->getAll());
        } elseif ($transport instanceof GoogleChatTransport) {
            return new GoogleChatOptions($options->getAll());
        }

        return null;
    }
}
