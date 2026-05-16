-- ============================================================
-- BLOCO 1: Garantir que todas as tabelas existam (idempotente).
-- Estas CREATE TABLE IF NOT EXISTS devem rodar ANTES dos ALTER
-- para que o script funcione mesmo que banco.sql falhe parcialmente.
-- ============================================================

CREATE TABLE IF NOT EXISTS `lista_negra_log` ( `id` int(11) NOT NULL AUTO_INCREMENT, `lista_negra_id` int(255) NOT NULL, `acao` varchar(50) COLLATE utf8_unicode_ci NOT NULL, `dados_anteriores` json DEFAULT NULL, `dados_novos` json DEFAULT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `data_log` datetime DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `login` ( `id` int(255) NOT NULL AUTO_INCREMENT, `login` varchar(255) DEFAULT NULL, `senha` varchar(255) DEFAULT NULL, `tipo` varchar(255) DEFAULT NULL, `usuario_api` varchar(255) DEFAULT NULL, `nome` varchar(255) DEFAULT NULL, `autorizado` varchar(255) DEFAULT NULL, `code_autorizado` varchar(255) DEFAULT NULL, `perfil_img` varchar(500) DEFAULT NULL, `porta` varchar(255) NOT NULL DEFAULT '', `webhook_completo` varchar(255) NOT NULL DEFAULT '', `qrcode` varchar(500) NOT NULL DEFAULT '', `tempo_code` varchar(255) NOT NULL DEFAULT '', `situacao` varchar(255) NOT NULL DEFAULT '', `email` varchar(255) NOT NULL DEFAULT '', `servidor_recebe` varchar(255) NOT NULL DEFAULT '', `servidor_envia` varchar(255) NOT NULL DEFAULT '', `servidor_confirma` varchar(255) NOT NULL DEFAULT '', `qr_quantidade` varchar(255) DEFAULT NULL, `qr_data` varchar(255) NOT NULL DEFAULT '', `caminho_vps` varchar(500) NOT NULL DEFAULT '', `funcao` varchar(255) NOT NULL DEFAULT '', `IA_boas_vindas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `IA_prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `IA_despedida` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `tempo_final` int(255) NOT NULL DEFAULT 0, `agenda_confirma` longtext, `agenda_cancela` longtext, `agenda_verfica` longtext, `tempo_verifica` int(255) NOT NULL DEFAULT 0, `solicitar_confirmacao` varchar(255) NOT NULL DEFAULT '', `pagamento_cliente` varchar(255) NOT NULL DEFAULT '', `id_assinatura` varchar(255) NOT NULL DEFAULT '', `vencimento` varchar(255) NOT NULL DEFAULT '', `creditos` int(255) NOT NULL DEFAULT 0, `plano` varchar(255) NOT NULL DEFAULT '', `modo_atuante` varchar(255) NOT NULL DEFAULT '', `modelo_ia` varchar(255) NOT NULL DEFAULT '', `google_cal` varchar(500) NOT NULL DEFAULT '', `logo` varchar(255) DEFAULT NULL, `nome_empresa` varchar(255) DEFAULT NULL, `tema` varchar(255) DEFAULT NULL, `numero_bot` varchar(255) NOT NULL DEFAULT '', `confirma_prof` varchar(500) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `logs_etiquetas` ( `id` int(11) NOT NULL AUTO_INCREMENT, `usuario_api` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `login` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `cliente_id` int(11) DEFAULT NULL, `etiquetas` text COLLATE utf8mb4_unicode_ci, `descricao` text COLLATE utf8mb4_unicode_ci, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mensagens_massa` ( `id` int(11) NOT NULL AUTO_INCREMENT, `login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `usuario_api` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `media_type` enum('text','image','video','audio','document') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text', `message_text` text COLLATE utf8mb4_unicode_ci, `media_file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `media_file_base64` longtext COLLATE utf8mb4_unicode_ci, `media_file_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `clientes_ids` text COLLATE utf8mb4_unicode_ci NOT NULL, `send_option` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'now', `schedule_datetime` datetime DEFAULT NULL, `interval_seconds` int(11) NOT NULL DEFAULT '300', `start_time` time DEFAULT '09:00:00', `end_time` time DEFAULT '18:00:00', `repeat_option` enum('once','daily','weekly','monthly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'once', `days_week` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `usar_ia` tinyint(1) DEFAULT '0', `status` enum('pendente','processando','pausada','concluida','erro','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente', `total_clientes` int(11) NOT NULL DEFAULT '0', `enviados` int(11) NOT NULL DEFAULT '0', `erros` int(11) NOT NULL DEFAULT '0', `ultimo_envio` datetime DEFAULT NULL, `proximo_envio` datetime DEFAULT NULL, `log_erros` text COLLATE utf8mb4_unicode_ci, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mensagens_massa_envios` ( `id` int(11) NOT NULL AUTO_INCREMENT, `mensagem_massa_id` int(11) NOT NULL, `cliente_id` int(11) NOT NULL, `cliente_nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `cliente_telefone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `status` enum('pendente','enviado','erro','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente', `tentativas` int(11) NOT NULL DEFAULT '0', `erro_detalhes` text COLLATE utf8mb4_unicode_ci, `enviado_em` datetime DEFAULT NULL, `response_api` text COLLATE utf8mb4_unicode_ci, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu` ( `id` int(255) NOT NULL AUTO_INCREMENT, `menu` varchar(255) DEFAULT NULL, `menu_pagina` varchar(255) DEFAULT NULL, `tipo` varchar(255) DEFAULT NULL, `ordem` varchar(255) DEFAULT NULL, `icone_menu` varchar(255) DEFAULT NULL, `funcao` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `modulos_baixados` ( `id` int(11) NOT NULL AUTO_INCREMENT, `nome_modulo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `caminho` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `usuario` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `data_hora` datetime DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `modulos_lista` ( `id` int(255) NOT NULL AUTO_INCREMENT, `nome_modulo` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `versao` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date_install` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date_down` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `creditos` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `tipo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `modulo_atual` ( `id` int(255) NOT NULL AUTO_INCREMENT, `nome_modulo` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `versao` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date_install` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date_down` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `tipo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pagamentos` ( `id` int(11) NOT NULL AUTO_INCREMENT, `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `data_pagamento` datetime NOT NULL, `id_pedido` varchar(100) COLLATE utf8_unicode_ci NOT NULL, `nome_produto` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `nome_completo` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `telefone` varchar(20) COLLATE utf8_unicode_ci NOT NULL, `cpf` varchar(14) COLLATE utf8_unicode_ci NOT NULL, `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL, `id_assinatura` varchar(100) COLLATE utf8_unicode_ci NOT NULL, `criado_em` varchar(255) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `pagamentos_status` ( `id` int(11) NOT NULL AUTO_INCREMENT, `usuario_api` varchar(50) COLLATE utf8_unicode_ci NOT NULL, `data_pagamento` date NOT NULL, `valor` decimal(10,2) NOT NULL, `status_pagamento` enum('Pago','Pendente') COLLATE utf8_unicode_ci NOT NULL, `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `planos_clientes` ( `id` int(255) NOT NULL AUTO_INCREMENT, `nome_plano` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `nome_modulo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `tipo` int(255) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `planos_features` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_plano` int(11) NOT NULL, `feature` text NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `planos_online` ( `id` int(11) NOT NULL AUTO_INCREMENT, `titulo` varchar(100) NOT NULL, `preco` decimal(10,2) NOT NULL, `icone` varchar(255) NOT NULL, `link_pagamento` varchar(255) NOT NULL, `code_pag` char(3) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `profissional` ( `id` int(255) NOT NULL AUTO_INCREMENT, `usuario_api` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', `profissional_nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', `profissional_cargo` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '', `telefone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `codigo_pais` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `profissional_servicos` ( `id` int(11) NOT NULL AUTO_INCREMENT, `profissional_id` int(11) NOT NULL, `servico_id` int(11) NOT NULL, `tempo_execucao_minutos` int(11) DEFAULT NULL, `valor_profissional` decimal(10,2) DEFAULT NULL, `ativo` tinyint(1) DEFAULT '1', `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '', PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `servicos` ( `id` int(11) NOT NULL AUTO_INCREMENT, `nome` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '', `descricao` text CHARACTER SET utf8 COLLATE utf8_unicode_ci, `duracao_minutos` int(11) NOT NULL DEFAULT '30', `valor` decimal(10,2) NOT NULL DEFAULT 0, `categoria` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `ativo` tinyint(1) DEFAULT '1', `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '', PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- FIM DO BLOCO 1
-- ============================================================

-- ============================================================
-- BLOCO 0: Corrigir PRIMARY KEY + AUTO_INCREMENT em tabelas
-- criadas pelo banco.sql original sem essas definições.
-- Seguro re-executar: ADD PRIMARY KEY falha silenciosamente
-- se a chave já existir; MODIFY funciona em ambos os casos.
-- ============================================================

ALTER TABLE `agendamento`          ADD PRIMARY KEY (`id`);
ALTER TABLE `agendamento`          MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `agenda_padrao`        ADD PRIMARY KEY (`id`);
ALTER TABLE `agenda_padrao`        MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chave`                ADD PRIMARY KEY (`id`);
ALTER TABLE `chave`                MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chave_ia_geral`       ADD PRIMARY KEY (`id`);
ALTER TABLE `chave_ia_geral`       MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `clientes`             ADD PRIMARY KEY (`id`);
ALTER TABLE `clientes`             MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `config`               ADD PRIMARY KEY (`id`);
ALTER TABLE `config`               MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `datas_excluidas`      ADD PRIMARY KEY (`id`);
ALTER TABLE `datas_excluidas`      MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `email_config`         ADD PRIMARY KEY (`id`);
ALTER TABLE `email_config`         MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `envio`                ADD PRIMARY KEY (`id`);
ALTER TABLE `envio`                MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;
ALTER TABLE `envio`                MODIFY COLUMN `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE `especialidades`       ADD PRIMARY KEY (`id`);
ALTER TABLE `especialidades`       MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `estilo`               ADD PRIMARY KEY (`id`);
ALTER TABLE `estilo`               MODIFY COLUMN `id` int(25)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `funcao`               ADD PRIMARY KEY (`id`);
ALTER TABLE `funcao`               MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `gerenciador`          ADD PRIMARY KEY (`id`);
ALTER TABLE `gerenciador`          MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `horarios_profissional` ADD PRIMARY KEY (`id`);
ALTER TABLE `horarios_profissional` MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `horarios_servico`     ADD PRIMARY KEY (`id`);
ALTER TABLE `horarios_servico`     MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `ia_historico`         ADD PRIMARY KEY (`id`);
ALTER TABLE `ia_historico`         MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `intervalos_profissional` ADD PRIMARY KEY (`id`);
ALTER TABLE `intervalos_profissional` MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `leads`                ADD PRIMARY KEY (`id`);
ALTER TABLE `leads`                MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `lista_negra`          ADD PRIMARY KEY (`id`);
ALTER TABLE `lista_negra`          MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `login`                ADD PRIMARY KEY (`id`);
ALTER TABLE `login`                MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `menu`                 ADD PRIMARY KEY (`id`);
ALTER TABLE `menu`                 MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `modulos_baixados`     ADD PRIMARY KEY (`id`);
ALTER TABLE `modulos_baixados`     MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `modulos_lista`        ADD PRIMARY KEY (`id`);
ALTER TABLE `modulos_lista`        MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `modulo_atual`         ADD PRIMARY KEY (`id`);
ALTER TABLE `modulo_atual`         MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pagamentos`           ADD PRIMARY KEY (`id`);
ALTER TABLE `pagamentos`           MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `pagamentos_status`    ADD PRIMARY KEY (`id`);
ALTER TABLE `pagamentos_status`    MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `planos_clientes`      ADD PRIMARY KEY (`id`);
ALTER TABLE `planos_clientes`      MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `planos_features`      ADD PRIMARY KEY (`id`);
ALTER TABLE `planos_features`      MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `planos_online`        ADD PRIMARY KEY (`id`);
ALTER TABLE `planos_online`        MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `profissional`         ADD PRIMARY KEY (`id`);
ALTER TABLE `profissional`         MODIFY COLUMN `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `profissional_servicos` ADD PRIMARY KEY (`id`);
ALTER TABLE `profissional_servicos` MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `servicos`             ADD PRIMARY KEY (`id`);
ALTER TABLE `servicos`             MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `lista_negra_log`      ADD PRIMARY KEY (`id`);
ALTER TABLE `lista_negra_log`      MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `logs_etiquetas`       ADD PRIMARY KEY (`id`);
ALTER TABLE `logs_etiquetas`       MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `mensagens_massa`      ADD PRIMARY KEY (`id`);
ALTER TABLE `mensagens_massa`      MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

ALTER TABLE `mensagens_massa_envios` ADD PRIMARY KEY (`id`);
ALTER TABLE `mensagens_massa_envios` MODIFY COLUMN `id` int(11)  NOT NULL AUTO_INCREMENT;

-- ============================================================
-- FIM DO BLOCO 0
-- ============================================================

INSERT IGNORE INTO `menu` (`id`, `menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`) VALUES (1, 'Administração', 'config_adm.php', '1', '1', 'feather icon-shield ', 'adm'), (2, 'Chave & Pagamento', 'chave.php', '1', '2', 'fa fa-key', 'adm'), (3, 'QR Code', 'qrcode.php', '1', '3.2', 'fa fa-qrcode', 'adm'), (4, 'Criar Bots', 'criar_bot.php', '1', '4', 'feather icon-plus-circle ', 'adm'), (5, 'Listar Bots', 'listar_bot.php', '1', '5', 'feather icon-list ', 'adm'), (8, 'Estilo da Página', 'estilo_pagina.php', '1', '3', 'feather icon-menu ', 'adm'), (9, 'Criar Menus', 'criar_menus.php', '1', '8', 'feather icon-settings ', 'adm'), (11, 'Instalação', 'config_adm.php', '4', '1', 'feather icon-shield ', 'adm_install'), (12, 'QR Code', 'qrcode.php', '2', '1.4', 'fa fa-qrcode', 'Agendamento,Atendimento'), (13, 'Sair', 'sair.php', '2', '9', 'feather icon-log-out', 'adm,Agendamento,Atendimento,prof'), (16, 'Inf Atendimento', 'prompt.php', '2', '1.1', 'feather icon-plus-circle', 'Agendamento'), (18, 'Visualizar Agenda', 'agenda.php', '2', '1.3', 'fa fa-users', 'Agendamento'), (22, 'Config Mensagens', 'msg_config.php', '2', '1.7', 'fa fa-user', 'Agendamento'), (23, 'Clientes ', 'clientes.php', '2', '1.4', 'fa fa-users', 'Agendamento,Atendimento'), (24, 'Ativação', 'perfil.php', '2', '1.8', 'feather icon-shield', 'Agendamento,Atendimento'), (25, 'Agendamentos', 'agendamentos.php', '2', '1.0', 'fa fa-calendar', 'Agendamento'), (26, 'Início', 'modo.php', '2', '1', 'feather icon-home', 'Agendamento'), (28, 'Datas especiais', 'datas_especiais.php', '2', '1.3', 'fa fa-calendar', 'Agendamento'), (29, 'Módulos', 'modulos.php', '1', '1.2', 'fa fa-upload', 'adm'), (30, 'Módulos baixados', 'modulos_instalado.php', '1', '1', 'fa fa-download', 'adm'), (31, 'Planos', 'planos.php', '1', '6', 'fa fa-credit-card', 'adm'), (32, 'IA config', 'ia_config.php', '1', '7', 'fa fa-dashboard', 'adm'), (33, 'Leads', 'leads.php', '1', '8', 'fa fa-envelope', 'adm'), (35, 'Módulos créditos', 'modulos_creditos.php', '1', '2', 'fa fa-money', 'adm'), (36, 'Inf Atendimento', 'prompt.php', '2', '1.1', 'feather icon-plus-circle', 'Atendimento'), (37, 'Início', 'modo.php', '2', '1', 'feather icon-home', 'Atendimento'), (38, 'Financeiro', 'financeiro.php', '2', '1', 'fa fa-money', 'Agendamento'), (39, 'Relatório', 'relatorio.php', '2', '1.1', 'feather icon-bar-chart', 'Agendamento'), (41, 'Disparo msg', 'msg_massa2.php', '2', '1.8', 'fa fa-envelope', 'Agendamento,Atendimento'), (42, 'Financeiro', 'financeiro_prof.php', '5', '1', 'fa fa-money', 'prof'), (43, 'Relatório', 'relatorio_prof.php', '5', '2', 'feather icon-list', 'prof'), (44, 'Visualizar Agenda', 'agenda_porf.php', '5', '3', 'fa fa-users', 'prof'), (45, 'Integração', 'integracao.php', '5', '4', 'feather icon-plus-circle', 'Agendamento,prof'), (46, 'Senha', 'senha.php', '5', '5', 'fa fa-lock', 'adm,Agendamento,Atendimento,prof'), (47, 'Bloqueados', 'lista_negra.php', '2', '3', 'feather icon-list', 'Agendamento,Atendimento');

INSERT IGNORE INTO `modulos_lista` (`id`, `nome_modulo`, `versao`, `date_install`, `date_down`, `creditos`, `tipo`) VALUES (4, 'Agendamento', '3.0', '2025-07-23 12:35:18', '2025-07-23 12:35:18', NULL, '1'), (5, 'Atendimento', '3.0', '2025-07-23 12:35:18', '2025-07-23 12:35:18', NULL, '1');

INSERT IGNORE INTO `modulo_atual` (`id`, `nome_modulo`, `versao`, `date_install`, `date_down`, `tipo`) VALUES (1, 'Agendamento e Atendimento', '3.0', NULL, '2025-07-23 12:35:18', NULL);

INSERT IGNORE INTO `planos_clientes` (`id`, `nome_plano`, `nome_modulo`, `date`, `tipo`) VALUES (1, 'plano1', 'Agendamento', '2025-07-18 22:48:25', 1), (5, 'plano2', 'Agendamento', '2025-07-21 11:22:46', 1), (6, 'plano2', 'Atendimento', '2025-07-21 11:22:49', 1), (10, 'plano2', 'Credito 100', '2025-08-08 14:32:38', 0), (11, 'plano3', 'credito 500', '2025-08-08 14:32:47', 0), (12, 'plano3', 'Agendamento', '2025-08-08 14:32:51', 1), (14, 'plano1', 'Ilimitado', '2025-08-11 22:22:41', 0), (15, 'plano1', 'Atendimento', '2025-08-11 23:44:31', 1), (16, 'plano3', 'Atendimento', '2025-08-15 11:34:42', 1);

INSERT IGNORE INTO `planos_features` (`id`, `id_plano`, `feature`) VALUES (1, 1, 'Atendimento humanizado com inteligência artificial.'), (2, 1, 'Reconhecimento de texto e áudio.'), (3, 1, 'Automação básica de processos de vendas.'), (4, 1, 'Suporte para até 5 produtos.'), (5, 1, 'Cadastro de até 100 clientes.'), (7, 2, 'Atendimento humanizado com inteligência artificial.'), (8, 2, 'Reconhecimento de texto, áudio e imagens.'), (9, 2, 'Automação completa de processos de vendas.'), (10, 2, 'Suporte para produtos ilimitados.'), (11, 2, 'Cadastro de clientes ilimitados.'), (12, 2, 'Sistema de acompanhamento e análise de conversões.'), (14, 3, 'Atendimento humanizado com inteligência artificial avançada.'), (15, 3, 'Reconhecimento de texto, áudio, imagens e contatos.'), (16, 3, 'Automação completa de processos de vendas e pós-venda.'), (17, 3, 'Suporte para produtos ilimitados com categorização.'), (18, 3, 'Cadastro de clientes ilimitados com segmentação.'), (19, 3, 'Sistema avançado de análise de métricas e conversões.'), (20, 3, 'Integração com múltiplos sistemas e APIs.'), (21, 3, 'Suporte técnico 24/7 e treinamento da equipe.'), (22, 1, 'Reconhecimento de áudio');

INSERT IGNORE INTO `planos_online` (`id`, `titulo`, `preco`, `icone`, `link_pagamento`, `code_pag`, `ativo`) VALUES (1, 'Popular', 97.00, '/login/painel/logo_basico.png', 'https://seusite.com/pagar?plano=premium', '1A3', 1), (2, 'Plano Premium', 197.00, '/login/painel/logo_premium.png', 'https://seusite.com/pagar?plano=premium', '2B4', 1), (3, 'Premium', 497.00, '/login/painel/logo_enterprise.png', 'https://pay.kiwify.com.br/8JN3lI9', '3C5', 1);

CREATE OR REPLACE VIEW `view_estatisticas_bloqueios` AS SELECT `lista_negra`.`usuario_api` AS `usuario_api`, count(0) AS `total_bloqueios`, count((case when (`lista_negra`.`status` = 'ativo') then 1 end)) AS `bloqueios_ativos`, count((case when (`lista_negra`.`status` = 'inativo') then 1 end)) AS `bloqueios_inativos`, max(`lista_negra`.`data_bloqueio`) AS `ultimo_bloqueio`, sum(`lista_negra`.`tentativas_contato`) AS `total_tentativas` FROM `lista_negra` GROUP BY `lista_negra`.`usuario_api`;

CREATE OR REPLACE VIEW `view_lista_negra_ativa` AS SELECT `lista_negra`.`id` AS `id`, `lista_negra`.`nome` AS `nome`, `lista_negra`.`telefone` AS `telefone`, `lista_negra`.`motivo_bloqueio` AS `motivo_bloqueio`, `lista_negra`.`data_bloqueio` AS `data_bloqueio`, `lista_negra`.`tentativas_contato` AS `tentativas_contato`, `lista_negra`.`ultima_tentativa` AS `ultima_tentativa`, `lista_negra`.`observacoes` AS `observacoes` FROM `lista_negra` WHERE (`lista_negra`.`status` = 'ativo') ORDER BY `lista_negra`.`data_bloqueio` DESC;

INSERT IGNORE INTO `login` (`id`, `login`, `senha`, `tipo`, `usuario_api`, `nome`, `autorizado`, `code_autorizado`, `porta`, `webhook_completo`, `qrcode`, `tempo_code`, `situacao`, `email`, `servidor_recebe`, `servidor_envia`, `servidor_confirma`, `qr_data`, `caminho_vps`, `funcao`, `tempo_final`, `tempo_verifica`, `solicitar_confirmacao`, `pagamento_cliente`, `id_assinatura`, `vencimento`, `creditos`, `plano`, `modo_atuante`, `modelo_ia`, `google_cal`, `numero_bot`) VALUES (1, 'admin', 'admin123', '1', 'admin', 'Administrador', '2', '', '', '', '', '', 'ativo', 'admin@admin.com', '', '', '', '', '', 'adm', 0, 0, '', '', '', '', 9999, 'plano3', '', 'gpt-4o-mini', '', '');

-- Adicionar coluna endereco na tabela config (idempotente)
ALTER TABLE `config` ADD COLUMN IF NOT EXISTS `endereco` VARCHAR(255) DEFAULT NULL;

-- Tempo de expiração de sessão configurável pelo painel (idempotente via prepared statement)
-- MySQL não suporta ADD COLUMN IF NOT EXISTS, então usamos information_schema para verificar
SET @__col_exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'config'
      AND COLUMN_NAME  = 'session_timeout_min'
);
SET @__sql = IF(@__col_exists = 0,
    'ALTER TABLE `config` ADD COLUMN `session_timeout_min` INT(11) NOT NULL DEFAULT 30',
    'SELECT 1'
);
PREPARE __stmt FROM @__sql;
EXECUTE __stmt;
DEALLOCATE PREPARE __stmt;

-- Diagnóstico do banco de dados no menu admin (idempotente via INSERT IGNORE)
INSERT IGNORE INTO `menu` (`id`, `menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`) VALUES (50, 'Diagnóstico BD', 'db_diagnostics.php', '1', '8.5', 'fa fa-database', 'adm,adm_install');

-- Logo MoysesNet (Modelo 1 Hexágono) — persiste após reinício
UPDATE `estilo` SET
  `logo_site`    = 'img/logo-moysesnet.svg',
  `emblema_site` = 'img/logo-moysesnet-small.svg',
  `icon_site`    = 'img/logo-moysesnet-icon.svg',
  `titulo`       = 'MoysesNet'
WHERE id = (SELECT MIN(id) FROM (SELECT id FROM estilo) t);
