<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <div class="container mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Crear producto</h1>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">Volver</a>
      </div>

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('productos.store') }}" method="POST" class="w-75">
        @csrf

        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre de Producto</label>
          <input id="nombre" name="nombre" value="{{ old('nombre') }}" class="form-control" type="text" required maxlength="255">
        </div>

        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción</label>
          <textarea id="descripcion" name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input id="cantidad" name="cantidad" value="{{ old('cantidad') }}" class="form-control" type="number" min="0" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input id="precio" name="precio" value="{{ old('precio') }}" class="form-control" type="number" step="0.01" min="0" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
      </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
