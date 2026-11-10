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
        $consulta-execute(['id' => $id]);

        return $consulta->fetch() ?: null;
    }

    //FUNÇÃO DE CADASTRAR UM NOVO PROJETO
    public function criar(array $dados): int{
        $consulta = Database::conexao()->prepare(
            'INSERT INTO projetos_design (nome, cliente, responsavel, status, inicio, final) VALUES (:nome, :cliente, :responsavel, :status, :inicio, :final)'
            );
        $consulta-execute([
        'nome' => trim($dados['nome']),
        'cliente' => trim($dados['cliente']),
        'responsavel' => trim($dados['responsavel']),
        'status' => trim($dados['status']),
        'inicio' => $inicio
        'final' => $final
        ]);

       return (int) Database::conexao()->lastInsertId();
    }
    
    //FUNÇÃO DE DELETAR UM PROJETO POR ID
    public function del(int $id): ?array{
        $consulta = Database::conexao()->prepare('DELETE * FROM projetos_design WHERE id = :id');
        $consulta-execute(['id' => $id]);

        return $consulta->fetch() ?: null;
    }


}