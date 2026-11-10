CREATE TABLE IF NOT EXISTS projetos_design (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    cliente TEXT NOT NULL,
    responsavel VARCHAR(100) NOT NULL,
    status TEXT NOT NULL DEFAULT 'pendente'
    CHECK (status in ('andamento', 'pendente', 'aprovacao', 'ajuste', 'aprovado', 'finalizado')),
    inicio DATE NULL,
    final DATE NULL,
    criado_em TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP 
)