<!-- Edit Surat Masuk Modal -->
<div class="modal fade" id="editSuratMasukModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <form id="editSuratMasukForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Surat Masuk</h5>
                    <button type="button"
                            class="btn btn-sm btn-icon"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- ✅ AREA SCROLL -->
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="form-label">Nomor Surat</label>
                                <input type="text"
                                       class="form-control"
                                       name="nomor_surat"
                                       id="e_nomor_surat"
                                       required maxlength="190">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="form-label">Tanggal Terima</label>
                                <input type="date"
                                       class="form-control"
                                       name="tanggal_terima"
                                       id="e_tanggal_terima"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="form-label">Asal Surat</label>
                                <input type="text"
                                       class="form-control"
                                       name="asal_surat"
                                       id="e_asal_surat"
                                       maxlength="190">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-5">
                                <label class="form-label">Pengirim</label>
                                <input type="text"
                                       class="form-control"
                                       name="pengirim"
                                       id="e_pengirim"
                                       maxlength="190">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-5">
                                <label class="form-label">Perihal</label>
                                <input type="text"
                                       class="form-control"
                                       name="perihal"
                                       id="e_perihal"
                                       required maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-5" id="e_lampiran_group">
                                <label class="form-label">Lampiran (Tambah Baru)</label>

                                <div id="e_dropzone"
                                     class="border border-dashed rounded p-5 text-center cursor-pointer">
                                    <div>Tarik & letakkan berkas di sini atau klik untuk memilih</div>
                                    <div class="text-muted fs-8">
                                        Mendukung unggah banyak berkas sekaligus
                                    </div>
                                </div>

                                <input type="file"
                                       class="form-control d-none"
                                       name="lampiran[]"
                                       id="e_lampiran"
                                       multiple>

                                <div class="mt-3" id="e_lampiran_new_list"></div>
                                <div class="text-muted fs-8 mt-1">
                                    Lampiran lama tetap tersimpan. Anda dapat menambah lampiran baru jika perlu.
                                </div>
                            </div>

                            <div class="mb-1">
                                <label class="form-label">Lampiran Saat Ini</label>
                                <div id="e_lampiran_list"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END SCROLL -->

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-primary"
                            id="btnUpdateSuratMasuk">
                        Perbarui
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
