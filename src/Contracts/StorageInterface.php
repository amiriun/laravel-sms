<?php
namespace Amiriun\SMS\Contracts;


interface StorageInterface
{
    public function insert(array $data);
}