<?php
$config->bug->list->defaultFields = $config->bug->list->defaultFields . ',take_time';
$config->bug->datatable->defaultField[]= 'take_time';

global $lang;
$config->bug->datatable->fieldList['take_time']['title']    = $lang->bug->take_time;
$config->bug->datatable->fieldList['take_time']['fixed']    = 'no';
$config->bug->datatable->fieldList['take_time']['width']    = '120';
$config->bug->datatable->fieldList['take_time']['required'] = 'no';