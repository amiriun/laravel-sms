<?php
namespace Amiriun\SMS\Services\Connectors;


use Amiriun\SMS\Contracts\SMSConnectorInterface;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;

abstract class AbstractConnector implements SMSConnectorInterface
{
    /**
     * @var StoreSMSDataRepository
     */
    protected $repository;

    /**
     * @param ReceiveSMSDTO $DTO
     *
     * @throws \Exception
     */
    public function receive(ReceiveSMSDTO $DTO)
    {
        $this->repository->storeReceiveSMSLog($DTO);
    }

    public function getConnectorName()
    {
        $getConnectorClassName = class_basename($this);
        $removeConnectorFromClassName = str_replace('Connector','',$getConnectorClassName);

        return strtolower($removeConnectorFromClassName);
    }
}