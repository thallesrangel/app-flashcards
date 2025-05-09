<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
   
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.png') }}">
    <title>IA</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    {{-- @include('alert') --}}
    @yield('content')

    <script type="text/javascript">
        var APP_URL = {!! json_encode(url('/')) !!}


        function alert_success(title, text) {
            Swal.fire({
                position: 'top',
                icon: 'success',
                title: title,
                text: text,
                showConfirmButton: false,
                timer: 2000
            })
        }

        function alert_error(title, text) {
            Swal.fire({
                position: 'top',
                icon: 'error',
                title: title,
                text: text,
                showConfirmButton: false,
                timer: 2000
            })
        }
    </script>

<div class="modal"  id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body bg-transparent">
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <div class="row">
                        <div class="btn btn-dark d-flex align-items-center justify-content-center" type="button"> 
                            <div class="spinner-border text-light" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="p-2">Carregando</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@include('components.footer')

</body>
</html>