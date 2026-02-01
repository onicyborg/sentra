<!-- Approve Surat Keluar Modal -->
<div class="modal fade" id="approveSuratKeluarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="approveSuratKeluarForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Approval Surat Keluar</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <div class="mb-5">
                        <label class="form-label">Catatan Persetujuan (opsional)</label>
                        <textarea class="form-control" name="catatan" id="a_catatan" rows="4" maxlength="1000" placeholder="Tambahkan catatan persetujuan atau alasan penolakan"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aksi</label>
                        <div class="d-flex gap-4">
                            <label class="form-check form-check-custom form-check-solid align-items-center">
                                <input class="form-check-input" type="radio" name="aksi" value="approve" checked>
                                <span class="ms-2">Setujui</span>
                            </label>
                            <label class="form-check form-check-custom form-check-solid align-items-center">
                                <input class="form-check-input" type="radio" name="aksi" value="reject">
                                <span class="ms-2">Tolak</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnDoApprove">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>
