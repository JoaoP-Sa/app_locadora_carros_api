<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    private $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$marcas = Marca::all();

        /* ---------------------------- uso de marca repository ------------------------------- */

        $marcaRepository = new MarcaRepository($this->marca);

        if ($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        }

        /* ------------------------------------------------------------------------------------ */

        /* repare como com o uso de repository acima conseguimos encurtar todo o código, que é
           visivelmente maior embaixo, e que só está sendo mantido comentado para fins didáticos */


        // $marcas = [];

        // if ($request->has('atributos_modelos')) {
        //     $atributos_modelos = $request->atributos_modelos;
        //     $marcas = $this->marca->with('modelos:id,'.$atributos_modelos);
        // } else {
        //     $marcas = $this->marca->with('modelos');
        // }

        // if ($request->has('filtro')) {
        //     $filtros = explode(';', $request->filtro);

        //     foreach($filtros as $key => $condicao) {
        //         $condicoes = explode(':', $condicao);
        //         $marcas = $marcas->where($condicoes[0], $condicoes[1], $condicoes[2]);
        //     }
        // }

        // if ($request->has('atributos')) {
        //     $atributos = $request->atributos;
        //     $marcas = $marcas->selectRaw($atributos)->get();

        //     //o método selectRaw aceita receber uma string única separada por vírgula, ele consegue
        //     //pegar essa string e quebrar ela em várias outras através da vírgula
        // } else {
        //     $marcas = $marcas->get();
        // }

        //$marcas = $this->marca->all();

        // return response()->json($marcas, 200);
        return response()->json($marcaRepository->getResultado(), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$marca = Marca::create($request->all());

        $request->validate($this->marca->rules(), $this->marca->feedback());

        $image = $request->file('imagem');
        $image_urn = $image->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->input('nome'),
            'imagem' => $image_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);

        if ($marca === null) {
            //return ['erro' => 'Recurso pesquisado não existe.'];
            return response()->json(['erro' => 'Recurso pesquisado não existe.'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();

            foreach ($marca->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }

        if ($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $image = $request->file('imagem');
        $image_urn = $image->store('imagens', 'public');

        // preencher o objeto marca com os dados do request
        $marca->fill($request->all());
        $marca->imagem = $image_urn;

        $marca->save();

        // $marca->update([
        //     'nome' => $request->input('nome'),
        //     'imagem' => $image_urn
        // ]);

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //$marca->delete();

        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'], 404);
        }

        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
