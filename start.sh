#!/bin/bash

MYSQL_SOCKET=/home/runner/mysql.sock
MYSQL_DATADIR=/home/runner/mysql_data
MYSQL_PID=/home/runner/mysql.pid
DB_NAME=agendamento

echo "=== Iniciando MySQL ==="
rm -f $MYSQL_SOCKET $MYSQL_PID

if [ ! -d "$MYSQL_DATADIR/mysql" ]; then
    echo "Inicializando banco de dados pela primeira vez..."
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

echo "=== Configurando banco de dados ==="
mysql -u root --socket=$MYSQL_SOCKET -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1

TABLE_COUNT=$(mysql -u root --socket=$MYSQL_SOCKET $DB_NAME -e "SHOW TABLES;" 2>/dev/null | wc -l)
if [ "$TABLE_COUNT" -lt "5" ]; then
    echo "Importando schema SQL principal..."
    mysql -u root --socket=$MYSQL_SOCKET $DB_NAME < /home/runner/workspace/login/painel/banco.sql 2>/dev/null || true
    echo "Schema principal importado!"
fi

echo "Aplicando tabelas complementares..."
mysql -u root --socket=$MYSQL_SOCKET $DB_NAME < /home/runner/workspace/login/painel/banco_fix.sql 2>&1
echo "Banco de dados pronto ($(mysql -u root --socket=$MYSQL_SOCKET $DB_NAME -e 'SHOW TABLES;' 2>/dev/null | wc -l) tabelas)."

echo "=== Criando links simbólicos de assets ==="
ln -sf /home/runner/workspace/login/files /home/runner/workspace/files 2>/dev/null || true

echo "=== Iniciando PHP na porta 5000 ==="
cd /home/runner/workspace
php -S 0.0.0.0:5000 -t /home/runner/workspace router.php
