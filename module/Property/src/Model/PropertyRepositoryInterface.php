<?php
namespace Property\Model;
use Laminas\Db\ResultSet\HydratingResultSet;

interface PropertyRepositoryInterface
{
    public function findAll(): HydratingResultSet;
}