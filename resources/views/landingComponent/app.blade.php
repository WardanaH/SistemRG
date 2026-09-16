<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restu Guru Promosindo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .hero-section {
            width: 100%;
            padding: 0;
            margin: 0;
            /* Pastikan kadada lagi aturan background warna atau height di sini */
        }

        .hero-image {
            width: 100%;
            height: auto;
            /* Biar tingginya otomatis ngikutin proporsi gambar asli */
            max-height: 80vh;
            /* Batas maksimal tinggi biar kada kebesaran di desktop */
            object-fit: cover;
            object-position: center;
        }

        .btn-location {
            background-color: #1a428a;
            color: white;
            border-radius: 5px;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
        }

        .btn-location.active {
            background-color: #ffd700;
            color: #000;
        }

        .product-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .promo-section {
            background-color: #3b5998;
            color: white;
            border-radius: 15px;
        }

        /* Style untuk Sticky Video */
        .sticky-video-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 250px;
            z-index: 1050;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: #000;
        }
    </style>
</head>

<body>

    @include('landingComponent.navbar')

    <main>
        @yield('content')
    </main>

    @include('landingComponent.footer')

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
