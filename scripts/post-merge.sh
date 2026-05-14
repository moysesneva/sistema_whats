#!/bin/bash
set -e

echo "=== Post-merge setup MoysesNet ==="

# Garante que o diretório de dados existe
mkdir -p /home/runner/mysql_data

# Tenta iniciar o MySQL apenas se ainda não estiver rodando
if ! mysqladmin ping --socket=/home/runner/mysql.sock --silent 2>/dev/null; then
    echo "Iniciando MySQL..."
    mysqld_safe \
        --datadir=/home/runner/mysql_data \
        --socket=/home/runner/mysql.sock \
        --pid-file=/home/runner/mysql.pid \
        --log-error=/home/runner/mysql_data/mysql.err \
        2>/dev/null &
    # Aguarda até 10 s pelo MySQL
    for i in $(seq 1 10); do
        sleep 1
        if mysqladmin ping --socket=/home/runner/mysql.sock --silent 2>/dev/null; then
            echo "MySQL pronto."
            break
        fi
    done
fi

# Aplica migrações/fixes do banco (ignora silenciosamente se MySQL indisponível)
if mysqladmin ping --socket=/home/runner/mysql.sock --silent 2>/dev/null; then
    if [ -f "login/painel/banco_fix.sql" ]; then
        echo "Aplicando banco_fix.sql..."
        mysql --socket=/home/runner/mysql.sock -u root agendamento < login/painel/banco_fix.sql || true
    fi
else
    echo "MySQL indisponível — migrações ignoradas."
fi

echo "=== Post-merge concluído ==="
