<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::all();

        return request()->wantsJson()
            ? response()->json(['data' => $productos], 200)
            : view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cantidad' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto = Producto::create($data);

        return $request->wantsJson()
            ? response()->json(['data' => $producto], 201)
            : redirect()->route('productos.show', $producto)->with('success', 'Producto creado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        return request()->wantsJson()
            ? response()->json(['data' => $producto], 200)
            : view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|nullable|string',
            'cantidad' => 'sometimes|required|integer|min:0',
            'precio' => 'sometimes|required|numeric|min:0',
        ]);

        // If only updating stock, the client can send only 'cantidad'.
        $producto->update($data);

        return $request->wantsJson()
            ? response()->json(['data' => $producto], 200)
            : redirect()->route('productos.show', $producto)->with('success', 'Producto actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return request()->wantsJson()
            ? response()->json(['message' => 'Producto eliminado.'], 200)
            : redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}
