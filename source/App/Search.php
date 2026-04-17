<?php

namespace Source\App;

use Source\Models\Billing\Invoice;
use Source\Models\Billing\Service;
use Source\Models\Clinical\Patient;
use Source\Models\Pharmacy\Product;

class Search extends AppLauncher
{
    public function global(array $data): void
    {
        $query = trim((string)(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''));

        $results = [
            'patients' => [],
            'services' => [],
            'products' => [],
            'fiscal' => []
        ];

        if ($query !== '') {
            if ($this->canAny(['patients.view', 'patients.update', 'patients.create'])) {
                $results['patients'] = array_slice((new Patient())->paginateRegistryByClinic($this->clinicId, 1, 8, $query)['data'] ?? [], 0, 8);
            }

            if ($this->canAny(['services.manage', 'appointments.manage', 'consultations.open'])) {
                $results['services'] = (new Service())->searchByClinic($this->clinicId, $query, 8);
            }

            if ($this->canAny(['pharmacy.products.manage', 'pharmacy.sales.create', 'stock.view'])) {
                $results['products'] = (new Product())->searchByClinic($this->clinicId, $query, 8);
            }

            if ($this->canAny(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage'])) {
                $results['fiscal'] = (new Invoice())->searchByClinic($this->clinicId, $query, 8);
            }
        }

        $head = $this->seo->render(
            'Pesquisa Global | ' . CONF_SITE_NAME,
            'Busca operacional unificada por paciente, serviço, produto e documento fiscal',
            url('/search/global'),
            theme('assets/images/logo.png'),
            false
        );

        echo $this->view->render('dashboard/search/global', [
            'head' => $head,
            'query' => $query,
            'results' => $results
        ]);
    }
}