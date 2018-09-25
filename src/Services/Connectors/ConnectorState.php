<?php
namespace Amiriun\SMS\Services\Connectors;


class ConnectorState
{
    const QUEUED = 'queued';
    const SCHEDULED = 'scheduled';
    const SENT = 'sent';
    const FAILED = 'failed';
    const DELIVERED = 'delivered';
    const UNDELIVERED = 'undelivered';
    const CANCELED = 'canceled';
    const BLOCKED = 'blocked';
    const INVALID = 'invalid';
    const AUTH_PROBLEM = 'auth_problem';
}