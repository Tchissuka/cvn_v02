<?php

namespace Source\Models\Settings;

use Source\Core\Model;

class Numerador extends Model
{
    public function __construct()
    {
        parent::__construct("general_numerator", ['Id'], ["clinic_id"]);
    }

    /**
     * Retorna numeração geral
     *
     * @param int         $type  identificador lógico do tipo de documento
     * @param int         $inst  clínica (clinic_id)
     * @param null|string $mode  'A' = anual, 'C' = contínua (apenas usado na criação do primeiro registo)
     * @return null|int
     */
    public function myNumber(int $type, int $inst, ?string $mode = null): ?int
    {

        if ($xxb = $this->find("type_num_id={$type} AND clinic_id={$inst}")->fetch()) {
            // mode: 'C' = contínua, 'A' = anual (reinicia por ano)
            if ($xxb->mode === 'C') {
                $xxb->number++;
            } else {
                if ($xxb->number_year == date('Y')) {
                    $xxb->number++;
                } else {
                    $xxb->number_year = date('Y');
                    $xxb->number = 1;
                }
            }
            if ($xxb->save()) {
                return $xxb->number;
            }
        }

        $this->clinic_id = $inst;
        $this->type_num_id = $type;
        // Se não for informado, modo padrão anual ('A')
        $this->mode = in_array($mode, ['A', 'C'], true) ? $mode : 'A';
        $this->number = 1;
        $this->number_year = date('Y');
        if ($this->save()) {
            return $this->number;
        }
    }
}
