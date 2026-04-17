<?php

namespace Source\App;

use Source\Models\Billing\Invoice;
use Source\Models\Users\AuditLog;

class Fiscal extends AppLauncher
{
    public function overview(array $data): void
    {
        $this->documents($data);
    }

    public function documents(array $data): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $payload = $_POST;

            if (($payload['action'] ?? null) === 'delete_draft_invoice') {
                $this->handleDraftInvoiceDelete($payload);
                return;
            }
        }

        $this->authorizeAny(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage']);

        $search = filter_input(INPUT_GET, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        $selectedInvoiceId = filter_input(INPUT_GET, 'invoice_id', FILTER_VALIDATE_INT) ?: null;

        $invoiceModel = new Invoice();
        $summary = $invoiceModel->summarizeByClinic($this->clinicId);
        $invoices = $invoiceModel->recentByClinic($this->clinicId, 15, $search);
        $selectedInvoice = $selectedInvoiceId ? $invoiceModel->findByIdInClinic($selectedInvoiceId, $this->clinicId) : null;

        if (!$selectedInvoice && !$selectedInvoiceId && !empty($invoices)) {
            $selectedInvoice = $invoices[0];
        }

        $head = $this->seo->render(
            'Fiscal | ' . CONF_SITE_NAME,
            'Faturação, documentos fiscais e posição financeira da clínica',
            url('/fiscal/overview'),
            theme('assets/images/logo.png'),
            false
        );

        echo $this->view->render('dashboard/fiscal/overview', [
            'head' => $head,
            'summary' => $summary,
            'invoices' => $invoices,
            'selectedInvoice' => $selectedInvoice,
            'search' => $search,
            'billingReady' => $invoiceModel->billingTablesReady(),
            'scope' => 'documents'
        ]);
    }

    public function saft(array $data): void
    {
        $this->authorizeAny(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage']);

        $this->renderPlaceholderPage(
            '/fiscal/saft',
            'SAF-T AO',
            'Página placeholder para exportação e controlo do SAF-T AO.',
            'SAF-T AO',
            'Esta área fica reservada à geração, validação e conferência do ficheiro fiscal normalizado antes da submissão ou do arquivo operativo.',
            [
                'Geração do SAF-T AO por período.',
                'Validação estrutural antes de exportação.',
                'Ligação futura com séries, certificados e auditoria.'
            ]
        );
    }

    public function series(array $data): void
    {
        $this->authorizeAny(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage']);

        $this->renderPlaceholderPage(
            '/fiscal/series',
            'Séries Fiscais',
            'Página placeholder para gestão de séries fiscais.',
            'Séries Fiscais',
            'Aqui ficará o controlo de séries documentais, sequência numérica, estado operacional e ativação de séries fiscais por tipo de documento.',
            [
                'Configuração e leitura das séries ativas.',
                'Conferência de sequência documental.',
                'Base futura para integração com emissão fiscal.'
            ]
        );
    }

    public function certificates(array $data): void
    {
        $this->authorizeAny(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage']);

        $this->renderPlaceholderPage(
            '/fiscal/certificates',
            'Certificados Digitais',
            'Página placeholder para certificados digitais do módulo fiscal.',
            'Certificados Digitais',
            'Esta área vai concentrar certificados, validade, estado de assinatura e parâmetros de confiança do ambiente fiscal eletrónico.',
            [
                'Cadastro e validade dos certificados digitais.',
                'Leitura operacional de assinatura eletrónica.',
                'Integração futura com emissão e SAF-T AO.'
            ]
        );
    }

    public function hashChain(array $data): void
    {
        $this->authorizeAny(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage']);

        $this->renderPlaceholderPage(
            '/fiscal/hash-chain',
            'Hash Chain',
            'Página placeholder para integridade e encadeamento fiscal.',
            'Hash Chain',
            'Aqui ficará a leitura do encadeamento dos documentos, verificação de integridade e conferência operacional da hash chain fiscal.',
            [
                'Conferência da sequência criptográfica dos documentos.',
                'Leitura de integridade e consistência operacional.',
                'Base futura para auditoria e validação fiscal.'
            ]
        );
    }

    public function audit(array $data): void
    {
        $this->authorizeAny(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage']);

        $this->renderPlaceholderPage(
            '/fiscal/audit',
            'Auditoria Fiscal',
            'Página placeholder para leitura de risco e consistência fiscal.',
            'Auditoria Fiscal',
            'Esta área vai reunir alertas, inconsistências documentais, desvios operacionais e rastreabilidade do ciclo fiscal da clínica.',
            [
                'Alertas de inconsistência documental.',
                'Leitura de risco fiscal e operacional.',
                'Rastreabilidade futura por documento, série e utilizador.'
            ]
        );
    }

    public function reports(array $data): void
    {
        $this->authorizeAny(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage']);

        $this->renderPlaceholderPage(
            '/fiscal/reports',
            'Relatórios Fiscais',
            'Página placeholder para relatórios fiscais.',
            'Relatórios Fiscais',
            'Aqui ficará a síntese fiscal por período, documento, série e situação, preparada para controlo interno e fecho periódico.',
            [
                'Relatórios fiscais por período e série.',
                'Conferência de faturação, cobrança e posição em aberto.',
                'Saída futura para análise interna e fecho documental.'
            ]
        );
    }

    private function handleDraftInvoiceDelete(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!csrf_verify($data)) {
            echo json_encode([
                'error' => true,
                'message' => 'Requisição inválida. Atualize a página e tente novamente.'
            ]);
            return;
        }

        $invoiceId = (int)($data['invoice_id'] ?? 0);
        $invoice = new Invoice();

        if ($invoiceId <= 0 || !$invoice->deleteDraftCascade($this->clinicId, $invoiceId)) {
            echo json_encode([
                'error' => true,
                'message' => $invoice->message()->getText() ?: 'Não foi possível eliminar o rascunho selecionado.'
            ]);
            return;
        }

        if ($this->clinicId > 0 && $this->user) {
            (new AuditLog())
                ->bootstrap(
                    $this->clinicId,
                    'invoices',
                    $invoiceId,
                    'draft_delete',
                    (int)$this->user->Id,
                    null,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                )
                ->register();
        }

        echo json_encode([
            'success' => true,
            'message' => $invoice->message()->getText() ?: 'Rascunho eliminado com sucesso.',
            'redirect' => url('/fiscal/overview')
        ]);
    }
}