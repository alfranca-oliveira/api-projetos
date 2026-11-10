# API de Projetos

API REST para cadastro e consulta de projetos de design, feita em PHP puro com banco de dados relacional (SQLite).

## Tecnologias

- PHP 8.1 ou superior
- SQLite 3 (pode ser trocado por PostgreSQL, veja abaixo)
- PDO para acesso ao banco

## Pré-requisitos

No Ubuntu (ou WSL):

```bash
sudo apt install -y php-cli php-sqlite3 php-mbstring sqlite3
```

## Como executar

1. Clone o repositório e entre na pasta:

```bash
git clone https://github.com/alfranca-oliveira/api-projetos.git
cd api-projetos
```

2. Crie o arquivo de configuração a partir do modelo:

```bash
cp .env.example .env
```

3. Crie o banco de dados:

```bash
sqlite3 database/projetos.sqlite < database/schema.sql
```

4. (Opcional) Adicione projetos de exemplo:

```bash
sqlite3 database/projetos.sqlite < database/seed.sql
```

5. Inicie o servidor, sempre a partir da pasta do projeto:

```bash
php -S localhost:8080 -t public public/index.php
```

A API estará disponível em `http://localhost:8080`.

## Endpoints

Os exemplos usam `curl -i`, que mostra o código de status na primeira linha da resposta (por exemplo, `HTTP/1.1 201 Created`). Sem o `-i`, aparece só o JSON.

### Cadastrar um projeto

`POST /projetos`

```bash
curl -i -X POST http://localhost:8080/projetos \
  -H "Content-Type: application/json" \
  -d '{"nome":"Landing page","cliente":"Estúdio Norte","responsavel":"Bruno Lima","status":"andamento","inicio":"2026-11-10","final":"2026-11-28"}'
```

Resposta `201 Created`:

```json
{
  "dados": {
    "id": 5,
    "nome": "Landing page",
    "cliente": "Estúdio Norte",
    "responsavel": "Bruno Lima",
    "status": "andamento",
    "inicio": "2026-11-10",
    "final": "2026-11-28",
    "criado_em": "2026-11-10 18:17:56"
  }
}
```

Só `nome`, `cliente` e `responsavel` são obrigatórios. Se o `status` não for enviado, o projeto é criado como `pendente`.

### Listar projetos

`GET /projetos`

```bash
curl -i http://localhost:8080/projetos
```

Resposta `200 OK` com a lista em `dados`, do mais recente para o mais antigo.

### Buscar um projeto por ID

`GET /projetos/{id}`

```bash
curl -i http://localhost:8080/projetos/2
```

Resposta `200 OK` com o projeto, ou `404 Not Found` se o ID não existir.

## Validações

| Campo | Regra |
| --- | --- |
| nome | Obrigatório, texto de até 100 caracteres |
| cliente | Obrigatório, texto de até 100 caracteres |
| responsavel | Obrigatório, texto de até 100 caracteres |
| status | Opcional. Aceita: `pendente`, `andamento`, `aprovacao`, `ajuste`, `aprovado`, `finalizado`. Padrão: `pendente` |
| inicio | Opcional, data no formato `AAAA-MM-DD` |
| final | Opcional, data no formato `AAAA-MM-DD`, não pode ser anterior a `inicio` |

A lista de status também é garantida pelo próprio banco, com uma regra `CHECK` na tabela.

## Códigos de resposta

| Código | Quando acontece |
| --- | --- |
| 200 | Consulta feita com sucesso |
| 201 | Projeto cadastrado |
| 400 | O corpo da requisição não é um JSON válido |
| 404 | Projeto ou rota não encontrados |
| 422 | Dados inválidos, com o erro de cada campo |
| 500 | Erro interno (o detalhe fica só no log do servidor) |

Exemplo de resposta `422`:

```json
{
  "erros": {
    "nome": "Campo obrigatório.",
    "final": "A data final não pode ser anterior à data de início."
  }
}
```

## Estrutura do projeto

```
public/index.php              Porta de entrada e rotas
src/Database.php              Conexão com o banco (lê o .env)
src/Resposta.php              Respostas em JSON com código de status
src/ProjetosController.php    Recebe os pedidos e valida os dados
src/ProjetosRepository.php    Consultas ao banco
database/schema.sql           Criação da tabela
database/seed.sql             Projetos de exemplo (opcional)
```

## Usando PostgreSQL

Crie a tabela no PostgreSQL (trocando `INTEGER PRIMARY KEY AUTOINCREMENT` por `INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY`) e altere o `.env`:

```
DB_DSN="pgsql:host=localhost;port=5432;dbname=projetos"
DB_USER=seu_usuario
DB_PASS=sua_senha
```

O restante do código não muda, porque o acesso ao banco é feito pelo PDO.

## Decisões técnicas

- **PHP puro, sem framework:** projeto pequeno, com cada parte fácil de entender.
- **SQLite:** permite rodar o projeto em poucos minutos, sem instalar um servidor de banco.
- **Consultas preparadas (PDO):** protegem contra SQL injection.
- **Validação em duas camadas:** a API valida os dados e devolve mensagens claras, e o banco também tem regras (`NOT NULL` e `CHECK`).
- **Datas no padrão ISO 8601 (`AAAA-MM-DD`):** formato universal, que ordena corretamente. A formatação para o padrão brasileiro fica a cargo de quem consome a API.
- **Credenciais no `.env`:** fora do código e fora do Git. O repositório tem só o `.env.example`.
- **Erros internos genéricos:** a API não expõe detalhes do banco para quem faz a requisição.

## Uso de IA

Usei o Claude como apoio em três momentos:

- **Estudo antes do teste:** para aprender conceitos de API REST, validação e infraestrutura, aplicando primeiro em um projeto pessoal.
- **Consulta durante o desenvolvimento:** tirei dúvidas sobre como validar as datas (formato e data final posterior à de início) e o status com uma lista de valores aceitos. Adaptei as sugestões ao projeto e testei cada caso.
- **Revisão e documentação:** a IA revisou o código, apontou erros que corrigi, criou os dados de exemplo e redigiu este README a partir do meu código. Executei todos os comandos para confirmar que funcionam.

A modelagem do banco, a estrutura do projeto e o restante do código foram feitos por mim baseados nos estudos.