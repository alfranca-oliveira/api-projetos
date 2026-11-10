<?php

class Resposta{
    public static function json(mixed $dados, int $status = 200): never{
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
        exit;
    }
}