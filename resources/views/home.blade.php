<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <title>Home</title>
</head>

<body>
    <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top ">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="http://tallernadal.com/images/logosapa_transparent.png" alt="logo de l'institut sa palomera" width="145" height="64" class="d-inline-block align-text-top">
                <span class="mx-auto">Pràctica 07</span>

            </a>
            <!-- barra navegació -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="">Home</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Articles propis</a>
                    </li>

                    @endauth


                </ul>

            </div>
            @auth
            <ul class="navbar-nav flex-nowrap ms-auto">
                <li class="nav-item dropdown no-arrow">
                    <div class="nav-item dropdown no-arrow">
                        <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
                            <span class="d-none d-lg-inline me-2 text-gray-600 small">{{ Auth::user()->username }} </span>

                            <img class="border rounded-circle img-profile" height="40px" width="40px" src="{{ session('avatarUrl')}}" alt="avatar del usuari" />
                        </a>
                        <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">

                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Perfil
                            </a>
                            <a class="dropdown-item" href="#">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="btn me-2 text-gray-400" type="submit">Logout</button>
                                </form>
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
            @endauth
            @guest
            <!-- Mostrar dos botons per el login i registre -->
            <ul class="navbar-nav flex-nowrap ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('registre') }}">Registre</a>
                </li>
            </ul>
            @endguest
        </div>

    </nav>


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
                                        <td><img src="{{ asset('images/' . $article->image) }}" alt="imatge de l'article" width="100" height="100"></td>
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

    <!-- modal form per crear article-->
    <div class="modal fade " id="modalCreateArticle" tabindex="-1" aria-labelledby="modalCreateArticleLabel" aria-hidden="true" >
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
                        <!-- <div class="mb-3">
                            <label for="image" class="form-label">Imatge</label>
                            <input type="" class="form-control" id="image" name="image">
                            @error('image','crearArticle')
                            <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div> -->
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
                        <button type="submit" class="btn btn-primary">Editar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- modal form per editar els articles del usuari si ha fet un error -->
    @if($errors->editarArticle->any())
    <div class="modal fade show" id="modalEditArticle" tabindex="-1" aria-labelledby="modalEditArticleLabel" aria-hidden="true" >
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
                        <button type="submit" class="btn btn-primary">Editar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script >
        var myModal = new bootstrap.Modal(document.getElementById('modalEditArticle'), {
            keyboard: false
        })
        myModal.show()

    </script>
    @endif




    









</body>

</html>