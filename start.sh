#!/bin/bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

MYSQL_SOCKET=/home/runner/mysql.sock
MYSQL_DATADIR=/home/runner/mysql_data
MYSQL_PID=/home/runner/mysql.pid
LOCAL_DB_NAME=agendamento

if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "localhost" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "=== Banco externo Hostinger configurado: ${DB_HOST} — MySQL local será ignorado ==="
    rm -f $MYSQL_SOCKET $MYSQL_PID
else
    echo "=== Iniciando MySQL local ==="
    rm -f $MYSQL_SOCKET $MYSQL_PID

    if [ ! -d "$MYSQL_DATADIR/mysql" ]; then
        echo "Inicializando banco de dados (limpando datadir antigo se necessário)..."
        rm -rf $MYSQL_DATADIR
        mkdir -p $MYSQL_DATADIR
        mysqld --initialize-insecure --user=runner --datadir=$MYSQL_DATADIR 2>&1
    fi

    mysqld --user=runner \
        --datadir=$MYSQL_DATADIR \
        --socket=$MYSQL_SOCKET \
        --pid-file=$MYSQL_PID \
        --mysqlx=OFF \
        --daemonize 2>&1

    echo "Aguardando MySQL iniciar..."
    for i in $(seq 1 20); do
        if mysql -u root --socket=$MYSQL_SOCKET -e "SELECT 1;" > /dev/null 2>&1; then
            echo "MySQL pronto!"
            break
        fi
        sleep 1
    done

    echo "=== Configurando banco de dados local ==="
    mysql -u root --socket=$MYSQL_SOCKET -e "CREATE DATABASE IF NOT EXISTS $LOCAL_DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1

    LOGIN_EXISTS=$(mysql -u root --socket=$MYSQL_SOCKET $LOCAL_DB_NAME -e "SHOW TABLES LIKE 'login';" 2>/dev/null | wc -l)
    if [ "$LOGIN_EXISTS" -lt "2" ]; then
        echo "Importando schema SQL principal..."
        mysql --force -u root --socket=$MYSQL_SOCKET $LOCAL_DB_NAME < "$SCRIPT_DIR/login/painel/banco.sql" 2>&1 | grep -v "^$" || true
        echo "Schema principal importado!"
    fi

    echo "Aplicando tabelas complementares..."
    mysql --force -u root --socket=$MYSQL_SOCKET $LOCAL_DB_NAME < "$SCRIPT_DIR/login/painel/banco_fix.sql" 2>&1 | grep -v "^$" || true
    echo "Banco de dados local pronto ($(mysql -u root --socket=$MYSQL_SOCKET $LOCAL_DB_NAME -e 'SHOW TABLES;' 2>/dev/null | wc -l) tabelas)."
fi

echo "=== Configurando hooks de Git ==="
git config core.hooksPath scripts 2>/dev/null && echo "core.hooksPath=scripts configurado." || echo "Aviso: não foi possível configurar core.hooksPath (fora de um repositório git?)."

echo "=== Criando links simbólicos de assets ==="
ln -sf "$SCRIPT_DIR/login/files" "$SCRIPT_DIR/files" 2>/dev/null || true

echo "=== Iniciando varredura periódica de uploads antigos ==="
bash "$SCRIPT_DIR/scripts/limpar_uploads.sh" &

echo "=== Iniciando varredura periódica de logs antigos ==="
bash "$SCRIPT_DIR/scripts/limpar_logs.sh" &

echo "=== Iniciando PHP na porta 5000 ==="
cd "$SCRIPT_DIR"
php -S 0.0.0.0:5000 -t "$SCRIPT_DIR" router.php
