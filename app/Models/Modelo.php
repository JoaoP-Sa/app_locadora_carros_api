<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = ['marca_id', 'nome', 'imagem', 'numero_portas', 'lugares', 'air_bag', 'abs'];

    public function rules()
    {
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpg',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean',
        ];

        /*
            na linha 18 temos 3 parâmetros passados no unique para o caso de edição de alguma linha, que são:

            1° - nome da tabela
            2° - nome da coluna definida como unique
            3° - o id que será ignorado na consulta por valores repetidos
        */
    }

    public function marca() {
        // Um modelo pertence a UMA marca
        return $this->belongsTo('App\Models\Marca');
    }
}
