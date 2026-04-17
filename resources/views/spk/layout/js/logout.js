function confirmLogout(event) {
    event.preventDefault(); // Mencegah link bekerja langsung

    Swal.fire({
        title: 'Yakin ingin keluar?',
        text: "Sesi Anda akan diakhiri.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Keluar!',
        cancelButtonText: 'Batal',
        reverseButtons: true // Tombol Batal di kiri, Ya di kanan (opsional)
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika user klik "Ya", submit form logout
            document.getElementById('logout-form').submit();
        }
    });
}
