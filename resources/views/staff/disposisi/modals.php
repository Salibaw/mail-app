@once
@push('modals')
<div class="modal fade" id="showDisposisiModal" tabindex="-1" aria-labelledby="showDisposisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showDisposisiModalLabel">Detail Disposisi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Perihal Surat:</strong> <span id="disposisi_show_perihal_surat"></span></p>
                <p><strong>Nomor Agenda Surat:</strong> <span id="disposisi_show_nomor_agenda"></span></p>
                <p><strong>Dari (Pemberi Disposisi):</strong> <span id="disposisi_show_dari_user"></span></p>
                <p><strong>Kepada:</strong> <span id="disposisi_show_ke_user"></span></p>
                <p><strong>Tanggal Disposisi:</strong> <span id="disposisi_show_tanggal"></span></p>
                <p><strong>Status Disposisi:</strong> <span id="disposisi_show_status"></span></p>

                <hr>
                <h5>Instruksi:</h5>
                <div id="disposisi_show_instruksi" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; white-space: pre-wrap;"></div>

                <h5 class="mt-3">Lampiran Surat Masuk:</h5>
                <div id="disposisi_show_lampiran_surat"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="disposisi_show_link_surat_masuk" class="btn btn-info">Lihat Detail Surat Masuk</a>
            </div>
        </div>
    </div>
</div>
@endpush
@once