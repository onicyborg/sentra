<!-- Edit Surat Keluar Modal -->
<div class="modal fade" id="editSuratKeluarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <form id="editSuratKeluarForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Surat Keluar</h5>
                    <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="form-label">Nomor Surat</label>
                                <input type="text" class="form-control" name="nomor_surat" id="e_nomor_surat_sk" required maxlength="190">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="form-label">Tanggal Surat</label>
                                <input type="date" class="form-control" name="tanggal_surat" id="e_tanggal_surat_sk" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="form-label">Tujuan</label>
                                <input type="text" class="form-control" name="tujuan" id="e_tujuan_sk" required maxlength="190">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="form-label">Perihal</label>
                                <input type="text" class="form-control" name="perihal" id="e_perihal_sk" required maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-5" id="e_lampiran_group_sk">
                                <label class="form-label">Dokumen (Tambah Baru)</label>
                                <div id="e_dropzone_sk" class="border border-dashed rounded p-5 text-center cursor-pointer">
                                    <div>Tarik & letakkan berkas di sini atau klik untuk memilih</div>
                                    <div class="text-muted fs-8">PDF, DOC, DOCX, JPG, PNG</div>
                                </div>
                                <input type="file" class="form-control d-none" name="lampiran[]" id="e_lampiran_sk" multiple>
                                <div class="mt-3" id="e_lampiran_new_list_sk"></div>
                                <div class="text-muted fs-8 mt-1">Dokumen lama tetap tersimpan.</div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label">Dokumen Saat Ini</label>
                                <div id="e_lampiran_list_sk"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdateSuratKeluar">Perbarui</button>
                </div>

            </form>
        </div>
    </div>
</div>
