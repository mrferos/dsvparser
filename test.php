<?php
include __DIR__ . '/src/DSVParser.php';
include __DIR__ . '/src/DSVParser/Query.php';

$dsvParser = new DSVParser();
$dsvParser->setFileName(__DIR__ . '/data.csv');
$dsvParser->open();
$dsvParser->setFirstRowAsColumnHeaders(true);
$query = new DSVParser\Query($dsvParser);
$query->execute("
    select d.id, d.reference_id
    from `__DIR__/data.csv` as d
    limit 10,20
");