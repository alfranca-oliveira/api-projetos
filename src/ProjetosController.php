<?php

class ProjetosController{

    private ProjetosRepository $repositorio;
    private const STATUS_VALIDOS = ['andamento', 'pendente', 'aprovacao', 'ajuste', 'aprovado', 'finalizado'];

    public function __construct(){
        $this->repositorio = new ProjetosRepository();
    }
    //RETORNA TODA A LISTA DE PROJETOS EXISTENTE
    public function listar(): void{
        Resposta::json(['dados' => $this->repositorio->listar()]);
    }

    //RETORNA A BUSCA DO PROJETO POR ID
    public function buscar(int $id): void{
        $projeto = $this->repositorio->buscar($id);

        if($projeto === null){
            Resposta::json(['erro' => 'Projeto não encontrado.'], 404);
        }

        Resposta::json(['dados' => $projeto]);
    }

    //RETORNA O CADASTRO DE UM NOVO PROJETO
    public function criar(): void{
        $dados = json_decode(file_get_contents('php://input'), true);

        if (!is_array($dados)){
            Resposta::json(['erro' => 'Envie um JSON válido.'], 400);
        }

        //CHAMA A FUNÇÃO PARA VALIDAR OS CAMPOS ANTES DE CADASTRAR
        $erros = $this->validar($dados);

        if ($erros) {
            Resposta::json(['erros' => $erros], 422);
        }

        $id = $this->repositorio->criar($dados);

        Resposta::json(['dados' => $this->repositorio->buscar($id)], 201);
    }

    //FUNÇÃO DE VALIDAR OS CAMPOS
    private function validar(array $dados): array{
        
    $erros = [];

        foreach (['nome', 'cliente', 'responsavel'] as $campo) {
            $valor = $dados[$campo] ?? null;

            if (!is_string($valor) || trim($valor) === '') {
                $erros[$campo] = 'Campo obrigatório.';
            } elseif (mb_strlen($valor) > 100) {
                $erros[$campo] = 'Máximo de 100 caracteres.';
            }
        }

        //VALIDA FORMATO DATA E SE A DO FIM É MAIOR QUE A DO INICIO
            if (isset($dados['inicio']) && !$this->dataValida($dados['inicio'])) {
                $erros['inicio'] = 'Use uma data válida no formato AAAA-MM-DD.';
            }

            if (isset($dados['final']) && !$this->dataValida($dados['final'])) {
                $erros['final'] = 'Use uma data válida no formato AAAA-MM-DD.';
            }

            if (!isset($erros['inicio']) && !isset($erros['final'])
            && isset($dados['inicio'], $dados['final']) && $dados['final'] < $dados['inicio']) {
                $erros['final'] = 'A data final não pode ser anterior à data de início.';
            }

            //ACEITA SÓ OS STATUS VALIDOS
            if (isset($dados['status']) && !in_array($dados['status'], self::STATUS_VALIDOS, true)) {
                $erros['status'] = 'Status inválido. Use: ' . implode(', ', self::STATUS_VALIDOS) . '.';
            }

        return $erros;
    }

    //FORMATO DA DATA
    private function dataValida(mixed $data): bool{
        if (!is_string($data) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            return false;
        }

        [$ano, $mes, $dia] = explode('-', $data);

        return checkdate((int) $mes, (int) $dia, (int) $ano);
    }
}