#!/bin/bash
set -e

echo "=== Post-merge setup MoysesNet ==="

# Garante que o banco de dados está rodando
if ! mysqladmin ping --socket=/home/runner/mysql.sock --silent 2>/dev/null; then
    echo "Iniciando MySQL..."
    mysqld_safe --datadir=/home/runner/mysql_data --socket=/home/runner/mysql.sock --pid-file=/home/runner/mysql.pid &
    sleep 5
fi

# Aplica migrações/fixes do banco
if [ -f "login/painel/banco_fix.sql" ]; then
    echo "Aplicando banco_fix.sql..."
    mysql --socket=/home/runner/mysql.sock -u root agendamento < login/painel/banco_fix.sql
fi

echo "=== Post-merge concluído ==="
