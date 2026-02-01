<!-- Disposisi Surat Masuk Modal -->
<div class="modal fade" id="disposisiSuratMasukModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="disposisiSuratMasukForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Disposisi Surat Masuk</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <div class="mb-5">
                        <label class="form-label">Tujuan Unit Kerja</label>
                        <input type="text" class="form-control" name="ke_unit" id="d_ke_unit" required maxlength="190" placeholder="Nama unit tujuan">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Catatan Disposisi (opsional)</label>
                        <textarea class="form-control" name="catatan" id="d_catatan" rows="4" maxlength="1000" placeholder="Instruksi atau catatan tambahan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnDoDisposisi">Kirim Disposisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
