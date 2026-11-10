<?php

require_once __DIR__.'/../src/Database.php';
require_once __DIR__.'/../src/Reposta.php';
require_once __DIR__.'/../src/ProjetosRepository.php';
require_once __DIR__.'/../src/ProjetosController.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$controller = new ProjetosController();

try {
    //ROTA LISTAR TODOS PROJETOS
    if ($metodo === 'GET' && $caminho === '/projetos') {
        $controller->listar();
    }
    //ROTA DE ADD NOVO PROJETO
    if ($metodo === 'POST' && $caminho === '/projetos') {
        $controller->criar();
    }
    //ROTA DE PEGAR PROJETO PELO ID
    if ($metodo === 'GET' && preg_match('#^/projetos/(\d+)$#', $caminho, $partes)) {
        $controller->buscar((int) $partes[1]);
    }

    Resposta::json(['erro' => 'Rota não encontrada.'], 404);

} catch (Throwable $erro) {
    error_log($erro->getMessage());
    Resposta::json(['erro' => 'Erro interno no servidor.'], 500);
}