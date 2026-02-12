<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Inventaris</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Background full page */
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('{{ asset("storage/RGlogo.webp") }}') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Overlay blur & semi-transparan */
        body::before {
            content: '';
            position: absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(12px);
            z-index: 0;
        }

        /* Card style */
        .card {
            border-radius: 20px;
            overflow: hidden;
            z-index: 1;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border: none;
            background-color: rgba(255,255,255,0.95);
            animation: fadeIn 0.8s ease;
        }

        /* Card header gradient */
        .card-header {
            background: linear-gradient(135deg, #FFD54F, #FFA726);
            color: #fff;
            text-align: center;
            font-weight: 600;
            font-size: 1.2rem;
        }

        /* Table style */
        table {
            width: 100%;
            margin-bottom: 0;
        }
        th {
            width: 40%;
            font-weight: 600;
            color: #555;
        }
        td {
            color: #333;
        }

        /* Badge modern */
        .badge {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        /* Foto inventaris */
        .img-inventaris {
            max-width: 100%;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s;
        }
        .img-inventaris:hover {
            transform: scale(1.05);
        }

        /* Footer */
        .card-footer {
            font-size: 0.85rem;
            color: #666;
            background: transparent;
            border-top: none;
        }

        /* Animasi fade-in */
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity:1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="container position-relative">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            <div class="card">
                <div class="card-header">
                    Detail Inventaris
                </div>
                <div class="card-body px-4 py-4">
                    <div class="row align-items-center">

                        {{-- FOTO --}}
                        <div class="col-md-5 text-center mb-3">
                            @if($inventaris->foto)
                                <img src="{{ asset('storage/'.$inventaris->foto) }}"
                                    class="img-inventaris"
                                    alt="Foto Inventaris">
                            @else
                                <div class="text-muted">Tidak ada foto</div>
                            @endif
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="col-md-7">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th>Kode Barang</th>
                                    <td>{{ $inventaris->kode_barang }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Barang</th>
                                    <td>{{ $inventaris->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah</th>
                                    <td>{{ $inventaris->jumlah }}</td>
                                </tr>
                                <tr>
                                    <th>Kondisi</th>
                                    <td>
                                        @if($inventaris->kondisi === 'Baik')
                                            <span class="badge bg-success">Baik</span>
                                        @elseif($inventaris->kondisi === 'Rusak Berat')
                                            <span class="badge bg-danger">Rusak Berat</span>
                                        @else
                                            <span class="badge bg-primary text-dark">
                                                {{ $inventaris->kondisi }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>{{ $inventaris->cabang->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Input</th>
                                    <td>{{ \Carbon\Carbon::parse($inventaris->tanggal_input)->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="card-footer text-center">
                    Sistem Inventaris • QR Scan
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
