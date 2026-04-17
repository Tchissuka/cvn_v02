<?php

namespace Source\App;

class Reports extends AppLauncher
{
    public function overview(array $data): void
    {
        $this->renderPlaceholderPage(
            '/reports/overview',
            'Relatórios',
            'Página placeholder para hub de relatórios.',
            'Relatórios',
            'Este espaço será o hub de relatórios transversais da clínica, reunindo saídas clínicas, financeiras, fiscais e operacionais num único ponto.',
            [
                'Central de relatórios por domínio.',
                'Acesso futuro a relatórios clínicos, financeiros e fiscais.',
                'Base para exportação, conferência e análise institucional.'
            ]
        );
    }
}