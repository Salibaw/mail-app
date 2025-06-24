@once
@push('modals')
<div class="modal fade" id="showSuratMasukModal" tabindex="-1" aria-labelledby="showSuratMasukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showSuratMasukModalLabel">Detail Surat Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nomor Agenda:</strong> <span id="show_sm_nomor_agenda"></span></p>
                <p><strong>Nomor Surat Asli:</strong> <span id="show_sm_nomor_surat"></span></p>
                <p><strong>Tanggal Surat Asli:</strong> <span id="show_sm_tanggal_surat"></span></p>
                <p><strong>Tanggal Terima:</strong> <span id="show_sm_tanggal_terima"></span></p>
                <p><strong>Pengirim:</strong> <span id="show_sm_pengirim"></span></p>
                <p><strong>Perihal:</strong> <span id="show_sm_perihal"></span></p>
                <p><strong>Status:</strong> <span id="show_sm_status"></span></p>
                <p><strong>Sifat Surat:</strong> <span id="show_sm_sifat_surat"></span></p>
                <p><strong>Dicatat Oleh:</strong> <span id="show_sm_dicatat_oleh"></span></p>
                <p><strong>Dicatat Pada:</strong> <span id="show_sm_dicatat_pada"></span></p>

                <hr>
                <h5>Isi Ringkas:</h5>
                <div id="show_sm_isi_ringkas" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;"></div>

                <h5 class="mt-3">Lampiran:</h5>
                <div id="show_sm_lampiran"></div>

                <h5 class="mt-3">Riwayat Disposisi:</h5>
                <ul id="show_sm_riwayat_disposisi" class="list-group">
                    {{-- Riwayat akan diisi oleh JS --}}
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush
@endonce