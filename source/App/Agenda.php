<?php

namespace Source\App;

class Agenda extends AppLauncher
{
    public function today(array $data): void
    {
        $this->renderPlaceholderPage(
            '/agenda/today',
            'Consultas Hoje',
            'Página placeholder para consultas do dia.',
            'Consultas Hoje',
            'Esta área vai reunir a agenda diária, fila clínica, encaixes e estado operacional das consultas agendadas para hoje.',
            [
                'Leitura diária das consultas confirmadas e pendentes.',
                'Fila operacional para receção e chamada do paciente.',
                'Integração futura com balcão, médico e atendimento clínico.'
            ]
        );
    }

    public function week(array $data): void
    {
        $this->renderPlaceholderPage(
            '/agenda/week',
            'Agenda Semanal',
            'Página placeholder para agenda semanal.',
            'Agenda Semanal',
            'Aqui ficará a leitura semanal da ocupação clínica, distribuição dos médicos e visão consolidada dos atendimentos planeados.',
            [
                'Visão semanal da ocupação clínica.',
                'Planeamento por médico e tipo de serviço.',
                'Base futura para remarcação e controlo de carga.'
            ]
        );
    }

    public function create(array $data): void
    {
        $this->renderPlaceholderPage(
            '/agenda/create',
            'Nova Consulta',
            'Página placeholder para abertura manual de consulta na agenda.',
            'Nova Consulta',
            'Esta área vai servir para criar consulta fora do fluxo do balcão, quando a equipa precisar abrir um agendamento manual com médico, horário e motivo.',
            [
                'Abertura manual de consulta.',
                'Escolha de médico, serviço e horário.',
                'Integração futura com balcão e ficha clínica.'
            ]
        );
    }

    public function availability(array $data): void
    {
        $this->renderPlaceholderPage(
            '/agenda/availability',
            'Horários Disponíveis',
            'Página placeholder para disponibilidade da agenda.',
            'Horários Disponíveis',
            'Aqui ficará a leitura de disponibilidade por médico, especialidade e janela horária, suportando encaixe e marcação operacional.',
            [
                'Disponibilidade por médico e período.',
                'Janela de encaixe para a receção.',
                'Base futura para agenda inteligente e remarcação.'
            ]
        );
    }
}