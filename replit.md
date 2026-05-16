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

## Variáveis de Ambiente

Todas as variáveis de ambiente do projeto estão listadas abaixo. As marcadas como **secret** devem ser configuradas como Secrets no painel do Replit (nunca em texto plano). As demais podem ser definidas como variáveis comuns ou deixadas com o valor padrão indicado.

### Banco de Dados

Consumidas por `login/painel/conn.php`.

| Variável  | Obrig.? | Secret? | Padrão        | Consumido por                   | Descrição                                                |
|-----------|---------|---------|---------------|---------------------------------|----------------------------------------------------------|
| `DB_HOST` | Não     | Não     | `localhost`   | `login/painel/conn.php`         | Host do MySQL. Omita (ou use `localhost`) para banco local. Defina com o host remoto (ex: Hostinger) para banco externo. |
| `DB_USER` | Condicional | Não | `root`       | `login/painel/conn.php`         | Usuário do banco. Obrigatório quando `DB_HOST` é remoto. |
| `DB_PASS` | Condicional | **Sim** | *(vazio)*   | `login/painel/conn.php`         | Senha do banco. Obrigatória quando `DB_HOST` é remoto.   |
| `DB_NAME` | Condicional | Não | `agendamento` | `login/painel/conn.php`         | Nome do banco. Obrigatório quando `DB_HOST` é remoto.    |

**MySQL local**: usado automaticamente quando `DB_HOST=localhost` (padrão no Replit).
**MySQL externo (Hostinger)**: basta definir `DB_HOST` com o host remoto — o `start.sh` pula o MySQL local automaticamente.

### Aplicação

| Variável            | Obrig.? | Secret? | Padrão        | Consumido por                          | Descrição                                                                                          |
|---------------------|---------|---------|---------------|----------------------------------------|----------------------------------------------------------------------------------------------------|
| `APP_ENV`           | Não     | Não     | *(omitida)*   | `login/painel/error_config.php`        | `dev` ou `development` ativa modo de depuração (erros na tela). Qualquer outro valor = produção.  |
| `PHP_ERROR_LOG`     | Não     | Não     | `/tmp/php_errors.log` | `login/painel/error_config.php` | Caminho do arquivo de log de erros PHP em produção. Ignorado em modo dev (usa stderr).           |
| `API_WEBHOOK_TOKEN` | **Sim** | **Sim** | *(sem padrão)* | `login/painel/api/api_auth.php`       | Token secreto que protege todos os endpoints de `login/painel/api/`. Ausente = API retorna `500`. |

**Desenvolvimento** (`APP_ENV=dev` ou `APP_ENV=development`): todos os erros PHP são exibidos na tela (`display_errors=1`, `error_reporting=E_ALL`). Útil para depurar localmente no Replit.

**Produção** (variável omitida ou qualquer outro valor): erros são suprimidos da saída (`display_errors=0`). **Nunca defina `APP_ENV=dev` em produção** — isso exibiria detalhes internos do sistema para os usuários finais.

#### API_WEBHOOK_TOKEN — Configuração e uso

Gere um token forte (exemplo):

```bash
openssl rand -hex 32
```

Defina o valor gerado como secret `API_WEBHOOK_TOKEN` no painel de Secrets do Replit.

**Envio pelo provedor WhatsApp / cron job:**

- Cabeçalho HTTP (recomendado):
  ```
  Authorization: Bearer <token>
  ```
- Parâmetro de query (fallback, quando o provedor não suporta cabeçalhos customizados):
  ```
  https://seu-dominio/login/painel/api/recebe.php?token=<token>
  ```

Requisições com token errado ou ausente retornam `401 JSON`.

### Limpeza de Logs e Uploads

Consumidas por `login/painel/api/run_cleanup.php` e `login/painel/api/limpar_uploads.php`. Todas são opcionais; os valores padrão são seguros para uso imediato.

| Variável                    | Obrig.? | Secret? | Padrão | Consumido por                                                         | Descrição                                                                 |
|-----------------------------|---------|---------|--------|-----------------------------------------------------------------------|---------------------------------------------------------------------------|
| `LOG_MAX_AGE_DAYS`          | Não     | Não     | `7`    | `login/painel/api/run_cleanup.php`                                    | Idade máxima (em dias) dos arquivos de log antes de serem removidos.      |
| `LOG_MAX_SIZE_MB`           | Não     | Não     | `10`   | `login/painel/api/run_cleanup.php`, `login/painel/auth_guard.php`     | Tamanho máximo (em MB) do log de erros PHP antes de acionar limpeza.      |
| `CLEANUP_COOLDOWN_SECONDS`  | Não     | Não     | `30`   | `login/painel/api/run_cleanup.php`                                    | Intervalo mínimo (em segundos) entre execuções consecutivas da limpeza.   |
| `UPLOADS_MAX_AGE_SECONDS`   | Não     | Não     | `3600` | `login/painel/api/run_cleanup.php`, `login/painel/api/limpar_uploads.php` | Idade máxima (em segundos) de arquivos temporários em uploads/.       |
| `DB_FAILURES_MAX_SIZE_MB`   | Não     | Não     | `1`    | `login/painel/api/run_cleanup.php`                                    | Tamanho máximo (em MB) do log `logs/db_failures.log`.                     |
| `DB_FAILURES_MAX_AGE_DAYS`  | Não     | Não     | `30`   | `login/painel/api/run_cleanup.php`                                    | Idade máxima (em dias) das entradas do log `logs/db_failures.log`.        |

## Admin

- URL: `/login/painel/login_adm.php`
- Login: `admin` / Senha: `admin123`

## Domínio

- Desenvolvimento: `sitezip.replit.app`
- Produção (pendente verificação DNS): `sistema.moysesnet.com`
  - TXT record necessário: hostname=`sistema`, value=`replit-verify=70be1b56-a989-494c-91bb-22741c802f8d`
  - A record: `34.111.179.208`

## Hooks de Git (CRLF / line endings)

O projeto usa `.gitattributes` para forçar LF no repositório. O hook `scripts/pre-commit` converte automaticamente qualquer CRLF → LF nos arquivos staged antes de cada commit.

**No Replit:** nenhuma configuração manual é necessária. O `start.sh` executa `git config core.hooksPath scripts` automaticamente ao iniciar o projeto — o hook fica ativo imediatamente.

**Fora do Replit (clone externo):** execute `composer install` normalmente após clonar. O `post-install-cmd` do `composer.json` configura o hook automaticamente:

```bash
composer install
```

O Composer executa `bash scripts/install-hooks.sh` ao final da instalação, configurando `core.hooksPath=scripts` no repositório local — Git passa a usar os hooks diretamente de `scripts/`, sem copiar arquivos.

**Fallback manual** (sem Composer):

```bash
bash scripts/install-hooks.sh
```

> **Atenção (staging parcial):** se você usou `git add -p` para fazer staging de hunks específicos (não o arquivo inteiro), o hook re-adiciona o arquivo completo ao stage após a correção, o que pode incluir hunks que você não pretendia commitar. Revise o stage com `git diff --cached` depois de um commit que acione essa correção.

> **Dependências:** o hook requer `file`, `grep` e `perl` no PATH. Se algum deles estiver ausente, o hook falhará com uma mensagem de erro explícita em vez de ignorar silenciosamente.

## Preferências do Usuário

- Todas as instruções, sugestões e comunicações devem ser em **português brasileiro**.
- Design: azul marinho `#001f3f` + laranja `#FF5500` (padrão Enam Impact Agency).
