@extends('layouts.master')
@section('title', 'Home')

@section('content')
<div class="container">
    @auth
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateArticle">
        Crear article
    </button>
    @endauth
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
                    <h4 class="card-title
                        ">Articles</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Imatge</th>
                                    <th>Títol</th>
                                    <th>Contingut</th>
                                    <th>Usuari</th>
                                    <th>Accions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articles as $article)
                                <tr>
                                    <td><img src="{{$article->imatge }}" alt="Article sense imatge" width="100" height="100"></td>
                                    <td>{{ $article->titol }}</td>
                                    <td>{{ $article->contingut }}</td>
                                    <td>{{ $article->usuari }}</td>
                                    <td>
                                        @auth
                                        @if (Auth::user()->email == $article->usuari)
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditArticle{{ $article->id }}">
                                            Editar
                                        </button>
                                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                        @endif
                                        @endauth
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $articles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@auth
<!-- modal form per crear article-->
<div class="modal fade " id="modalCreateArticle" tabindex="-1" aria-labelledby="modalCreateArticleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateArticleLabel">Crear article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('articles.create') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="titol" class="form-label">Títol</label>
                        <input type="text" class="form-control" id="titol" name="titol" value="{{ old('titol') }}">
                        @error('titol','crearArticle')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contingut" class="form-label">Contingut</label>
                        <textarea class="form-control" id="contingut" name="contingut" rows="3">{{ old('contingut') }}</textarea>
                        @error('contingut','crearArticle')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="imatge" class="form-label">Imatge</label>
                        <select class="form-select" id="imatge" name="imatge">
                            <option value="">Selecciona una imatge</option>
                            @foreach ($userImatges as $imatge)
                            <option value="{{ $imatge->id }}">{{ $imatge->titol }}</option>
                            @endforeach
                        </select>
                        @error('imatge','crearArticle')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Mostrem el formulari de crear articles automàticament -->
@if ($errors->crearArticle->any())
<script>
    var myModal = new bootstrap.Modal(document.getElementById('modalCreateArticle'), {
        keyboard: false
    })
    myModal.show()
</script>

@endif




<!-- modal form per editar els articles del usuari -->

@foreach ($articles as $article)
@if(Auth::user()->email == $article->usuari)
<div class="modal fade" id="modalEditArticle{{ $article->id }}" tabindex="-1" aria-labelledby="modalEditArticleLabel{{ $article->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditArticleLabel{{ $article->id }}">Editar article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body
            ">
                <form action="{{ route('articles.editar', $article->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="titol" class="form-label">Títol</label>
                        <input type="text" class="form-control" id="titol" name="titol" value="{{ $article->titol }}">
                    </div>
                    <div class="mb-3">
                        <label for="contingut" class="form-label
                    ">Contingut</label>
                        <textarea class="form-control" id="contingut" name="contingut" rows="3">{{ $article->contingut }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="imatge" class="form-label">Imatge</label>
                        <select class="form-select" id="imatge" name="imatge">
                            <option value="">Imatge actual</option> 
                            @foreach ($userImatges as $imatge)
                            <option value="{{ $imatge->id }}" >{{ $imatge->titol }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Editar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- modal form per editar els articles del usuari si ha fet un error -->
@if($errors->editarArticle->any())
<div class="modal fade show" id="modalEditArticle" tabindex="-1" aria-labelledby="modalEditArticleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditArticleLabel">Editar article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('articles.editar', session('idArticle')) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="titol" class="form-label">Títol</label>
                        <input type="text" class="form-control" id="titol" name="titol" value="{{ old('titol') }}">
                        @error('titol','editarArticle')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contingut" class="form-label">Contingut</label>
                        <textarea class="form-control" id="contingut" name="contingut" rows="3">{{ old('contingut') }}</textarea>
                        @error('contingut','editarArticle')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="imatge" class="form-label">Imatge</label>
                        <select class="form-select" id="imatge" name="imatge">
                            <option value="">Selecciona una imatge</option>
                            @foreach ($userImatges as $imatge)
                            <option value="{{ $imatge->id }}"  @selected(old('imatge') == $imatge->id)>{{ $imatge->titol }}</option>
                            @endforeach
                        </select>
                        @error('imatge','editarArticle')
                        <div class="alert alert-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Editar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var myModal = new bootstrap.Modal(document.getElementById('modalEditArticle'), {
        keyboard: false
    })
    myModal.show()
</script>
@endif
@endauth

@endsection