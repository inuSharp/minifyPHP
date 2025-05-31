<?php

function getDBConfig() {
    $config = getConfig();
    $dbConfig = array_filter($config, function ($key) {
        return str_starts_with($key, 'db.');
    }, ARRAY_FILTER_USE_KEY);
    return $dbConfig;
}

function getDBList() {
    $dbConfig = getDBConfig();
    $names = [];
    foreach ($dbConfig as $key => $value) {
        $names[] = str_replace('db.', '', $key);
    }
    return $names;
}

function connectMysql() {
    $dbName = request('d', '');
    $dbKey = 'db.' . $dbName;
    $dbConfig = getDBConfig();
    if (!array_key_exists($dbKey, $dbConfig)) {
        return 'not found db config';
    }
    $config = $dbConfig[$dbKey];
    dbConnect([
        'host' => $config['host'],
        'dbname' => $config['dbname'],
        'port' => $config['port'],
        'user' => $config['user'],
        'password' => $config['password'],
    ]);
}

function getDBTableNames() {
    connectMysql();
    return array_column(select('show tables'), 'Tables_in_testdb');
}

function getTableDefine() {
    connectMysql();
    $tableName = request('t', '');
    return select('show create table ' . $tableName)[0]['Create Table'];
}
