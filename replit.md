# MoysesNet — Sistema de Agendamento + Chatbot WhatsApp

Sistema PHP + MySQL para agendamento de serviços e atendimento via WhatsApp, com painel administrativo completo. Design Enam Impact Agency (azul marinho #001f3f + laranja #FF5500).

## Stack

- **Backend**: PHP 8.2 (servidor embutido na porta 5000)
- **Banco de dados**: MySQL (local via socket, ou externo via variáveis de ambiente)
- **Frontend**: HTML + CSS + JS (jQuery), sem framework
- **Roteamento**: `router.php` na raiz

## Estrutura Principal

- `login/painel/` — painel administrativo (PHP)
- `login/painel/conn.php` — conexão com o banco (usa variáveis de ambiente)
- `login/painel/banco.sql` — schema principal
- `login/painel/banco_fix.sql` — tabelas complementares e correções
- `login/files/` — assets públicos (CSS, JS, imagens)
- `login/files/extra-pages/` — páginas públicas (coming soon, etc.)
- `scripts/` — scripts auxiliares (limpeza de uploads, post-merge, hooks de Git)
- `start.sh` — script de inicialização
- `router.php` — roteador PHP

## Banco de Dados

O `conn.php` lê as credenciais de variáveis de ambiente:

| Variável  | Padrão        | Descrição                    |
|-----------|---------------|------------------------------|
| `DB_HOST` | `localhost`   | Host do MySQL                |
| `DB_USER` | `root`        | Usuário do banco             |
| `DB_PASS` | *(secret)*    | Senha do banco               |
| `DB_NAME` | `agendamento` | Nome do banco de dados       |

**MySQL local**: usado automaticamente quando `DB_HOST=localhost` (padrão no Replit).
**MySQL externo (Hostinger)**: basta definir `DB_HOST` com o host remoto — o `start.sh` pula o MySQL local automaticamente.

## Admin

- URL: `/login/painel/login_adm.php`
- Login: `admin` / Senha: `admin123`

## Domínio

- Desenvolvimento: `sitezip.replit.app`
- Produção (pendente verificação DNS): `sistema.moysesnet.com`
  - TXT record necessário: hostname=`sistema`, value=`replit-verify=70be1b56-a989-494c-91bb-22741c802f8d`
  - A record: `34.111.179.208`

## Hooks de Git (CRLF / line endings)

O projeto usa `.gitattributes` para forçar LF no repositório. Para evitar que arquivos com CRLF (Windows) entrem no commit, instale o hook de pre-commit uma vez por clone:

```bash
bash scripts/install-hooks.sh
```

O hook (`scripts/pre-commit`) inspeciona os arquivos staged e converte automaticamente qualquer CRLF → LF antes do commit. Novos clones ou colaboradores devem rodar o mesmo comando após clonar.

> **Atenção (staging parcial):** se você usou `git add -p` para fazer staging de hunks específicos (não o arquivo inteiro), o hook re-adiciona o arquivo completo ao stage após a correção, o que pode incluir hunks que você não pretendia commitar. Revise o stage com `git diff --cached` depois de um commit que acione essa correção.

> **Dependências:** o hook requer `file`, `grep` e `perl` no PATH. Se algum deles estiver ausente, o hook falhará com uma mensagem de erro explícita em vez de ignorar silenciosamente.

## Preferências do Usuário

- Todas as instruções, sugestões e comunicações devem ser em **português brasileiro**.
- Design: azul marinho `#001f3f` + laranja `#FF5500` (padrão Enam Impact Agency).
