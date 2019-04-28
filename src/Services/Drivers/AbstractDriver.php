<?php
namespace Amiriun\SMS\Services\Drivers;


use Amiriun\SMS\Contracts\DriverInterface;
use Amiriun\SMS\DataContracts\ReceiveSMSDTO;
use Amiriun\SMS\Repositories\StoreSMSDataRepository;

abstract class AbstractDriver implements DriverInterface
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