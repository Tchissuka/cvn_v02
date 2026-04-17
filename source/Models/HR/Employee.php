<?php

namespace Source\Models\HR;

use Source\Models\ClinicModel;

class Employee extends ClinicModel
{
    public function __construct()
    {
        parent::__construct('employees', ['Id'], ['clinic_id', 'person_id']);
    }

    public function bootstrap(
        int $clinicId,
        int $personId,
        ?int $departmentId = null,
        ?string $jobTitle = null,
        ?string $hireDate = null,
        ?string $terminationDate = null,
        bool $active = true
    ): self {
        $this->clinic_id = $clinicId;
        $this->person_id = $personId;
        $this->department_id = $departmentId;
        $this->job_title = $jobTitle;
        $this->hire_date = $hireDate;
        $this->termination_date = $terminationDate;
        $this->is_active = $active ? 1 : 0;
        return $this;
    }

    public function saveEmployee(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Preencha os dados obrigatórios do funcionário.");
            return false;
        }

        if (!parent::save()) {
            if (!$this->message()->getText()) {
                $this->message->error("Erro ao salvar o funcionário, verifique os dados.");
            }
            return false;
        }

        $this->message->success("Funcionário salvo com sucesso.");
        return true;
    }
}
