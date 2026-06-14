<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="container mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Editar Producto</h1>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">Volver</a>
      </div>

      {{-- small preview table that matches index styling --}}
      <div class="mb-4">
        <table class="table table-striped table-sm w-50">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Precio</th>
              <th>Cantidad</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $producto->nombre }}</td>
              <td>{{ $producto->descripcion }}</td>
              <td>{{ number_format($producto->precio, 2) }}</td>
              <td>{{ $producto->cantidad }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <form action="{{ route('productos.update', $producto) }}" method="POST" class="w-75">
        @csrf
        @method('PUT')

        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" class="form-control" required maxlength="255">
        </div>

        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción</label>
          <textarea id="descripcion" name="descripcion" class="form-control" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input id="precio" name="precio" value="{{ old('precio', $producto->precio) }}" class="form-control" type="number" step="0.01" min="0" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input id="cantidad" name="cantidad" value="{{ old('cantidad', $producto->cantidad) }}" class="form-control" type="number" min="0" required>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-info">Guardar cambios</button>
          <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
      </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
