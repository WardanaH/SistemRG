<!-- Sticky Video Component -->
<div class="sticky-video-container shadow-lg">

    <!-- OPSI 1: Pakai Video YouTube -->
    <!-- Ambil ID videonya aja (VrPqovXZ-Lk) andak di link embed -->
    {{-- <iframe width="100%" height="100%"
            src="https://www.youtube.com/embed/VrPqovXZ-Lk?autoplay=1&mute=1&loop=1&playlist=VrPqovXZ-Lk&controls=0"
            title="YouTube video player" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe> --}}

    <!-- OPSI 2: Pakai Video Lokal (Simpan di public/assets/landing/video.mp4) -->
    <!-- Kalau handak pakai ini, hapus komennya dan komen Opsi 1 di atas -->
    <video width="100%" height="auto" autoplay muted loop controls>
        <source src="{{ asset('assets/landing/video.mp4') }}" type="video/mp4">
        Browser ikam kada support tag video.
    </video>
</div>
