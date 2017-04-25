<?php

namespace app\NL\database;

interface IAdapter
{
    public function createConnection();
//    public function execute($query, $bulk, $concern);
}