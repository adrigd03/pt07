@extends('layouts.master')
@section('title', 'Galeria')

@section('content')

<div class="container">

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateImatge">
        Afegir imatge
    </button>
    @if (session('success'))
    <div class="alert alert-success mt-1">
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger mt-1">
        {{ session('error') }}
    </div>
    @endif

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Galeria</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($imatges as $imatge)
                        <div class="col-md-4">
                            <div class="card">
                                <img src="{{ $imatge->url }}" class="card-img-top" alt="imatge de la galeria">
                                <div class="card-body">
                                    <p class="card-text">{{ $imatge->titol }}</p>
                                    <p class="card-text">{{ $imatge->descripcio }}</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditImatge{{ $imatge->id }}">
                                        Editar
                                    </button>
                                    <form action="{{ route('galeria.destroy', $imatge->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="modal fade" id="modalCreateImatge" tabindex="-1" aria-labelledby="modalCreateImatgeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateImatgeLabel">Afegir imatge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('galeria.crear') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titol" class="form-label">Títol</label>
                        <input type="text" class="form-control" id="titol" name="titol" value="{{ old('titol') }}">
                        @error('titol', 'crearImatge')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="descripcio" class="form-label">Descripció</label>
                        <textarea class="form-control" id="descripcio" name="descripcio" rows="3">{{ old('descripcio') }}</textarea>
                        @error('descripcio', 'crearImatge')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="imatge" class="form-label">Imatge</label>
                        <input type="file" class="form-control" id="imatge" name="imatge">
                        @error('imatge', 'crearImatge')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Afegir</button>
                </div>
            </form>
        </div>

    </div>
</div>

@foreach ($imatges as $imatge)
<div class="modal fade" id="modalEditImatge{{ $imatge->id }}" tabindex="-1" aria-labelledby="modalEditImatgeLabel{{ $imatge->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditImatgeLabel{{ $imatge->id }}">Editar imatge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('galeria.editar', $imatge->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titol" class="form-label">Títol</label>
                        <input type="text" class="form-control" id="titol" name="titol" value="{{ $imatge->titol }}">
                        @error('titol', 'editarImatge')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="descripcio" class="form-label">Descripció</label>
                        <textarea class="form-control" id="descripcio" name="descripcio" rows="3">{{ $imatge->descripcio }}</textarea>
                        @error('descripcio', 'editarImatge')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


@if($errors->editarImatge->any())
<div class="modal fade show" id="modalEditImatge" tabindex="-1" aria-labelledby="modalEditImatgeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditImatgeLabel">Editar imatge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('galeria.editar', session('idImatge')) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titol" class="form-label">Títol</label>
                        <input type="text" class="form-control" id="titol" name="titol" value="{{ old('titol') }}">
                        @error('titol', 'editarImatge')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="descripcio" class="form-label">Descripció</label>
                        <textarea class="form-control" id="descripcio" name="descripcio" rows="3">{{ old('descripcio') }}</textarea>
                        @error('descripcio', 'editarImatge')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    var myModal = new bootstrap.Modal(document.getElementById('modalEditImatge'), {
        keyboard: false
    });
    myModal.show();
</script>
@endif

@if($errors->crearImatge->any())
<script type="text/javascript">
    var myModal = new bootstrap.Modal(document.getElementById('modalCreateImatge'), {
        keyboard: false
    });
    myModal.show();
</script>
@endif

@endsection