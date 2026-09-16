<section class="container text-center mt-4">
    <span class="badge bg-warning text-dark px-3 py-2 mb-3">TEMUKAN LOKASI KAMI</span>
    <div class="row justify-content-center gap-2 px-3">
        <button class="col-md-2 btn-location" onclick="showLocation('liang')">CAB. LIANG ANGGANG</button>
        <button class="col-md-2 btn-location" onclick="showLocation('martapura')" id="btn-martapura">CAB.
            MARTAPURA</button>
        <button class="col-md-2 btn-location" onclick="showLocation('banjarbaru')">CAB. BANJARBARU</button>
        <button class="col-md-2 btn-location" onclick="showLocation('banjarmasin')">CAB. BANJARMASIN</button>
    </div>

    <!-- Info Bar Kuning (Awalnya tersembunyi/d-none) -->
    <div id="location-bar"
        class="bg-warning text-dark mt-3 p-2 d-none fw-bold small d-flex justify-content-between align-items-center px-4">
        <span id="loc-address">Alamat akan muncul di sini</span>
        <a id="loc-link" href="#" target="_blank" class="text-dark text-decoration-none"><i
                class="fas fa-map-marker-alt"></i> Buka Maps</a>
    </div>
</section>
