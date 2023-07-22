<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem'];

    public function rules()
    {
        return [
            //'nome' => 'required|unique:marcas|min:3', // aqui estamos dizendo que o nome é único na tabela marcas
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png,jpg',
        ];

        /*
            na linha 18 temos 3 parâmetros passados no unique para o caso de edição de alguma linha, que são:

            1° - nome da tabela
            2° - nome da coluna definida como unique
            3° - o id que será ignorado na consulta por valores repetidos
        */
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres',
            'imagem.mimes' => 'O arquivo deve ser uma imagem do tipo PNG ou JPG'
        ];
    }

    public function modelos() {
        // uma MARCA possui muitos MODELOS
        return $this->hasMany('App\Models\Modelo');
    }
}
