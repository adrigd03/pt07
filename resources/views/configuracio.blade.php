@extends('layouts.master')
@section('title', 'Perfil')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Perfil</h4>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-5">
                            <div class="row">
                                <div class="col">
                                    <img src="{{ Auth::user()->avatar }}"  class="img-fluid" alt="Imatge de perfil" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                            <div class="row mt-3 ">
                                <div class="col">
                                    <button type="button" class="btn p-0 text-primary text-decoration-underline" data-bs-toggle="modal" data-bs-target="#modalEditImage">
                                        Canviar imatge de perfil
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col">

                            <div class="row">
                                <div class="col-md-5">

                                    <p>NOM D'USUARI ACTUAL</p>

                                    <p>{{ Auth::user()->username }}</p>

                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3">
                                    <button type="button" class="btn p-0 text-primary text-decoration-underline" data-bs-toggle="collapse" data-bs-target="#collapseEditUsername">
                                        Editar nom d'usuari
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn p-0 text-primary text-decoration-underline" data-bs-toggle="collapse" data-bs-target="#collapseEditPassword">
                                        Canviar contrasenya
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <button type="button" class="btn p-0 text-danger text-decoration-underline" data-bs-toggle="modal" data-bs-target="#modalDeleteAccount">
                                        Eliminar compte
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- collapse div del formulari de canvi de username -->
    <div class="collapse" id="collapseEditUsername">
        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Editar nom d'usuari</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('configuracio.username') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label for="username">Nom d'usuari</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ Auth::user()->username }}">
                    </div>
                    @error('username','username')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
    <!-- collapse div del formulari de canvi de password -->
    <div class="collapse" id="collapseEditPassword">
        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title
                    ">Canviar contrasenya</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('configuracio.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label for="old_password">Contrasenya actual</label>
                        <input type="password" class="form-control" id="old_password" name="old_password">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Contrasenya</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirmation">Repetir contrasenya</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    @error('old_password','password')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    @error('password','password')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal div del formulari de canvi d'imatge -->
<div class="modal fade" id="modalEditImage" tabindex="-1" aria-labelledby="modalEditImageLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditImageLabel">Canviar imatge de perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body
                ">
                <form action="{{ route('configuracio.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <input type="file" class="form-control" id="avatar" name="avatar">
                    </div>
                    @error('avatar','avatar')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal div confirmacio esborrar compte -->
<div class="modal fade" id="modalDeleteAccount" tabindex="-1" aria-labelledby="modalDeleteAccountLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteAccountLabel">Eliminar compte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Estàs segur que vols eliminar el teu compte?</p>
                <form action="{{ route('configuracio.delete') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->username->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var collapse = new bootstrap.Collapse(document.getElementById('collapseEditUsername'));
        collapse.show();
        var input = document.getElementById('username');
        input.value = "{{ old('username') }}";
    });
</script>
@endif

@if($errors->password->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var collapse = new bootstrap.Collapse(document.getElementById('collapseEditPassword'));
        collapse.show();
    });
</script>
@endif

@if($errors->avatar->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('modalEditImage'), {
            keyboard: false
        })
        myModal.show()
    });
</script>
@endif

@endsection