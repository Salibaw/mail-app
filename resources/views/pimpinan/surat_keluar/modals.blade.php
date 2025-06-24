@once
@push('modals')
<div class="modal fade" id="showSuratKeluarModal" tabindex="-1" aria-labelledby="showSuratKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratKeluarModalLabel">Detail Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Perihal:</strong> <span id="show_sk_perihal"></span></p>
                <p><strong>Penerima:</strong> <span id="show_sk_penerima"></span></p>
                <p><strong>Diajukan Oleh:</strong> <span id="show_sk_diajukan_oleh"></span></p>
                <p><strong>Status:</strong> <span id="show_sk_status"></span></p>
                <p><strong>Sifat Surat:</strong> <span id="show_sk_sifat_surat"></span></p>
                <p><strong>Nomor Surat:</strong> <span id="show_sk_nomor_surat"></span></p>
                <p><strong>Tanggal Surat:</strong> <span id="show_sk_tanggal_surat"></span></p>
                <p><strong>Diajukan Pada:</strong> <span id="show_sk_diajukan_pada"></span></p>

                <hr>
                <h5>Isi Surat:</h5>
                <div id="show_sk_isi_surat" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;"></div>

                <h5 class="mt-3">Lampiran:</h5>
                <div id="show_sk_lampiran"></div>

                <h5 class="mt-3">Riwayat Persetujuan/Penolakan:</h5>
                <ul id="show_sk_riwayat_persetujuan" class="list-group">
                    {{-- Riwayat akan diisi oleh JS --}}
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btn_sk_approve" data-bs-toggle="modal" data-bs-target="#approveSuratKeluarModal" data-id="" style="display:none;">Setujui</button>
                <button type="button" class="btn btn-danger" id="btn_sk_reject" data-bs-toggle="modal" data-bs-target="#rejectSuratKeluarModal" data-id="" style="display:none;">Tolak</button>
                <a href="#" class="btn btn-primary" id="btn_sk_download_pdf" target="_blank" style="display:none;">Unduh PDF</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approveSuratKeluarModal" tabindex="-1" aria-labelledby="approveSuratKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveSuratKeluarModalLabel">Setujui Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveSuratKeluarForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui surat ini?</p>
                    <div class="mb-3">
                        <label for="catatan_persetujuan" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan_persetujuan" name="catatan_persetujuan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectSuratKeluarModal" tabindex="-1" aria-labelledby="rejectSuratKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectSuratKeluarModalLabel">Tolak Surat Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectSuratKeluarForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak surat ini?</p>
                    <div class="mb-3">
                        <label for="catatan_penolakan" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" id="catatan_penolakan" name="catatan_penolakan" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endonce