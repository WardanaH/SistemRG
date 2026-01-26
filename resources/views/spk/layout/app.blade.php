<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Restu Guru Promosindo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        /* CSS tambahan agar sidebar full height */
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
        }
        .sidebar-container {
            min-width: 250px;
            max-width: 250px;
            min-height: calc(100vh - 56px); /* Mengurangi tinggi header */
        }
    </style>
</head>
<body>

    <div class="wrapper">
        @include('spk.layout.header')

        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-auto bg-dark text-white sidebar-container">
                    @include('spk.layout.sidebar')
                </div>

                <main class="col p-4 bg-light">
                    @yield('content')
                </main>
            </div>
        </div>

        @include('spk.layout.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
