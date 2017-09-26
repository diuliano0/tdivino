<?php

namespace Modules\Core\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Core\Models\Pessoa;
use Modules\Core\Models\User;

/**
 * Class PessoaTransformer
 * @package namespace Portal\Transformers;
 */
class PessoaTransformer extends TransformerAbstract
{

    /**
     * Transform the \Pessoa entity
     * @param \Pessoa $model
     *
     * @return array
     */
    public function transform(Pessoa $model)
    {
        return [
            'id' => (int)$model->id,
            'data_nascimento' => $model->data_nascimento,
            'cpf_cnpj' => (string)$model->cpf_cnpf,
            'rg' => (string)$model->rg,
            'nec_especial' => (string)$model->nec_especial,
            'orgao_emissor' => (string)$model->orgao_emissor,
            'escolaridade' => (string)$model->escolaridade,
            'estado_civil' => (string)$model->estado_civil,
            'fantasia' => (string)$model->fantasia,
            'contato' => (string)$model->contato,
            'sexo_label' => ($model->sexo) ? (string)User::$_SEXO[$model->sexo] : null,
            'sexo' => (int)$model->sexo,
            'tipo' => (int)$model->tipo(),
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
