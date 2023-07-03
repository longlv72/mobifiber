<?php

namespace App\Facads;


use Illuminate\Support\Facades\Facade;

class ODB extends Facade {
    protected static function getFacadeAccessor() { return 'odb'; }
    const INTEGER = \PDO::PARAM_INT;
    const STRING = \PDO::PARAM_STR;
    const CHAR = \PDO::PARAM_STR_CHAR;
    const BOOLEAN = \PDO::PARAM_BOOL;
    const LOB = \PDO::PARAM_LOB;
    const CURSOR = \PDO::PARAM_STMT;
}
