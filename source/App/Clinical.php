<?php

namespace Source\App;

use Source\Models\Billing\Invoice;
use Source\Models\Billing\InvoiceItem;
use Source\Models\Billing\Payment;
use Source\Models\Billing\Service;
use Source\Models\Clinical\Appointment;
use Source\Models\Clinical\Doctor;
use Source\Models\Clinical\Patient;
use Source\Models\Users\AuditLog;

class Clinical extends AppLauncher
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Pacientes (submenu do módulo Clínico)
     * GET  -> lista de pacientes
     * POST -> criação/edição (JSON)
     * Use nas rotas/submenus como: Clinical@patients
     */
    public function patients(array $data): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $payload = $_POST;

            if (($payload['action'] ?? null) === 'delete_draft_invoice') {
                $this->handleDraftInvoiceDelete($payload);
                return;
            }

            if (($payload['action'] ?? null) === 'create_draft_invoice') {
                $this->handleDraftInvoice($payload);
                return;
            }

            $this->handlePatientSave($payload);
            return;
        }

        $page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT) ?: 1;
        $search = filter_input(INPUT_GET, "s", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        $selectedPatientId = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT) ?: null;

        $patientModel = new Patient();
        $result = $patientModel->paginateRegistryByClinic($this->clinicId, $page, 20, $search);

        $selectedPatient = null;
        if ($selectedPatientId) {
            $selectedPatient = $patientModel->findRegistryByIdInClinic($selectedPatientId, $this->clinicId);
        } elseif ($search && count($result['data']) === 1) {
            $selectedPatient = $result['data'][0];
            $selectedPatientId = (int)($selectedPatient->Id ?? 0);
        }

        $financialSummary = null;
        $invoiceHistory = [];
        $paymentHistory = [];
        $services = (new Service())->searchByClinic($this->clinicId, '', 24);
        $doctors = (new Doctor())->listActiveByClinic($this->clinicId, null, 24);

        if ($selectedPatientId) {
            $invoiceModel = new Invoice();
            $paymentModel = new Payment();
            $financialSummary = $invoiceModel->summarizeByPatient($this->clinicId, $selectedPatientId);
            $invoiceHistory = $invoiceModel->recentByPatient($this->clinicId, $selectedPatientId, 8);
            $paymentHistory = $paymentModel->recentByPatient($this->clinicId, $selectedPatientId, 8);
        }

        $head = $this->seo->render(
            "Balcão | " . CONF_SITE_NAME,
            "Balcão operativo para localizar pacientes, abrir rascunhos e tratar o fluxo administrativo do atendimento",
            url('/desk/patients'),
            theme("assets/images/logo.png"),
            false
        );

        echo $this->view->render("dashboard/clinical/patients", [
            "head" => $head,
            "patients" => $result["data"],
            "pagination" => $result,
            "search" => $search,
            "selectedPatient" => $selectedPatient,
            "financialSummary" => $financialSummary,
            "invoiceHistory" => $invoiceHistory,
            "paymentHistory" => $paymentHistory,
            "services" => $services,
            "doctors" => $doctors
        ]);
    }

    public function attendance(array $data): void
    {
        $this->authorizeAny(['patients.view', 'consultations.open', 'appointments.manage']);

        $selectedPatientId = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT) ?: null;
        $selectedPatient = null;

        if ($selectedPatientId) {
            $selectedPatient = (new Patient())->findRegistryByIdInClinic($selectedPatientId, $this->clinicId);
        }

        $head = $this->seo->render(
            'Pacientes | ' . CONF_SITE_NAME,
            'Área clínica orientada ao médico e ao atendimento do paciente durante a consulta',
            url('/clinical/attendance'),
            theme('assets/images/logo.png'),
            false
        );

        echo $this->view->render('dashboard/clinical/attendance', [
            'head' => $head,
            'selectedPatient' => $selectedPatient
        ]);
    }

    public function register(array $data): void
    {
        $this->patients($data);
    }

    public function chart(array $data): void
    {
        $this->authorizeAny(['patients.view', 'consultations.open', 'appointments.manage']);

        $this->renderPlaceholderPage(
            '/clinical/chart',
            'Ficha do Paciente',
            'Espaço reservado para ficha clínica consolidada do paciente, vindo do balcão para o atendimento.',
            'Ficha do Paciente',
            'Esta área vai concentrar antecedentes, dados vitais, documentos clínicos e o contexto que o médico precisa ao assumir o atendimento vindo do balcão.',
            [
                'Receção do paciente encaminhado a partir do Balcão.',
                'Resumo clínico consolidado para leitura rápida do médico.',
                'Ligação futura com consulta, exames e histórico clínico.'
            ]
        );
    }

    public function evolution(array $data): void
    {
        $this->authorizeAny(['patients.view', 'consultations.open', 'appointments.manage']);

        $this->renderPlaceholderPage(
            '/clinical/evolution',
            'Evolução e Conduta',
            'Área placeholder para evolução clínica, conduta médica e plano terapêutico do paciente.',
            'Evolução e Conduta',
            'Esta página fica reservada ao registo progressivo do atendimento, decisão clínica, plano terapêutico e próximos passos definidos pelo médico.',
            [
                'Registo de evolução por consulta ou episódio clínico.',
                'Plano terapêutico e conduta definidos pelo médico.',
                'Integração futura com prescrições, exames e retorno.'
            ]
        );
    }

    public function services(array $data): void
    {
        $this->authorizeAny(['services.manage', 'appointments.manage', 'consultations.open']);

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $search = filter_input(INPUT_GET, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        $selectedServiceId = filter_input(INPUT_GET, 'service_id', FILTER_VALIDATE_INT) ?: null;

        $serviceModel = new Service();
        $result = $serviceModel->paginateCatalogByClinic($this->clinicId, $page, 18, $search);
        $selectedService = $selectedServiceId ? $serviceModel->findCatalogByIdInClinic($selectedServiceId, $this->clinicId) : null;

        if (!$selectedService && $search && count($result['data']) === 1) {
            $selectedService = $result['data'][0];
            $selectedServiceId = (int)($selectedService->Id ?? 0);
        }

        $head = $this->seo->render(
            'Serviços | ' . CONF_SITE_NAME,
            'Catálogo clínico de serviços e preços base',
            url('/clinical/services'),
            theme('assets/images/logo.png'),
            false
        );

        echo $this->view->render('dashboard/clinical/services', [
            'head' => $head,
            'services' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'selectedService' => $selectedService
        ]);
    }

    public function serviceSearch(array $data): void
    {
        $this->authorizeAny(['services.manage', 'appointments.manage', 'consultations.open']);

        $this->renderPlaceholderPage(
            '/clinical/services/search',
            'Buscar Serviço',
            'Página placeholder para busca clínica dedicada de serviços.',
            'Buscar Serviço',
            'Aqui ficará a pesquisa clínica especializada de serviços, separada da busca global, com filtros próprios por categoria, preço base e tipo de atendimento.',
            [
                'Busca dirigida apenas ao catálogo clínico.',
                'Filtros operacionais por categoria e tipo de atendimento.',
                'Entrada futura para acoplamento direto ao Balcão e à Agenda.'
            ]
        );
    }

    /**
     * Lógica de criação/edição de paciente.
     * Responde sempre em JSON para facilitar integração com AJAX.
     */
    protected function handlePatientSave(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!csrf_verify($data)) {
            echo json_encode([
                "error" => true,
                "message" => "Requisição inválida. Atualize a página e tente novamente."
            ]);
            return;
        }

        $id = isset($data['id']) ? (int)$data['id'] : null;
        $personId = isset($data['person_id']) ? (int)$data['person_id'] : 0;
        $mrn = trim($data['medical_record_number'] ?? "");
        $insuranceId = !empty($data['insurance_id']) ? (int)$data['insurance_id'] : null;
        $notes = $data['notes'] ?? null;

        if ($personId <= 0 || $mrn === "") {
            echo json_encode([
                "error" => true,
                "message" => "Informe o paciente (person_id) e o número de processo."
            ]);
            return;
        }

        $patient = new Patient();

        if (!empty($id)) {
            $current = $patient->findByIdInClinic($id, $this->clinicId);
            if (!$current) {
                echo json_encode([
                    "error" => true,
                    "message" => "Paciente não encontrado para edição."
                ]);
                return;
            }
            $patient->Id = $id;
        }

        $patient->bootstrap($this->clinicId, $personId, $mrn, $insuranceId, $notes);

        if (!$patient->savePatient()) {
            echo json_encode([
                "error" => true,
                "message" => $patient->message()->getText() ?: "Falha ao salvar paciente."
            ]);
            return;
        }

        // Auditoria básica
        if ($this->clinicId > 0 && $this->user) {
            (new AuditLog())
                ->bootstrap(
                    $this->clinicId,
                    'patients',
                    (int)$patient->Id,
                    ($id ? 'update' : 'create'),
                    (int)$this->user->Id,
                    null,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                )
                ->register();
        }

        echo json_encode([
            "success" => true,
            "message" => $patient->message()->getText() ?: "Paciente salvo com sucesso.",
            "id" => (int)$patient->Id,
            "redirect" => url('/desk/patients?patient_id=' . (int)$patient->Id)
        ]);
    }

    protected function handleDraftInvoice(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!csrf_verify($data)) {
            echo json_encode([
                'error' => true,
                'message' => 'Requisição inválida. Atualize a página e tente novamente.'
            ]);
            return;
        }

        $patientId = (int)($data['patient_id'] ?? 0);
        $serviceId = (int)($data['service_id'] ?? 0);
        $doctorId = !empty($data['doctor_id']) ? (int)$data['doctor_id'] : null;
        $scheduledAt = trim((string)($data['scheduled_at'] ?? ''));
        $description = trim($data['invoice_description'] ?? '');
        $amount = (float)str_replace(',', '.', (string)($data['invoice_amount'] ?? 0));
        $notes = trim($data['invoice_notes'] ?? '');

        $service = null;
        if ($serviceId > 0) {
            $service = (new Service())->findCatalogByIdInClinic($serviceId, $this->clinicId);
            if ($service) {
                $description = trim((string)($service->name ?? $description));
                $servicePrice = (float)($service->default_price ?? 0);
                if ($servicePrice > 0) {
                    $amount = $servicePrice;
                }
            }
        }

        if ($patientId <= 0 || $description === '' || $amount <= 0) {
            echo json_encode([
                'error' => true,
                'message' => 'Selecione o paciente, descreva o serviço e informe um valor válido para abrir o rascunho.'
            ]);
            return;
        }

        $patient = (new Patient())->findRegistryByIdInClinic($patientId, $this->clinicId);
        if (!$patient) {
            echo json_encode([
                'error' => true,
                'message' => 'Paciente não encontrado para abertura do rascunho.'
            ]);
            return;
        }

        $doctor = null;
        if ($doctorId) {
            $doctor = (new Doctor())->findByIdInClinic($doctorId, $this->clinicId);
            if (!$doctor) {
                echo json_encode([
                    'error' => true,
                    'message' => 'O médico selecionado não foi encontrado para esta clínica.'
                ]);
                return;
            }
        }

        $invoice = new Invoice();
        if (!$invoice->billingTablesReady()) {
            echo json_encode([
                'error' => true,
                'message' => 'A fundação financeira ainda não está disponível nesta base. Execute a migração de faturação da base ativa antes de criar rascunhos.'
            ]);
            return;
        }

        $invoice->bootstrap(
            $this->clinicId,
            $this->generateDraftInvoiceNumber(),
            date('Y-m-d H:i:s'),
            'draft',
            $patientId,
            !empty($patient->insurance_id) ? (int)$patient->insurance_id : null,
            $amount,
            0,
            $amount,
            $this->buildDeskDraftNotes($notes, $service, $doctor)
        );
        $invoice->created_by = (int)($this->user->Id ?? 0);

        if (!$invoice->saveInvoice()) {
            echo json_encode([
                'error' => true,
                'message' => $invoice->message()->getText() ?: 'Não foi possível abrir o rascunho da fatura.'
            ]);
            return;
        }

        $invoiceItem = new InvoiceItem();
        $invoiceItem->bootstrap(
            $this->clinicId,
            (int)$invoice->Id,
            $description,
            1,
            $amount,
            0,
            $serviceId > 0 ? $serviceId : null
        );
        $invoiceItem->created_by = (int)($this->user->Id ?? 0);

        if (!$invoiceItem->saveInvoiceItem()) {
            $invoice->destroy();
            echo json_encode([
                'error' => true,
                'message' => $invoiceItem->message()->getText() ?: 'O rascunho foi cancelado porque o item financeiro não pôde ser gravado.'
            ]);
            return;
        }

        if ($doctor && $scheduledAt !== '') {
            $timestamp = strtotime($scheduledAt);
            if ($timestamp === false) {
                $invoiceItem->destroy();
                $invoice->destroy();
                echo json_encode([
                    'error' => true,
                    'message' => 'A data do atendimento indicada no balcão é inválida.'
                ]);
                return;
            }

            $appointment = new Appointment();
            $appointment->bootstrap(
                $this->clinicId,
                $patientId,
                (int)$doctorId,
                date('Y-m-d H:i:s', $timestamp),
                'pending',
                $description,
                $notes !== '' ? $notes : 'Agendamento aberto a partir do balcão.'
            );

            if (!$appointment->saveAppointment()) {
                $invoiceItem->destroy();
                $invoice->destroy();
                echo json_encode([
                    'error' => true,
                    'message' => $appointment->message()->getText() ?: 'Não foi possível acoplar o médico ao atendimento solicitado.'
                ]);
                return;
            }
        }

        if ($this->clinicId > 0 && $this->user) {
            (new AuditLog())
                ->bootstrap(
                    $this->clinicId,
                    'invoices',
                    (int)$invoice->Id,
                    'draft_create',
                    (int)$this->user->Id,
                    null,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                )
                ->register();
        }

        echo json_encode([
            'success' => true,
            'message' => $doctor && $scheduledAt !== ''
                ? 'Pedido do paciente registado com serviço, rascunho financeiro e médico acoplado ao atendimento.'
                : 'Pedido do paciente registado com sucesso no balcão.',
            'redirect' => url('/desk/patients?patient_id=' . $patientId)
        ]);
    }

    protected function handleDraftInvoiceDelete(array $data): void
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
        $patientId = (int)($data['patient_id'] ?? 0);
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

        $redirect = url('/desk/patients' . ($patientId > 0 ? '?patient_id=' . $patientId : ''));
        echo json_encode([
            'success' => true,
            'message' => $invoice->message()->getText() ?: 'Rascunho eliminado com sucesso.',
            'redirect' => $redirect
        ]);
    }

    protected function generateDraftInvoiceNumber(): string
    {
        $invoice = new Invoice();
        $prefix = sprintf('RASC-%02d-%s', $this->clinicId, date('Ymd'));

        do {
            $candidate = $prefix . '-' . random_int(1000, 9999);
        } while ($invoice->find('clinic_id = :clinic AND invoice_number = :number', 'clinic=' . $this->clinicId . '&number=' . $candidate, 'Id')->fetch());

        return $candidate;
    }

    protected function buildDeskDraftNotes(string $notes, ?object $service, ?object $doctor): string
    {
        $lines = [];

        if ($service) {
            $lines[] = 'Serviço solicitado no balcão: ' . trim((string)($service->name ?? ''));
        }

        if ($doctor) {
            $lines[] = 'Médico acoplado ao atendimento: #' . (int)($doctor->Id ?? 0);
        }

        if ($notes !== '') {
            $lines[] = $notes;
        }

        if (empty($lines)) {
            return 'Rascunho criado no balcão clínico-financeiro do paciente.';
        }

        return implode("\n", $lines);
    }
}
