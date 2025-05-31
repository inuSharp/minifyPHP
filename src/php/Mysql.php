<?php

function dbConnect($connection=null) { static $db; if (is_null($db)) { if (is_null($connection)) { throw new \Exception("db is not connected"); } $db   = new PDO( 'mysql:host='.$connection['host'].';port='.$connection['port'].';dbname='.$connection['dbname'], $connection['user'], $connection['password'], [PDO::ATTR_PERSISTENT => false, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'] ); } return $db; }
function execSQL($sql) { $db = dbConnect(); $stmt = $db->prepare($sql); if (!$stmt) { $error = $db->errorInfo(); $errorMessage = implode(',', $error); throw new \Exception($errorMessage); } $success = $stmt->execute(); if (!$success) { $error = "sql execute error"; throw new Exception($error); } return $stmt; }
function select($sql) { $stmt = execSQL($sql); $result = $stmt->fetchAll(); $ret = []; $rowIndex = -1; foreach ($result as $row) { $rowIndex++; $ret[$rowIndex] = []; foreach ($row as $colName => $colValue) { if (is_numeric($colName)) { continue; } $ret[$rowIndex][$colName] = $colValue; } } return $ret; }
function transactionStart() { $db = dbConnect(); $db->beginTransaction(); }
function commit() { $db = dbConnect(); $db->commit(); }
function rollback() { $db = dbConnect(); $db->rollBack(); }

function getTableColumns($table) {
    $columns = [];
    $sql = "SHOW COLUMNS FROM {$table} FROM ". config('MYSQL_DB_NAME', 'required');
    $selected = select($sql);
    foreach ($selected as $row) {
        $columns[] = $row['Field'];
    }
    return $columns;
}
