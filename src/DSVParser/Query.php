<?php
namespace DSVParser;

require_once __DIR__ . '/Query/PHPSQLParser.php';

class Query
{
    /** @var  \DSVParser */
    protected $_dsvParser;

    public function __construct(\DSVParser $parser)
    {
        $this->_dsvParser = $parser;
    }

    public function execute($query)
    {
        $query = new \PHPSQLParser($query);
        var_dump($query->parsed); die;
    }
}