<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/css/selectize.default.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/js/standalone/selectize.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillatoasts@1.4.0/vanillatoasts.css">
    <script src="https://cdn.jsdelivr.net/npm/vanillatoasts@1.4.0/vanillatoasts.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>

<body>

    <!-- Header -->
    @include('partials.header')

    <!-- Main Content -->
    {{-- <div style="height: calc(100vh - (56px + 72px));"> --}}
    <div>
        <div class="container pt-5">
            <div style="width: 100%">
                @yield('content')
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Footer -->
    @include('partials.footer')
</body>
<script src="{{ asset('js/custom.js') }}"></script>

</html>
