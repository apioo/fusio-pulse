<?php

namespace App\Action;

use App\Service;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

readonly class Sms implements ActionInterface
{
    public function __construct(private Service\Sms $service)
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): mixed
    {
        return $this->service->send(
            $request->getPayload(),
            $context
        );
    }
}
