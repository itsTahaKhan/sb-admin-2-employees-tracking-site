<?php
function jsuccess($msg) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'success', 'msg' => $msg]);
    exit;
}
function loginCheckAdmin($msg) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'admin', 'msg' => $msg]);
    exit;
}

function loginCheckEmp($msg) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'employee', 'msg' => $msg]);
    exit;
}

function jerror($msg) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'msg' => $msg]);
    exit;
}
function validationError($msg) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'validationError', 'msg' => $msg]);
    exit;
}