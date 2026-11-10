<?php

class ProjetosRepository{

    //FUNÇÃO DE LISTAR PROJETOS
    public function listar(): array{
        return Database::conexao()
            ->query('SELECT * FROM projetos_design ORDER BY id DESC')
            ->fetchAll();
    }

    //FUNÇÃO DE BUSCA POR ID DO PROJETO
    public function buscar(int $id): ?array{
        $consulta = Database::conexao()->prepare('SELECT * FROM projetos_design WHERE id = :id');
        $consulta->execute(['id' => $id]);

        return $consulta->fetch() ?: null;
    }

    //FUNÇÃO DE CADASTRAR UM NOVO PROJETO
    public function criar(array $dados): int{
        $consulta = Database::conexao()->prepare(
            'INSERT INTO projetos_design (nome, cliente, responsavel, status, inicio, final) VALUES (:nome, :cliente, :responsavel, :status, :inicio, :final)'
            );
        $consulta->execute([
        'nome' => trim($dados['nome']),
        'cliente' => trim($dados['cliente']),
        'responsavel' => trim($dados['responsavel']),
        'status' => $dados['status'] ?? 'pendente',
        'inicio' => $dados['inicio'] ?? null,
        'final' => $dados['final'] ?? null
        ]);

       return (int) Database::conexao()->lastInsertId();
    }


}