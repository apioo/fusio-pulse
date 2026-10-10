<?php

declare(strict_types = 1);

namespace App\Model;


class ChatMessage implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $subject = null;
    /**
     * @var \PSX\Record\Record<string>|null
     */
    protected ?\PSX\Record\Record $options = null;
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }
    public function getSubject(): ?string
    {
        return $this->subject;
    }
    /**
     * @param \PSX\Record\Record<string>|null $options
     */
    public function setOptions(?\PSX\Record\Record $options): void
    {
        $this->options = $options;
    }
    /**
     * @return \PSX\Record\Record<string>|null
     */
    public function getOptions(): ?\PSX\Record\Record
    {
        return $this->options;
    }
    /**
     * @return \PSX\Record\RecordInterface<mixed>
     */
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('subject', $this->subject);
        $record->put('options', $this->options);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

