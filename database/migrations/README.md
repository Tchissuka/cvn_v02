## Migracao legado -> v2

Este pacote migra os dados essenciais de `cvnsu_bd` para `cvnsu_vbd` com saneamento minimo e preservacao dos identificadores legados onde isso ajuda a manter referencias.

### O que entra neste primeiro pacote

- clinicas a partir de `institutions`
- utilizadores a partir de `users`
- perfis a partir de `user_type`
- ligacao utilizador-perfil em `role_user`
- grupos de menu, menus principais, submenus e acessos pessoais
- pessoas a partir de `personal` + `personal_address`
- funcionarios a partir de `employees`
- pacientes a partir de `patients`
- numeradores a partir de `general_numerator`
- categorias e produtos do catalogo legado

### Melhorias aplicadas na carga

- cria clinicas tecnicas para registos sem clinica e para catalogo global legado
- gera emails tecnicos para utilizadores antigos que nao tinham email real
- separa `full_name` em `first_name` e `last_name`
- normaliza genero para `male` e `female`
- preserva grupos por perfil e tambem acessos diretos por utilizador
- preenche numeros de processo em falta com identificadores estaveis

### Execucao local

No PowerShell:

```powershell
Set-Location c:\xampp\htdocs\cvn_v02\database\migrations
.\run-legacy-migration.ps1
```

Se quiser outro nome de base:

```powershell
.\run-legacy-migration.ps1 -SourceDb cvnsu_bd -TargetDb cvnsu_vbd
```

### Observacoes

- o target esperado e uma base v2 vazia ou quase vazia
- o script e desenhado para rerun sem duplicar o essencial, usando PKs legadas e `INSERT IGNORE`/`ON DUPLICATE KEY UPDATE`
- areas clinicas avancadas, faturacao historica, farmacia operacional e tesouraria ainda precisam de uma fase 2 especifica

### Fundacao RBAC na base legacy ativa

Enquanto a aplicacao ainda estiver ligada a `cvnsu_bd`, pode preparar a base ativa para `roles + permissions` com:

```powershell
php c:\xampp\htdocs\cvn_v02\database\migrations\run_legacy_rbac_foundation.php
```

O runner:

- cria `roles`, `permissions`, `role_user` e `permission_role` na base ativa
- carrega um catalogo inicial de permissoes da fase 1
- converte `user_type` legado em roles `legacy-group-*`
- associa utilizadores aos roles com base em `users.tipoUtili`

Por seguranca, a tabela `permission_role` fica vazia ate a matriz final de acessos ser aprovada.

### Fundacao de faturacao/prestacoes na base legacy ativa

Para ativar rascunhos de fatura, itens de fatura e registo de pagamentos/prestacoes em `cvnsu_bd`, execute:

```powershell
php c:\xampp\htdocs\cvn_v02\database\migrations\run_legacy_billing_foundation.php
```

O runner cria, se ainda nao existirem:

- `invoices`
- `invoice_items`
- `payments`

Isto permite que o modulo clinico consulte saldo em aberto do paciente e abra rascunhos de faturacao diretamente no balcão clinico-financeiro.

### Verificacao

Depois da carga, valide o resultado com:

```powershell
php c:\xampp\htdocs\cvn_v02\database\migrations\check_migration.php
```

### Saneamento de pacientes sem clinica

Para reatribuir pacientes que ficaram na clinica tecnica `900000001`, execute:

```powershell
php c:\xampp\htdocs\cvn_v02\database\migrations\run_post_migration_patient_clinic_resolution.php
php c:\xampp\htdocs\cvn_v02\database\migrations\check_patient_clinic_resolution.php
```

Regras automaticas aplicadas, por ordem:

- instituicao do funcionario com o mesmo `person_id`
- instituicao do utilizador com o mesmo `person_id`
- instituicao do utilizador que criou a consulta antiga
- instituicao do medico da consulta antiga
- instituicao do tecnico de triagem da consulta antiga

O saneamento so aplica quando a regra aponta para uma unica clinica no mesmo nivel de prioridade.