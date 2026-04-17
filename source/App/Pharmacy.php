<?php

namespace Source\App;

use Source\Models\Pharmacy\Product;

class Pharmacy extends AppLauncher
{
    public function desk(array $data): void
    {
        $this->authorizeAny(['pharmacy.products.manage', 'pharmacy.sales.create', 'stock.view']);

        $this->renderPlaceholderPage(
            '/pharmacy/desk',
            'Balcão da Farmácia',
            'Página placeholder para o balcão operativo da farmácia.',
            'Balcão da Farmácia',
            'Esta área vai concentrar atendimento farmacêutico, dispensa rápida, reserva operacional e encaminhamento do produto para venda ou apoio ao tratamento.',
            [
                'Atendimento rápido no balcão farmacêutico.',
                'Ligação futura a stock, venda e histórico do paciente.',
                'Espaço reservado para processo operacional distinto do catálogo.'
            ]
        );
    }

    public function products(array $data): void
    {
        $this->authorizeAny(['pharmacy.products.manage', 'pharmacy.sales.create', 'stock.view']);

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        $search = filter_input(INPUT_GET, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null;
        $selectedProductId = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT) ?: null;

        $productModel = new Product();
        $result = $productModel->paginateCatalogByClinic($this->clinicId, $page, 18, $search);
        $selectedProduct = $selectedProductId ? $productModel->findCatalogByIdInClinic($selectedProductId, $this->clinicId) : null;

        if (!$selectedProduct && $search && count($result['data']) === 1) {
            $selectedProduct = $result['data'][0];
        }

        $head = $this->seo->render(
            'Farmácia | ' . CONF_SITE_NAME,
            'Catálogo de produtos e balcão farmacêutico',
            url('/pharmacy/products'),
            theme('assets/images/logo.png'),
            false
        );

        echo $this->view->render('dashboard/pharmacy/products', [
            'head' => $head,
            'products' => $result['data'],
            'pagination' => $result,
            'search' => $search,
            'selectedProduct' => $selectedProduct
        ]);
    }

    public function search(array $data): void
    {
        $this->authorizeAny(['pharmacy.products.manage', 'pharmacy.sales.create', 'stock.view']);

        $this->renderPlaceholderPage(
            '/pharmacy/search',
            'Buscar Produto',
            'Página placeholder para busca dedicada de produtos farmacêuticos.',
            'Buscar Produto',
            'Esta página vai receber a procura operacional de produtos, com filtros próprios para stock, categoria, disponibilidade e contexto de venda ou dispensa.',
            [
                'Busca dirigida apenas ao universo da farmácia.',
                'Filtros futuros por stock, categoria e disponibilidade.',
                'Integração futura com balcão, catálogo e ficha do produto.'
            ]
        );
    }
}