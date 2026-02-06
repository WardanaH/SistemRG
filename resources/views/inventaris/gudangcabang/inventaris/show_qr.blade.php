<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Inventaris</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }
        .card {
            border-radius: 16px;
        }
        th {
            width: 40%;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0">Detail Inventaris</h5>
                </div>

                <div class="card-body px-4">

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
                                    <span class="badge bg-warning text-dark">
                                        {{ $inventaris->kondisi }}
                                    </span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $inventaris->lokasi ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Tanggal Input</th>
                            <td>
                                {{ \Carbon\Carbon::parse($inventaris->tanggal_input)->format('d M Y') }}
                            </td>
                        </tr>
                    </table>

                </div>

                <div class="card-footer text-center text-muted small">
                    Sistem Inventaris • QR Scan
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
