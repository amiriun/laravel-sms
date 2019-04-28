<?php
namespace Amiriun\SMS\Repositories\Storage;


use Amiriun\SMS\Contracts\StorageInterface;

class MysqlStorage implements StorageInterface
{
    private $db;

    public function __construct()
    {
        $this->db = \DB::table(config('sms.logging.table_name'));
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function insert(array $data)
    {
        return $this->db->insert($data);
    }
}