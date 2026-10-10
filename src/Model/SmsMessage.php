<?php

declare(strict_types = 1);

namespace App\Model;


class SmsMessage implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $phone = null;
    protected ?string $subject = null;
    protected ?string $from = null;
    /**
     * @var \PSX\Record\Record<string>|null
     */
    protected ?\PSX\Record\Record $options = null;
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }
    public function getSubject(): ?string
    {
        return $this->subject;
    }
    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }
    public function getFrom(): ?string
    {
        return $this->from;
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
        $record->put('phone', $this->phone);
        $record->put('subject', $this->subject);
        $record->put('from', $this->from);
        $record->put('options', $this->options);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

