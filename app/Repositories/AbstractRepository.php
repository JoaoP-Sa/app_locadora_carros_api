<?php

namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository {
    private $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados($atributos) {
        $this->model = $this->model->with($atributos);
        // a query está sendo montada
    }

    public function filtro($filtros) {
        $filtros = explode(';', $filtros);

        foreach($filtros as $key => $condicao) {
            $condicoes = explode(':', $condicao);
            $this->model = $this->model->where($condicoes[0], $condicoes[1], $condicoes[2]);
            // a query está sendo montada
        }
    }

    public function selectAtributos($atributos) {
        $this->model = $this->model->selectRaw($atributos);

        //o método selectRaw aceita receber uma string única separada por vírgula, ele consegue
        //pegar essa string e quebrar ela em várias outras através da vírgula
    }

    public function getResultado() {
        return $this->model->get();
    }
}

?>
