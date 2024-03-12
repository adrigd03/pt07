<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canviar contrasenya</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-center text-white">
                        <h3 class="mb-0 d-inline">Canviar contrasenya</h3>
                        <div class="d-inline float-end">

                            <a href="{{ route('home') }}" class="btn btn-close" aria-label="Close"></a>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('restaurarContrasenya.post') }}">
                        <div class="card-body">
                            @csrf
                            <!-- Camp de email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}" required>
                                @error('email','restaurar')
                                <div class="alert alert-danger mt-2" name="error">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Camp de password i de confirmació de password -->
                            <div class="mb-3">
                                <label for="password" class="form-label ">Nova contrasenya</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                @error('password','restaurar')
                                <div class="alert alert-danger mt-2" name="error">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label ">Confirmar contrasenya</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                @error('password_confirmation','restaurar')
                                <div class="alert alert-danger mt-2" name="error">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="token" value="{{ $token }}">


                        </div>

                        @error('error','restaurar')
                        <div class="alert alert-danger mt-2" name="error"> {{ $message }}</div>
                        @enderror

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Canviar contrasenya</button>
                        </div>
                    </form>


                    @if(session('success'))

                    <div class="alert alert-success mt-2" name="error">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger mt-2" name="error">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>

</html>