 SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

 START TRANSACTION;

 SET time_zone = "+00:00";

 CREATE TABLE `agendamento` ( `id` int(255) NOT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `dia` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `horario` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `profissional_nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `profissional_cargo` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `cliente_telefone` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `cliente_nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `data` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `id_profissional` int(255) NOT NULL, `confirmacao` int(255) NOT NULL, `lembrete` int(255) NOT NULL, `servico_id` int(11) DEFAULT NULL, `duracao_minutos` int(11) DEFAULT NULL, `valor_servico` decimal(10,2) DEFAULT NULL, `id_cliente_ref` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `agenda_padrao` ( `id` int(255) NOT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `dia` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `horario` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `profissional_nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `profissional_cargo` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `id_profissional` int(255) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `chave` ( `id` int(255) NOT NULL, `chave` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `chave_ia_geral` ( `id` int(255) NOT NULL, `chave` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, `nome` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, `date` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, `plano` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `clientes` ( `id` int(255) NOT NULL, `nome` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `telefone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `situacao` int(255) DEFAULT NULL, `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `time_atendimento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `id_agendamento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `funcao` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `time_resposta` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `etiqueta` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `updated_at` varchar(255) COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `config` ( `id` int(255) NOT NULL, `ip_vps` varchar(255) DEFAULT NULL, `porta` int(255) DEFAULT NULL, `nova_porta` int(255) DEFAULT NULL, `chave` varchar(255) DEFAULT NULL, `caminho_modelo` varchar(255) DEFAULT NULL, `hero_title` varchar(255) DEFAULT NULL, `hero_subtitle` varchar(255) DEFAULT NULL, `services_title` varchar(255) DEFAULT NULL, `services_description` text, `chave_painel` varchar(255) DEFAULT NULL, `webhook` varchar(255) DEFAULT NULL, `validade` varchar(255) DEFAULT NULL, `webhook_completo` varchar(255) DEFAULT NULL, `google` varchar(255) DEFAULT NULL, `servidor_recebe` varchar(255) DEFAULT NULL, `servidor_envia` varchar(255) DEFAULT NULL, `servidor_confirma` varchar(255) DEFAULT NULL, `api` varchar(255) DEFAULT NULL, `link_pagamento` varchar(500) DEFAULT NULL, `preco` varchar(255) DEFAULT NULL, `telefone` varchar(255) DEFAULT NULL, `chave_kiwify` varchar(255) DEFAULT NULL, `tipo_vendas` varchar(10) DEFAULT 'vendas1', `texto_vendas` text, `video_youtube` varchar(255) DEFAULT NULL, `tema` int(1) DEFAULT '4', `planos` text, `card1_icon` varchar(255) DEFAULT NULL, `card1_title` varchar(255) DEFAULT NULL, `card1_description` text, `card2_icon` varchar(255) DEFAULT NULL, `card2_title` varchar(255) DEFAULT NULL, `card2_description` text, `card3_icon` varchar(255) DEFAULT NULL, `card3_title` varchar(255) DEFAULT NULL, `card3_description` text, `feature_image` varchar(255) DEFAULT NULL, `feature_title` varchar(255) DEFAULT NULL, `feature_description` text, `feature_items` text, `link_plano1` varchar(255) DEFAULT NULL, `link_plano2` varchar(255) DEFAULT NULL, `link_plano3` varchar(255) DEFAULT NULL, `link_creditos` varchar(255) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 INSERT INTO `config` (`id`, `ip_vps`, `porta`, `nova_porta`, `chave`, `caminho_modelo`, `hero_title`, `hero_subtitle`, `services_title`, `services_description`, `chave_painel`, `webhook`, `validade`, `webhook_completo`, `google`, `servidor_recebe`, `servidor_envia`, `servidor_confirma`, `api`, `link_pagamento`, `preco`, `telefone`, `chave_kiwify`, `tipo_vendas`, `texto_vendas`, `video_youtube`, `tema`, `planos`, `card1_icon`, `card1_title`, `card1_description`, `card2_icon`, `card2_title`, `card2_description`, `card3_icon`, `card3_title`, `card3_description`, `feature_image`, `feature_title`, `feature_description`, `feature_items`, `link_plano1`, `link_plano2`, `link_plano3`, `link_creditos`) VALUES (4, '109.199.96.201', 443, 0, 'b49cc1beb9b8a6864bf03aa04bbd9fe7', 'uploads/', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'vendas1', '', '', 4, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

CREATE TABLE `datas_excluidas` ( `id` int(255) NOT NULL, `data_excluida` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `id_profissional` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `motivo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `profissional` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `email_config` ( `id` int(11) NOT NULL, `login` varchar(100) NOT NULL, `email` varchar(255) NOT NULL, `senha_app` varchar(255) NOT NULL, `smtp_host` varchar(255) NOT NULL DEFAULT 'smtp.gmail.com', `smtp_port` int(11) NOT NULL DEFAULT '587', `smtp_secure` varchar(10) NOT NULL DEFAULT 'tls', `status` tinyint(1) DEFAULT '1', `ultima_verificacao` timestamp NULL DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `envio` ( `id` int(255) NOT NULL, `comando` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `telefone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `msg` longtext COLLATE utf8mb4_unicode_ci NOT NULL, `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `usuario_api` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `data_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 CREATE TABLE `especialidades` ( `id` int(255) NOT NULL, `especialidades` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `estilo` ( `id` int(25) NOT NULL, `logo_site` varchar(255) DEFAULT NULL, `emblema_site` varchar(255) DEFAULT NULL, `fundo_login` varchar(255) DEFAULT NULL, `icon_site` varchar(255) DEFAULT NULL, `titulo` varchar(255) DEFAULT NULL, `barra_logo` varchar(255) NOT NULL, `barra_principal` varchar(255) NOT NULL, `menu_trasnparencia` varchar(255) NOT NULL, `cor_selecao_menu` varchar(255) NOT NULL, `tema_menu` varchar(255) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 INSERT INTO `estilo` (`id`, `logo_site`, `emblema_site`, `fundo_login`, `icon_site`, `titulo`, `barra_logo`, `barra_principal`, `menu_trasnparencia`, `cor_selecao_menu`, `tema_menu`) VALUES (1, 'img/Edita Código (2) (1) (1).png', 'img/Design sem nome (7).png', 'img/2070739-abstrato-tecnologia-futurista-azul-magica-particulas-linhas-luz-cintilante-brilho-no-fundo-escuro-vetor.jpg', 'img/logo (1).ico', 'Edita Código Online', 'theme8', 'theme6', 'theme1', '', 'light');

 CREATE TABLE `funcao` ( `id` int(255) NOT NULL, `funcao` varchar(255) DEFAULT NULL, `id_funcao` int(255) DEFAULT NULL, `login` varchar(255) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `gerenciador` ( `id` int(255) NOT NULL, `data_hora` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `nomeCompleto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `celular` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `cpf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `comando` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 INSERT INTO `gerenciador` (`id`, `data_hora`, `nomeCompleto`, `email`, `celular`, `cpf`, `usuario_api`, `status`, `comando`) VALUES (420, NULL, NULL, NULL, '553184767330', NULL, 'agenda_553184767330', NULL, 'criar_conta'), (421, NULL, NULL, NULL, '553184767330', NULL, 'agenda_553184767330', NULL, 'criar_conta'), (422, NULL, NULL, NULL, '553184767331', NULL, 'agenda_553184767331', NULL, 'criar_conta'), (423, NULL, NULL, NULL, '553184767330', NULL, 'agenda_553184767330', NULL, 'criar_conta'), (424, NULL, NULL, NULL, '553184767331', NULL, 'agenda_553184767331', NULL, 'criar_conta'), (425, NULL, NULL, NULL, '5521986308126', NULL, 'agenda_5521986308126', NULL, 'criar_conta'), (426, NULL, NULL, NULL, '556696553735', NULL, 'agenda_556696553735', NULL, 'criar_conta'), (427, NULL, NULL, NULL, '558588032500', NULL, 'agenda_558588032500', NULL, 'criar_conta'), (428, NULL, NULL, NULL, '557388840305', NULL, 'agenda_557388840305', NULL, 'criar_conta'), (429, NULL, NULL, NULL, '55123456789', NULL, 'agenda_55123456789', NULL, 'criar_conta');

 CREATE TABLE `horarios_profissional` ( `id` int(11) NOT NULL, `profissional_id` int(11) NOT NULL, `dia_semana` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, `hora_entrada` time DEFAULT NULL, `almoco_inicio` time DEFAULT NULL, `almoco_fim` time DEFAULT NULL, `hora_saida` time DEFAULT NULL, `ativo` tinyint(1) DEFAULT '1', `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `horarios_servico` ( `id` int(11) NOT NULL, `profissional_servico_id` int(11) NOT NULL, `dia_semana` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, `hora_inicio` time NOT NULL, `hora_fim` time NOT NULL, `ativo` tinyint(1) DEFAULT '1', `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `ia_historico` ( `id` int(255) NOT NULL, `ia_msg` longtext COLLATE utf8_unicode_ci NOT NULL, `usuario_msg` longtext COLLATE utf8_unicode_ci NOT NULL, `telefone_usuario` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `login_historico` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `data_hora` varchar(255) COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `intervalos_profissional` ( `id` int(11) NOT NULL, `horario_id` int(11) NOT NULL, `intervalo_inicio` time NOT NULL, `intervalo_fim` time NOT NULL, `motivo` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `leads` ( `id` int(255) NOT NULL, `nome` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `whats` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `data` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `lista_negra_log` ( `id` int(11) NOT NULL, `lista_negra_id` int(255) NOT NULL, `acao` varchar(50) COLLATE utf8_unicode_ci NOT NULL, `dados_anteriores` json DEFAULT NULL, `dados_novos` json DEFAULT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `data_log` datetime DEFAULT CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `lista_negra` ( `id` int(255) NOT NULL, `nome` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nome ou identificação do contato bloqueado', `telefone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Número de telefone do contato bloqueado', `usuario_api` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ID do usuário que criou o bloqueio', `motivo_bloqueio` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Motivo do bloqueio (spam, telemarketing, etc)', `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'ativo' COMMENT 'Status do bloqueio (ativo/inativo)', `data_bloqueio` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora do bloqueio', `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última atualização do registro', `observacoes` text COLLATE utf8_unicode_ci COMMENT 'Observações adicionais sobre o bloqueio', `ip_origem` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'IP de origem do bloqueio', `tentativas_contato` int(11) DEFAULT '0' COMMENT 'Número de tentativas de contato após bloqueio', `ultima_tentativa` datetime DEFAULT NULL COMMENT 'Data da última tentativa de contato' ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Tabela para gerenciar lista negra de contatos bloqueados';

 CREATE TABLE `login` ( `id` int(255) NOT NULL, `login` varchar(255) DEFAULT NULL, `senha` varchar(255) DEFAULT NULL, `tipo` varchar(255) DEFAULT NULL, `usuario_api` varchar(255) DEFAULT NULL, `nome` varchar(255) DEFAULT NULL, `autorizado` varchar(255) DEFAULT NULL, `code_autorizado` varchar(255) DEFAULT NULL, `perfil_img` varchar(500) DEFAULT NULL, `porta` varchar(255) NOT NULL, `webhook_completo` varchar(255) NOT NULL, `qrcode` varchar(500) NOT NULL, `tempo_code` varchar(255) NOT NULL, `situacao` varchar(255) NOT NULL, `email` varchar(255) NOT NULL, `servidor_recebe` varchar(255) NOT NULL, `servidor_envia` varchar(255) NOT NULL, `servidor_confirma` varchar(255) NOT NULL, `qr_quantidade` varchar(255) DEFAULT NULL, `qr_data` varchar(255) NOT NULL, `caminho_vps` varchar(500) NOT NULL, `funcao` varchar(255) NOT NULL, `IA_boas_vindas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `IA_prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `IA_despedida` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `tempo_final` int(255) NOT NULL, `agenda_confirma` longtext, `agenda_cancela` longtext, `agenda_verfica` longtext, `tempo_verifica` int(255) NOT NULL, `solicitar_confirmacao` varchar(255) NOT NULL, `pagamento_cliente` varchar(255) NOT NULL, `id_assinatura` varchar(255) NOT NULL, `vencimento` varchar(255) NOT NULL, `creditos` int(255) NOT NULL, `plano` varchar(255) NOT NULL, `modo_atuante` varchar(255) NOT NULL, `modelo_ia` varchar(255) NOT NULL, `google_cal` varchar(500) NOT NULL, `logo` varchar(255) DEFAULT NULL, `nome_empresa` varchar(255) DEFAULT NULL, `tema` varchar(255) DEFAULT NULL, `numero_bot` varchar(255) NOT NULL, `confirma_prof` varchar(500) DEFAULT NULL, `cancela_prof` varchar(500) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `logs_etiquetas` ( `id` int(11) NOT NULL, `usuario_api` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `login` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `cliente_id` int(11) DEFAULT NULL, `etiquetas` text COLLATE utf8mb4_unicode_ci, `descricao` text COLLATE utf8mb4_unicode_ci, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 CREATE TABLE `mensagens_massa` ( `id` int(11) NOT NULL, `login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Login do usuário da sessão', `usuario_api` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID da API do usuário', `campaign_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome da campanha', `media_type` enum('text','image','video','audio','document') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text', `message_text` text COLLATE utf8mb4_unicode_ci COMMENT 'Texto da mensagem com variáveis', `media_file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Caminho do arquivo de mídia', `media_file_base64` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Arquivo em base64 (para arquivos pequenos)', `media_file_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL do arquivo (para arquivos grandes)', `clientes_ids` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON com IDs dos clientes selecionados', `send_option` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'now', `schedule_datetime` datetime DEFAULT NULL COMMENT 'Data/hora agendada para envio', `interval_seconds` int(11) NOT NULL DEFAULT '300' COMMENT 'Intervalo entre mensagens em segundos', `start_time` time DEFAULT '09:00:00' COMMENT 'Horário de início', `end_time` time DEFAULT '18:00:00' COMMENT 'Horário de fim', `repeat_option` enum('once','daily','weekly','monthly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'once', `days_week` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dias da semana separados por vírgula (0=Dom,1=Seg...)', `usar_ia` tinyint(1) DEFAULT '0' COMMENT 'Se deve usar IA para reescrever', `status` enum('pendente','processando','pausada','concluida','erro','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente', `total_clientes` int(11) NOT NULL DEFAULT '0' COMMENT 'Total de clientes para enviar', `enviados` int(11) NOT NULL DEFAULT '0' COMMENT 'Quantidade já enviada', `erros` int(11) NOT NULL DEFAULT '0' COMMENT 'Quantidade com erro', `ultimo_envio` datetime DEFAULT NULL COMMENT 'Data/hora do último envio', `proximo_envio` datetime DEFAULT NULL COMMENT 'Data/hora do próximo cliente', `log_erros` text COLLATE utf8mb4_unicode_ci COMMENT 'Log de erros ocorridos', `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 CREATE TABLE `mensagens_massa_envios` ( `id` int(11) NOT NULL, `mensagem_massa_id` int(11) NOT NULL, `cliente_id` int(11) NOT NULL, `cliente_nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `cliente_telefone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `status` enum('pendente','enviado','erro','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente', `tentativas` int(11) NOT NULL DEFAULT '0', `erro_detalhes` text COLLATE utf8mb4_unicode_ci, `enviado_em` datetime DEFAULT NULL, `response_api` text COLLATE utf8mb4_unicode_ci COMMENT 'Resposta da API do WhatsApp', `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

 CREATE TABLE `menu` ( `id` int(255) NOT NULL, `menu` varchar(255) DEFAULT NULL, `menu_pagina` varchar(255) DEFAULT NULL, `tipo` varchar(255) DEFAULT NULL, `ordem` varchar(255) DEFAULT NULL, `icone_menu` varchar(255) DEFAULT NULL, `funcao` varchar(255) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 INSERT INTO `menu` (`id`, `menu`, `menu_pagina`, `tipo`, `ordem`, `icone_menu`, `funcao`) VALUES (1, 'Administração', 'config_adm.php', '1', '1', 'feather icon-shield ', 'adm'), (2, 'Chave & Pagamento', 'chave.php', '1', '2', 'fa fa-key', 'adm'), (3, 'QR Code', 'qrcode.php', '1', '3.2', 'fa fa-qrcode', 'adm'), (4, 'Criar Bots', 'criar_bot.php', '1', '4', 'feather icon-plus-circle ', 'adm'), (5, 'Listar Bots', 'listar_bot.php', '1', '5', 'feather icon-list ', 'adm'), (8, 'Estilo da Página', 'estilo_pagina.php', '1', '3', 'feather icon-menu ', 'adm'), (9, 'Criar Menus', 'criar_menus.php', '1', '8', 'feather icon-settings ', 'adm'), (11, 'Instalação', 'config_adm.php', '4', '1', 'feather icon-shield ', 'adm_install'), (12, 'QR Code', 'qrcode.php', '2', '1.4', 'fa fa-qrcode', 'Agendamento,Atendimento'), (13, 'Sair', 'sair.php', '2', '9', 'feather icon-log-out', 'adm,Agendamento,Atendimento,prof'), (16, 'Inf Atendimento', 'prompt.php', '2', '1.1', 'feather icon-plus-circle', 'Agendamento'), (18, 'Visualizar Agenda', 'agenda.php', '2', '1.3', 'fa fa-users', 'Agendamento'), (22, 'Config Mensagens', 'msg_config.php', '2', '1.7', 'fa fa-user', 'Agendamento'), (23, 'Clientes ', 'clientes.php', '2', '1.4', 'fa fa-users', 'Agendamento,Atendimento'), (24, 'Ativação', 'perfil.php', '2', '1.8', 'feather icon-shield', 'Agendamento,Atendimento'), (25, 'Agendamentos', 'agendamentos.php', '2', '1.0', 'fa fa-calendar', 'Agendamento'), (26, 'Início', 'modo.php', '2', '1', 'feather icon-home', 'Agendamento'), (28, 'Datas especiais', 'datas_especiais.php', '2', '1.3', 'fa fa-calendar', 'Agendamento'), (29, 'Módulos', 'modulos.php', '1', '1.2', 'fa fa-upload', 'adm'), (30, 'Módulos baixados', 'modulos_instalado.php', '1', '1', 'fa fa-download', 'adm'), (31, 'Planos', 'planos.php', '1', '6', 'fa fa-credit-card', 'adm'), (32, 'IA config', 'ia_config.php', '1', '7', 'fa fa-dashboard', 'adm'), (33, 'Leads', 'leads.php', '1', '8', 'fa fa-envelope', 'adm'), (35, 'Módulos créditos', 'modulos_creditos.php', '1', '2', 'fa fa-money', 'adm'), (36, 'Inf Atendimento', 'prompt.php', '2', '1.1', 'feather icon-plus-circle', 'Atendimento'), (37, 'Início', 'modo.php', '2', '1', 'feather icon-home', 'Atendimento'), (38, 'Financeiro', 'financeiro.php', '2', '1', 'fa fa-money', 'Agendamento'), (39, 'Relatório', 'relatorio.php', '2', '1.1', 'feather icon-bar-chart', 'Agendamento'), (41, 'Disparo msg', 'msg_massa2.php', '2', '1.8', 'fa fa-envelope', 'Agendamento,Atendimento'), (42, 'Financeiro', 'financeiro_prof.php', '5', '1', 'fa fa-money', 'prof'), (43, 'Relatório', 'relatorio_prof.php', '5', '2', 'feather icon-list', 'prof'), (44, 'Visualizar Agenda', 'agenda_porf.php', '5', '3', 'fa fa-users', 'prof'), (45, 'Integração', 'integracao.php', '5', '4', 'feather icon-plus-circle', 'Agendamento,prof'), (46, 'Senha', 'senha.php', '5', '5', 'fa fa-lock', 'adm,Agendamento,Atendimento,prof'), (47, 'Bloqueados', 'lista_negra.php', '2', '3', 'feather icon-list', 'Agendamento,Atendimento');

 CREATE TABLE `modulos_baixados` ( `id` int(11) NOT NULL, `nome_modulo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `caminho` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `usuario` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `data_hora` datetime DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `modulos_lista` ( `id` int(255) NOT NULL, `nome_modulo` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `versao` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date_install` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date_down` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `creditos` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `tipo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 INSERT INTO `modulos_lista` (`id`, `nome_modulo`, `versao`, `date_install`, `date_down`, `creditos`, `tipo`) VALUES (4, 'Agendamento', '3.0', '2025-07-23 12:35:18', '2025-07-23 12:35:18', NULL, '1'), (5, 'Atendimento', '3.0', '2025-07-23 12:35:18', '2025-07-23 12:35:18', NULL, '1');

 CREATE TABLE `modulo_atual` ( `id` int(255) NOT NULL, `nome_modulo` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `versao` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date_install` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date_down` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `tipo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 INSERT INTO `modulo_atual` (`id`, `nome_modulo`, `versao`, `date_install`, `date_down`, `tipo`) VALUES (1, 'Agendamento e Atendimento', '3.0', NULL, '2025-07-23 12:35:18', NULL);

 CREATE TABLE `pagamentos` ( `id` int(11) NOT NULL, `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `data_pagamento` datetime NOT NULL, `id_pedido` varchar(100) COLLATE utf8_unicode_ci NOT NULL, `nome_produto` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `nome_completo` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `telefone` varchar(20) COLLATE utf8_unicode_ci NOT NULL, `cpf` varchar(14) COLLATE utf8_unicode_ci NOT NULL, `status` varchar(50) COLLATE utf8_unicode_ci NOT NULL, `id_assinatura` varchar(100) COLLATE utf8_unicode_ci NOT NULL, `criado_em` varchar(255) COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `pagamentos_status` ( `id` int(11) NOT NULL, `usuario_api` varchar(50) COLLATE utf8_unicode_ci NOT NULL, `data_pagamento` date NOT NULL, `valor` decimal(10,2) NOT NULL, `status_pagamento` enum('Pago','Pendente') COLLATE utf8_unicode_ci NOT NULL, `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `planos_clientes` ( `id` int(255) NOT NULL, `nome_plano` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `nome_modulo` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `date` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `tipo` int(255) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 INSERT INTO `planos_clientes` (`id`, `nome_plano`, `nome_modulo`, `date`, `tipo`) VALUES (1, 'plano1', 'Agendamento', '2025-07-18 22:48:25', 1), (5, 'plano2', 'Agendamento', '2025-07-21 11:22:46', 1), (6, 'plano2', 'Atendimento', '2025-07-21 11:22:49', 1), (10, 'plano2', 'Credito 100', '2025-08-08 14:32:38', 0), (11, 'plano3', 'credito 500', '2025-08-08 14:32:47', 0), (12, 'plano3', 'Agendamento', '2025-08-08 14:32:51', 1), (14, 'plano1', 'Ilimitado', '2025-08-11 22:22:41', 0), (15, 'plano1', 'Atendimento', '2025-08-11 23:44:31', 1), (16, 'plano3', 'Atendimento', '2025-08-15 11:34:42', 1);

 CREATE TABLE `planos_features` ( `id` int(11) NOT NULL, `id_plano` int(11) NOT NULL, `feature` text NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 INSERT INTO `planos_features` (`id`, `id_plano`, `feature`) VALUES (1, 1, 'Atendimento humanizado com inteligência artificial.'), (2, 1, 'Reconhecimento de texto e áudio.'), (3, 1, 'Automação básica de processos de vendas.'), (4, 1, 'Suporte para até 5 produtos.'), (5, 1, 'Cadastro de até 100 clientes.'), (7, 2, 'Atendimento humanizado com inteligência artificial.'), (8, 2, 'Reconhecimento de texto, áudio e imagens.'), (9, 2, 'Automação completa de processos de vendas.'), (10, 2, 'Suporte para produtos ilimitados.'), (11, 2, 'Cadastro de clientes ilimitados.'), (12, 2, 'Sistema de acompanhamento e análise de conversões.'), (14, 3, 'Atendimento humanizado com inteligência artificial avançada.'), (15, 3, 'Reconhecimento de texto, áudio, imagens e contatos.'), (16, 3, 'Automação completa de processos de vendas e pós-venda.'), (17, 3, 'Suporte para produtos ilimitados com categorização.'), (18, 3, 'Cadastro de clientes ilimitados com segmentação.'), (19, 3, 'Sistema avançado de análise de métricas e conversões.'), (20, 3, 'Integração com múltiplos sistemas e APIs.'), (21, 3, 'Suporte técnico 24/7 e treinamento da equipe.'), (22, 1, 'Reconhecimento de áudio');

 CREATE TABLE `planos_online` ( `id` int(11) NOT NULL, `titulo` varchar(100) NOT NULL, `preco` decimal(10,2) NOT NULL, `icone` varchar(255) NOT NULL, `link_pagamento` varchar(255) NOT NULL, `code_pag` char(3) NOT NULL, `ativo` tinyint(1) NOT NULL DEFAULT '1' ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 INSERT INTO `planos_online` (`id`, `titulo`, `preco`, `icone`, `link_pagamento`, `code_pag`, `ativo`) VALUES (1, 'Popular', 97.00, '/login/painel/logo_basico.png', 'https://seusite.com/pagar?plano=premium', '1A3', 1), (2, 'Plano Premium', 197.00, '/login/painel/logo_premium.png', 'https://seusite.com/pagar?plano=premium', '2B4', 1), (3, 'Premium', 497.00, '/login/painel/logo_enterprise.png', 'https://pay.kiwify.com.br/8JN3lI9', '3C5', 1);

 CREATE TABLE `profissional` ( `id` int(255) NOT NULL, `usuario_api` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `profissional_nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `profissional_cargo` varchar(255) COLLATE utf8_unicode_ci NOT NULL, `telefone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `codigo_pais` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

 CREATE TABLE `profissional_servicos` ( `id` int(11) NOT NULL, `profissional_id` int(11) NOT NULL, `servico_id` int(11) NOT NULL, `tempo_execucao_minutos` int(11) DEFAULT NULL, `valor_profissional` decimal(10,2) DEFAULT NULL, `ativo` tinyint(1) DEFAULT '1', `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `servicos` ( `id` int(11) NOT NULL, `nome` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, `descricao` text CHARACTER SET utf8 COLLATE utf8_unicode_ci, `duracao_minutos` int(11) NOT NULL DEFAULT '30', `valor` decimal(10,2) NOT NULL, `categoria` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, `ativo` tinyint(1) DEFAULT '1', `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `login` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `view_estatisticas_bloqueios` ( `usuario_api` varchar(255) ,`total_bloqueios` bigint(21) ,`bloqueios_ativos` bigint(21) ,`bloqueios_inativos` bigint(21) ,`ultimo_bloqueio` datetime ,`total_tentativas` decimal(32,0) );

 CREATE TABLE `view_lista_negra_ativa` ( `id` int(255) ,`nome` varchar(255) ,`telefone` varchar(255) ,`motivo_bloqueio` varchar(500) ,`data_bloqueio` datetime ,`tentativas_contato` int(11) ,`ultima_tentativa` datetime ,`observacoes` text );

 DROP TABLE IF EXISTS `view_estatisticas_bloqueios`;

 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_estatisticas_bloqueios` AS SELECT `lista_negra`.`usuario_api` AS `usuario_api`, count(0) AS `total_bloqueios`, count((case when (`lista_negra`.`status` = 'ativo') then 1 end)) AS `bloqueios_ativos`, count((case when (`lista_negra`.`status` = 'inativo') then 1 end)) AS `bloqueios_inativos`, max(`lista_negra`.`data_bloqueio`) AS `ultimo_bloqueio`, sum(`lista_negra`.`tentativas_contato`) AS `total_tentativas` FROM `lista_negra` GROUP BY `lista_negra`.`usuario_api` ;

 DROP TABLE IF EXISTS `view_lista_negra_ativa`;

 CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_lista_negra_ativa` AS SELECT `lista_negra`.`id` AS `id`, `lista_negra`.`nome` AS `nome`, `lista_negra`.`telefone` AS `telefone`, `lista_negra`.`motivo_bloqueio` AS `motivo_bloqueio`, `lista_negra`.`data_bloqueio` AS `data_bloqueio`, `lista_negra`.`tentativas_contato` AS `tentativas_contato`, `lista_negra`.`ultima_tentativa` AS `ultima_tentativa`, `lista_negra`.`observacoes` AS `observacoes` FROM `lista_negra` WHERE (`lista_negra`.`status` = 'ativo') ORDER BY `lista_negra`.`data_bloqueio` DESC ;

 ALTER TABLE `agendamento` ADD PRIMARY KEY (`id`);

 ALTER TABLE `agenda_padrao` ADD PRIMARY KEY (`id`);

 ALTER TABLE `chave` ADD PRIMARY KEY (`id`);

 ALTER TABLE `chave_ia_geral` ADD PRIMARY KEY (`id`);

 ALTER TABLE `clientes` ADD PRIMARY KEY (`id`);

 ALTER TABLE `config` ADD PRIMARY KEY (`id`);

 ALTER TABLE `datas_excluidas` ADD PRIMARY KEY (`id`);

 ALTER TABLE `email_config` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `unique_login` (`login`);

 ALTER TABLE `envio` ADD PRIMARY KEY (`id`);

 ALTER TABLE `especialidades` ADD PRIMARY KEY (`id`);

 ALTER TABLE `estilo` ADD PRIMARY KEY (`id`);

 ALTER TABLE `funcao` ADD PRIMARY KEY (`id`);

 ALTER TABLE `gerenciador` ADD PRIMARY KEY (`id`);

 ALTER TABLE `horarios_profissional` ADD PRIMARY KEY (`id`);

 ALTER TABLE `horarios_servico` ADD PRIMARY KEY (`id`);

 ALTER TABLE `ia_historico` ADD PRIMARY KEY (`id`);

 ALTER TABLE `intervalos_profissional` ADD PRIMARY KEY (`id`);

 ALTER TABLE `leads` ADD PRIMARY KEY (`id`);

 ALTER TABLE `lista_negra` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `idx_telefone_usuario_ativo` (`telefone`,`usuario_api`,`status`), ADD KEY `idx_telefone` (`telefone`), ADD KEY `idx_usuario_api` (`usuario_api`), ADD KEY `idx_status` (`status`), ADD KEY `idx_data_bloqueio` (`data_bloqueio`), ADD KEY `idx_usuario_status_data` (`usuario_api`,`status`,`data_bloqueio`);

 ALTER TABLE `lista_negra_log` ADD PRIMARY KEY (`id`), ADD KEY `idx_lista_negra_id` (`lista_negra_id`), ADD KEY `idx_data_log` (`data_log`);

 ALTER TABLE `login` ADD PRIMARY KEY (`id`);

 ALTER TABLE `logs_etiquetas` ADD PRIMARY KEY (`id`), ADD KEY `idx_usuario_api` (`usuario_api`), ADD KEY `idx_cliente_id` (`cliente_id`), ADD KEY `idx_created_at` (`created_at`);

 ALTER TABLE `mensagens_massa` ADD PRIMARY KEY (`id`), ADD KEY `idx_login` (`login`), ADD KEY `idx_usuario_api` (`usuario_api`), ADD KEY `idx_status` (`status`), ADD KEY `idx_schedule` (`schedule_datetime`), ADD KEY `idx_proximo_envio` (`proximo_envio`), ADD KEY `idx_send_option` (`send_option`);

 ALTER TABLE `mensagens_massa_envios` ADD PRIMARY KEY (`id`), ADD KEY `idx_mensagem_massa` (`mensagem_massa_id`), ADD KEY `idx_cliente` (`cliente_id`), ADD KEY `idx_status` (`status`);

 ALTER TABLE `menu` ADD PRIMARY KEY (`id`);

 ALTER TABLE `modulos_baixados` ADD PRIMARY KEY (`id`);

 ALTER TABLE `modulos_lista` ADD PRIMARY KEY (`id`);

 ALTER TABLE `modulo_atual` ADD PRIMARY KEY (`id`);

 ALTER TABLE `pagamentos` ADD PRIMARY KEY (`id`);

 ALTER TABLE `pagamentos_status` ADD PRIMARY KEY (`id`);

 ALTER TABLE `planos_clientes` ADD PRIMARY KEY (`id`);

 ALTER TABLE `planos_features` ADD PRIMARY KEY (`id`), ADD KEY `id_plano` (`id_plano`);

 ALTER TABLE `planos_online` ADD PRIMARY KEY (`id`);

 ALTER TABLE `profissional` ADD PRIMARY KEY (`id`);

 ALTER TABLE `profissional_servicos` ADD PRIMARY KEY (`id`);

 ALTER TABLE `servicos` ADD PRIMARY KEY (`id`);

 ALTER TABLE `agendamento` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=377;

 ALTER TABLE `agenda_padrao` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=821;

 ALTER TABLE `chave` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

 ALTER TABLE `chave_ia_geral` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `clientes` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `config` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

 ALTER TABLE `datas_excluidas` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `email_config` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

 ALTER TABLE `envio` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `especialidades` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `estilo` MODIFY `id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

 ALTER TABLE `funcao` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `gerenciador` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=430;

 ALTER TABLE `horarios_profissional` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `horarios_servico` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `ia_historico` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5827;

 ALTER TABLE `intervalos_profissional` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `leads` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

 ALTER TABLE `lista_negra` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

 ALTER TABLE `lista_negra_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `login` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `logs_etiquetas` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `mensagens_massa` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

 ALTER TABLE `mensagens_massa_envios` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `menu` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

 ALTER TABLE `modulos_baixados` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `modulos_lista` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

 ALTER TABLE `modulo_atual` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

 ALTER TABLE `pagamentos` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

 ALTER TABLE `pagamentos_status` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `planos_clientes` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

 ALTER TABLE `planos_features` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

 ALTER TABLE `planos_online` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

 ALTER TABLE `profissional` MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

 ALTER TABLE `profissional_servicos` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `servicos` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

 ALTER TABLE `mensagens_massa_envios` ADD CONSTRAINT `mensagens_massa_envios_ibfk_1` FOREIGN KEY (`mensagem_massa_id`) REFERENCES `mensagens_massa` (`id`) ON DELETE CASCADE;

 COMMIT;
