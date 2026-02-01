<!-- Send Surat Keluar Modal -->
<div class="modal fade" id="sendSuratKeluarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="sendSuratKeluarForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Surat Keluar</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <div class="mb-5">
                        <label class="form-label">Tanggal Kirim</label>
                        <input type="date" class="form-control" name="tanggal_kirim" id="s_tanggal_kirim" required>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Media Pengiriman (opsional)</label>
                        <input type="text" class="form-control" name="media_pengiriman" id="s_media_pengiriman" maxlength="190" placeholder="Contoh: Pos, Email, Kurir">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnDoSend">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
