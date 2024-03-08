<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-center text-white">
                        <h3 class="mb-0 d-inline">Registre</h3>
                        <div class="d-inline float-end">
                            <a href="{{ route('home') }}" class="btn btn-close" aria-label="Close"></a>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('registre') }}">
                        <div class="card-body">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" required>
                                @error('email','registre')
                                <div class="alert alert-danger mt-2" name="error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="{{old('username')}}" required>
                                @error('username','registre')
                                <div class="alert alert-danger mt-2" name="error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label ">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                @error('password','registre')
                                <div class="alert alert-danger mt-2" name="error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label ">Confirmar Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        @error('error','registre')
                        <div class="alert alert-danger mt-2" name="error"> {{ $message }}</div>
                        @enderror

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registra't</button>
                        </div>
                        <div class="mt-3 d-grid gap-2">
                            <a href="{{ route('login-google') }}" class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" height="24" width="100%">
                                    <path fill="#ffffff" d="M386 400c45-42 65-112 53-179H260v74h102c-4 24-18 44-38 57z"></path>
                                    <path fill="#ffffff" d="M90 341a192 192 0 0 0 296 59l-62-48c-53 35-141 22-171-60z"></path>
                                    <path fill="#ffffff" d="M153 292c-8-25-8-48 0-73l-63-49c-23 46-30 111 0 171z"></path>
                                    <path fill="#ffffff" d="M153 219c22-69 116-109 179-50l55-54c-78-75-230-72-297 55z"></path>
                                </svg> Registra't amb Google
                            </a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Ja tens un compte? <a href="{{ route('login') }}">Inicia sessió</a></p>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>