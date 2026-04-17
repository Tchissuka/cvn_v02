<?php

namespace Source\App;

class Finance extends AppLauncher
{
    public function overview(array $data): void
    {
        $this->renderPlaceholderPage(
            '/finance/overview',
            'Resumo Financeiro',
            'Página placeholder para visão geral financeira.',
            'Resumo Financeiro',
            'Esta área vai consolidar receitas, despesas, posição de caixa e leitura sintética da operação financeira da clínica.',
            [
                'Resumo de caixa, receitas e despesas.',
                'Leitura financeira consolidada da clínica.',
                'Integração futura com fiscal, balcão e relatórios.'
            ]
        );
    }

    public function revenue(array $data): void
    {
        $this->renderPlaceholderPage(
            '/finance/revenue',
            'Receitas',
            'Página placeholder para receitas financeiras.',
            'Receitas (Kz)',
            'Aqui ficará a leitura detalhada das entradas financeiras, origem da cobrança e composição das receitas por serviço, produto ou documento.',
            [
                'Receitas por origem e período.',
                'Conferência de cobrança por documento.',
                'Base futura para indicadores financeiros.'
            ]
        );
    }

    public function expenses(array $data): void
    {
        $this->renderPlaceholderPage(
            '/finance/expenses',
            'Despesas',
            'Página placeholder para despesas financeiras.',
            'Despesas',
            'Esta área vai reunir saídas financeiras, classificação de despesa, conferência de pagamentos e leitura operacional do custo da clínica.',
            [
                'Despesas por categoria e período.',
                'Conferência de pagamentos e compromissos.',
                'Integração futura com relatórios de resultado.'
            ]
        );
    }

    public function reports(array $data): void
    {
        $this->renderPlaceholderPage(
            '/finance/reports',
            'Relatórios Financeiros',
            'Página placeholder para relatórios financeiros.',
            'Relatórios Financeiros',
            'Aqui ficará a síntese financeira consolidada para acompanhamento interno, fecho mensal e leitura gerencial da operação.',
            [
                'Relatórios financeiros por período.',
                'Leitura gerencial de desempenho e posição.',
                'Base futura para exportação e análise consolidada.'
            ]
        );
    }
}