<?php

namespace Source\Models\Clinical;

use Source\Models\ClinicModel;

class Appointment extends ClinicModel
{
    public function __construct()
    {
        parent::__construct('appointments', ['Id'], ['clinic_id', 'patient_id', 'doctor_id', 'scheduled_at', 'status']);
    }

    public function bootstrap(
        int $clinicId,
        int $patientId,
        int $doctorId,
        string $scheduledAt,
        string $status,
        ?string $reason = null,
        ?string $notes = null
    ): self {
        $this->clinic_id = $clinicId;
        $this->patient_id = $patientId;
        $this->doctor_id = $doctorId;
        $this->scheduled_at = $scheduledAt;
        $this->status = $status;
        $this->reason = $reason;
        $this->notes = $notes;
        return $this;
    }

    public function saveAppointment(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Preencha os dados obrigatórios do agendamento.");
            return false;
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error("Erro ao salvar o agendamento, verifique os dados.");
            }
            return false;
        }

        $this->message->success("Agendamento salvo com sucesso.");
        return true;
    }
}
