# Plano de Refactor e Modernizacao do Sistema Clinico

## Objetivo

Transformar o sistema atual num ERP clinico moderno, orientado por modulos de negocio, com controlo de acessos por processo, rastreabilidade, farmácia integrada, laboratorio, medicina dentaria e operacao multi-area dentro da mesma clinica.

Este plano usa como base o esquema novo ja existente em [database/schema.sql](../database/schema.sql), aproveitando tabelas de perfis, permissoes, pedidos de exames, resultados, stock e vendas de farmácia.

## Principios do Sistema Novo

1. O acesso nao deve ser controlado apenas por menu; deve ser controlado por permissao de acao.
2. Cada modulo deve ter estados de processo claros.
3. Toda operacao critica deve guardar `created_by`, `updated_by`, clinica, data e referencia do processo.
4. Os modulos clinicos e operacionais devem conversar entre si sem duplicar informacao.
5. O frontend deve ser moderno, mas guiado por regras de negocio robustas.

## Modulos-Alvo

### 1. Atendimento e Cadastro

- cadastro e atualizacao de paciente
- historico de contactos e documentos
- seguradoras, convenios e responsavel financeiro
- elegibilidade do paciente por unidade/clinica

### 2. Fluxo Clinico Geral

- triagem
- agendamento
- consulta
- evolucao clinica
- prescricoes
- solicitacao de exames
- visualizacao controlada de resultados

### 3. Laboratorio

- rececao do pedido de exame
- fila tecnica por setor
- recolha/processamento
- lancamento de resultado
- validacao tecnica e validacao clinica
- anexo de PDF/imagem/laudo
- restricao por sensibilidade do resultado

### 4. Farmacia

- cadastro de produtos
- lote e validade
- entradas e rececao de mercadoria
- transferencias e movimentos de stock
- inventario e ajustes
- venda no balcao
- venda vinculada a prescricao
- relatorio de ruptura, vencimento e margem

### 5. Medicina Dentaria

- agenda odontologica
- odontograma
- procedimentos dentarios
- evolucao por dente/quadrante
- orcamento e plano de tratamento
- faturacao por ato/procedimento

### 6. Financeiro e Faturacao

- cobranca de consultas, exames, farmacia e odontologia
- caixa e meios de pagamento
- conciliacao de recebimentos
- integracao com faturas e recibos

### 7. Administracao e Governanca

- perfis e permissoes
- menus por perfil
- auditoria
- configuracoes por clinica
- numeradores e documentos

## Matriz Inicial de Perfis

### Diretor / Administrador Geral

- acesso total a configuracao, auditoria, finance, relatorios e governanca
- nao deve alterar resultados laboratoriais tecnicos sem permissao explicita

### Recepcao

- criar e editar paciente
- marcar consulta
- abrir ficha administrativa
- emitir guias e recibos basicos
- nao pode ver resultados sensiveis nem alterar atos clinicos

### Medico

- consultar agenda
- abrir consulta
- registar evolucao
- prescrever
- solicitar analises
- ver resultados autorizados dos seus pacientes
- nao pode ajustar stock nem validar entrada de mercadoria

### Enfermeiro / Triagem

- registar sinais vitais
- triagem e encaminhamento
- consultar paciente
- nao pode fechar consulta medica nem emitir resultado laboratorial

### Tecnico de Laboratorio

- ver pedidos em fila
- registar processamento
- lancar resultado
- anexar laudo
- nao pode faturar, dispensar farmacia ou apagar resultado validado

### Validador de Laboratorio / Bioquimico

- rever resultado tecnico
- validar ou rejeitar resultado
- libertar para visualizacao clinica

### Farmaceutico / Operador de Venda

- vender no balcao
- vender por prescricao
- consultar stock disponivel
- nao pode fazer ajuste estrutural de stock sem permissao de inventario

### Gestor de Stock / Armazem

- criar entradas
- receber compra
- ajustar stock
- controlar lotes e validade
- transferir entre armazens
- nao pode alterar prontuario clinico

### Dentista

- agenda odontologica
- odontograma
- procedimentos
- prescricoes e exames ligados ao atendimento dentario

### Gerente Financeiro

- ver faturacao
- caixa
- recebimentos
- relatorios economicos
- nao deve ver dados clinicos sensiveis alem do necessario

## Permissoes por Acao

As permissoes devem ser registadas em `permissions` e associadas a `roles` pelas tabelas `role_user` e `permission_role`.

### Permissoes Clinicas

- `patients.view`
- `patients.create`
- `patients.update`
- `triage.create`
- `appointments.manage`
- `consultations.open`
- `consultations.close`
- `prescriptions.create`
- `exam_requests.create`
- `exam_results.view`
- `exam_results.view_sensitive`

### Permissoes de Laboratorio

- `lab.queue.view`
- `lab.results.create`
- `lab.results.update`
- `lab.results.validate`
- `lab.results.publish`

### Permissoes de Farmacia

- `pharmacy.products.manage`
- `pharmacy.sales.create`
- `pharmacy.sales.cancel`
- `stock.view`
- `stock.receive`
- `stock.adjust`
- `stock.transfer`
- `stock.inventory.execute`

### Permissoes de Odontologia

- `dentistry.chart.view`
- `dentistry.chart.update`
- `dentistry.procedures.create`
- `dentistry.treatment_plan.manage`

### Permissoes Administrativas

- `roles.manage`
- `permissions.manage`
- `menus.manage`
- `institution.manage`
- `audit.view`
- `reports.executive.view`

## Fluxo Clinico-Alvo

1. Recepcao cria ou seleciona paciente.
2. Paciente entra em triagem.
3. Triagem atualiza sinais vitais e prioridade.
4. Medico abre consulta.
5. Medico registra anamnese, exame fisico e diagnostico.
6. Medico pode emitir prescricao, pedir exames ou encaminhar.
7. Pedido de exame entra na fila do laboratorio.
8. Laboratorio processa e lanca resultado.
9. Validador libera o resultado.
10. O medico visualiza resultado conforme permissao e vinculo ao caso.

## Fluxo de Laboratorio-Alvo

### Estados sugeridos do pedido

- `requested`
- `received`
- `in_processing`
- `result_entered`
- `validated`
- `published`
- `cancelled`

### Estados sugeridos do resultado

- `draft`
- `technical_review`
- `validated`
- `published`
- `reopened`

### Regras importantes

- resultado nao deve ficar visivel ao clinico antes da validacao
- resultados sensiveis devem exigir permissao adicional
- cada alteracao apos validacao deve ficar auditada

## Fluxo de Farmacia-Alvo

### Entradas e stock

1. Compra ou rececao de mercadoria.
2. Entrada por lote e validade.
3. Movimento em `stock_movements`.
4. Atualizacao de `stock_items` e `stock_batches`.

### Venda

1. Operador escolhe paciente ou venda avulsa.
2. Sistema valida disponibilidade por lote.
3. Sistema baixa stock pelo criterio FEFO quando houver validade.
4. Gera `pharmacy_sales` e `pharmacy_sale_items`.
5. Cria movimento de saida em `stock_movements`.

### Controlo gerencial

- ruptura de stock
- itens abaixo do minimo
- lotes a expirar
- divergencia de inventario
- top vendas e margem

## Fluxo de Medicina Dentaria-Alvo

O esquema atual ainda nao mostra tabelas proprias para odontologia. Este modulo precisa nascer com entidade propria, nao como adaptacao generica da consulta.

### Entidades sugeridas

- `dental_records`
- `dental_chart_entries`
- `dental_procedures`
- `dental_treatment_plans`
- `dental_appointments`
- `dental_invoices`

### Regras

- um atendimento dentario pode ter varios atos por sessao
- o historico deve permitir leitura por dente/quadrante
- integracao com prescricoes e exames deve ser opcional

## Gaps Atuais Identificados

### O que ja existe no esquema novo

- papeis e permissoes
- pacientes, consultas e pedidos de exame
- resultados laboratoriais
- stock, lotes, movimentos e vendas de farmacia

### O que ainda precisa de desenho/implementacao

- servico de autorizacao por permissao de acao na aplicacao
- estados formais dos processos clinicos e laboratoriais
- integracao entre prescricao e dispensa em farmacia
- auditoria funcional por modulo
- modulo especifico de medicina dentaria
- UI moderna orientada por workflow e nao por paginas avulsas

## Ordem Recomendada de Refactor

### Fase 1. Fundacao de Seguranca e Arquitetura

- criar middleware/policy de permissao
- padronizar sessao, usuario atual, clinica atual e contexto de acesso
- separar controllers por dominio
- criar servicos de negocio para fluxos criticos

### Fase 2. Clinico Base

- pacientes
- agenda
- triagem
- consulta
- prescricoes

### Fase 3. Laboratorio

- pedido de exame
- fila tecnica
- resultado
- validacao e publicacao

### Fase 4. Farmacia e Stock

- produtos
- lotes
- entradas
- movimentos
- venda e dispensa

### Fase 5. Odontologia

- modelo de dados proprio
- agenda propria
- odontograma
- faturacao por procedimento

### Fase 6. Financeiro, BI e Auditoria

- relatorios operacionais
- auditoria por processo
- paines executivos

## Diretrizes de Implementacao

1. Evitar colocar regra de negocio em view ou em route bootstrap.
2. Controlar permissao no backend, nao apenas no frontend.
3. Cada modulo deve ter casos de uso claros e endpoints previsiveis.
4. Cada transacao de stock e resultado clinico deve ser auditavel.
5. Refactorar por fatias completas de negocio, nao por ficheiros isolados.

## Primeira Entrega Recomendada

Para ganhar tracao sem quebrar o sistema inteiro, a primeira entrega do refactor deve ser:

1. Autorizacao por roles e permissions.
2. Dashboard por perfil.
3. Pacientes + consulta base.
4. Pedido de exame + resultado.
5. Produtos + stock + venda de farmacia.

Depois disso, avancar para odontologia e financeiro integrado.