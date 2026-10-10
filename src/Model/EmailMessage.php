<?php

declare(strict_types = 1);

namespace App\Model;


class EmailMessage implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $from = null;
    /**
     * @var array<string>|null
     */
    protected ?array $to = null;
    /**
     * @var array<string>|null
     */
    protected ?array $replyTo = null;
    /**
     * @var array<string>|null
     */
    protected ?array $cc = null;
    /**
     * @var array<string>|null
     */
    protected ?array $bcc = null;
    protected ?int $priority = null;
    protected ?string $subject = null;
    protected ?string $text = null;
    protected ?string $html = null;
    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }
    public function getFrom(): ?string
    {
        return $this->from;
    }
    /**
     * @param array<string>|null $to
     */
    public function setTo(?array $to): void
    {
        $this->to = $to;
    }
    /**
     * @return array<string>|null
     */
    public function getTo(): ?array
    {
        return $this->to;
    }
    /**
     * @param array<string>|null $replyTo
     */
    public function setReplyTo(?array $replyTo): void
    {
        $this->replyTo = $replyTo;
    }
    /**
     * @return array<string>|null
     */
    public function getReplyTo(): ?array
    {
        return $this->replyTo;
    }
    /**
     * @param array<string>|null $cc
     */
    public function setCc(?array $cc): void
    {
        $this->cc = $cc;
    }
    /**
     * @return array<string>|null
     */
    public function getCc(): ?array
    {
        return $this->cc;
    }
    /**
     * @param array<string>|null $bcc
     */
    public function setBcc(?array $bcc): void
    {
        $this->bcc = $bcc;
    }
    /**
     * @return array<string>|null
     */
    public function getBcc(): ?array
    {
        return $this->bcc;
    }
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }
    public function getPriority(): ?int
    {
        return $this->priority;
    }
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }
    public function getSubject(): ?string
    {
        return $this->subject;
    }
    public function setText(?string $text): void
    {
        $this->text = $text;
    }
    public function getText(): ?string
    {
        return $this->text;
    }
    public function setHtml(?string $html): void
    {
        $this->html = $html;
    }
    public function getHtml(): ?string
    {
        return $this->html;
    }
    /**
     * @return \PSX\Record\RecordInterface<mixed>
     */
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('from', $this->from);
        $record->put('to', $this->to);
        $record->put('replyTo', $this->replyTo);
        $record->put('cc', $this->cc);
        $record->put('bcc', $this->bcc);
        $record->put('priority', $this->priority);
        $record->put('subject', $this->subject);
        $record->put('text', $this->text);
        $record->put('html', $this->html);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

