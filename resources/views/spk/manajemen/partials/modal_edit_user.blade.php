<div class="modal fade" id="modalEditUser" tabindex="-1" role="dialog" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info">
                <h5 class="modal-title text-white" id="modalEditUserLabel">Edit User</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Form Edit --}}
            <form id="formEditUser" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <p class="text-sm text-uppercase font-weight-bold mb-3">Informasi Akun</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" id="edit_username" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Password (Isi jika ingin ubah)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <select name="role" id="edit_role" class="form-control" style="appearance: auto; padding-left: 10px;">
                                    <option value="" disabled>Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <select name="cabang_id" id="edit_cabang_id" class="form-control" style="appearance: auto; padding-left: 10px;">
                                    <option value="">Pilih Cabang</option>
                                    @foreach($cabangs as $c)
                                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-3">
                    <p class="text-sm text-uppercase font-weight-bold mb-3">Informasi Pribadi</p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" id="edit_telepon" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Gaji</label>
                                <input type="text" name="gaji" id="edit_gaji" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="alamat" id="edit_alamat" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-info">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
