<!-- Create Tindak Lanjut Modal -->
<div class="modal fade" id="createTindakLanjutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="createTindakLanjutForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tindak Lanjut Surat</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="mb-5">
                        <label class="form-label">Deskripsi Tindak Lanjut</label>
                        <textarea class="form-control" name="deskripsi" id="tl_deskripsi" rows="4" required></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Lampiran (opsional)</label>
                        <div id="tl_dropzone" class="border border-dashed rounded p-5 text-center cursor-pointer">
                            <div>Tarik & letakkan berkas di sini atau klik untuk memilih</div>
                            <div class="text-muted fs-8">Mendukung unggah banyak berkas sekaligus</div>
                        </div>
                        <input type="file" class="form-control d-none" name="lampiran[]" id="tl_lampiran" multiple>
                        <div class="mt-3" id="tl_lampiran_list"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
