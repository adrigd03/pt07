<header>
    <nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top ">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span class="mx-auto">Pràctica 07</span>

            </a>
            <!-- barra navegació -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{route('home')}}">Home</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{route('articles-propis')}}">Articles propis</a>
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

                            <img class="border rounded-circle img-profile" height="40px" width="40px" src="{{ (Auth::user()->avatar)}}" alt="avatar del usuari" />
                        </a>
                        <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">

                            <a class="dropdown-item" href="{{route('configuracio')}}">
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
</header>