<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($doc['judul']) ?> - KataKita</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style>
        /* Custom Styles untuk menimpa Bootstrap agar cocok dengan Tailwind */
        body { background-color: #f8fafc; font-family: sans-serif; overflow: hidden; height: 100vh; display: flex; flex-direction: column; }
        
        /* Area Editor (Kertas) */
        .editor-wrapper { flex: 1; overflow-y: auto; padding: 2rem; display: flex; justify-content: center; background: #f1f5f9; }
        .page-sheet { width: 100%; max-width: 816px; /* Ukuran A4 */ background: white; min-height: 1056px; padding: 40px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); margin-bottom: 3rem; }
        
        /* Summernote Overrides */
        .note-editor.note-frame { border: none !important; box-shadow: none !important; }
        .note-toolbar { position: sticky; top: 0; z-index: 50; background: white !important; border-bottom: 1px solid #e2e8f0 !important; padding: 0.5rem !important; width: 100%; }
        .note-editable { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.6; }
        .note-resizebar { display: none; }
    </style>
</head>
<body>

    <nav class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 z-50">
        <div class="flex items-center gap-4 flex-1">
            <a href="<?= base_url('/') ?>" class="text-slate-400 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-full transition-colors" title="Kembali ke Dashboard">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            
            <div class="flex items-center gap-3 w-full max-w-xl">
                <i class="fa-solid fa-file-word text-2xl text-blue-600"></i>
                
                <div class="flex-1 group relative">
                    <input type="text" id="docTitle" value="<?= esc($doc['judul']) ?>" 
                           class="w-full font-semibold text-slate-700 text-lg border-b border-transparent hover:border-slate-300 focus:border-blue-500 focus:outline-none px-1 py-0.5 bg-transparent transition-all"
                           onblur="renameDoc()" onkeypress="if(event.key === 'Enter') this.blur()">
                    <span class="absolute right-0 top-1/2 -translate-y-1/2 text-xs text-slate-300 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        <i class="fa-solid fa-pen"></i> Rename
                    </span>
                </div>
                
                <div id="saveStatus" class="text-xs text-slate-400 font-medium flex items-center gap-1 min-w-[100px]">
                    <i class="fa-solid fa-cloud"></i> Tersimpan
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="openShareModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm flex items-center gap-2 transition-all active:scale-95">
                <i class="fa-solid fa-user-plus"></i>
                <span>Bagikan</span>
            </button>

            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-md border-2 border-white" title="<?= session()->get('name') ?>">
                <?= strtoupper(substr(session()->get('name') ?? 'U', 0, 2)) ?>
            </div>
        </div>
    </nav>

    <div class="editor-wrapper">
        <div class="page-sheet">
            <textarea id="summernote"><?= $doc['isi'] ?></textarea>
        </div>
    </div>

    <script>
        var docId = <?= $doc['id'] ?>;
        var isTyping = false; 
        var lastContent = ''; // Untuk menyimpan isi terakhir yang kita tahu

        $(document).ready(function() {
            // 1. Inisialisasi Summernote
            $('#summernote').summernote({
                placeholder: 'Mulai mengetik dokumen Anda di sini...',
                tabsize: 2,
                height: 950,
                focus: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear', 'fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph', 'height']],
                    ['insert', ['link', 'picture', 'table', 'hr']],
                    ['view', ['fullscreen', 'help']]
                ],
                callbacks: {
                    // Saat user mulai mengetik/mengubah isi
                    onChange: function(contents) {
                        setStatus('saving');
                        isTyping = true;
                        lastContent = contents; // Update data lokal
                        
                        // Debounce: Tunggu 1 detik berhenti ngetik baru simpan
                        clearTimeout($.data(this, 'timer'));
                        var wait = setTimeout(saveContent, 1000);
                        $(this).data('timer', wait);
                    }
                }
            });

            // Set isi awal
            lastContent = $('#summernote').summernote('code');

            // 2. Fungsi Simpan Isi (AJAX POST)
            function saveContent() {
                var content = $('#summernote').summernote('code');
                $.post('<?= base_url("update/") ?>' + docId, { isi: content }, function(data) {
                    if(data.status === 'success') {
                        setStatus('saved');
                        isTyping = false;
                    }
                });
            }

            // 3. Fungsi Auto-Sync (AJAX GET - Polling)
            // Cek setiap 2 detik (Biar terasa lebih real-time)
            setInterval(function() {
                // Hanya terima update jika user sedang TIDAK mengetik
                if(!isTyping) { 
                    $.get('<?= base_url("get-content/") ?>' + docId, function(data) {
                        var currentEditorContent = $('#summernote').summernote('code');
                        
                        // Cek apakah data dari server BEDA dengan yang ada di editor?
                        // Kita bandingkan length-nya biar cepat, atau isi string-nya langsung
                        if (data.isi && data.isi !== currentEditorContent) {
                            
                            console.log('Ada perubahan dari user lain, mengupdate editor...');
                            
                            // [INTI PERBAIKAN]: Update isi editor dengan data server
                            $('#summernote').summernote('code', data.isi);
                            
                            // Update variabel lokal biar gak looping
                            lastContent = data.isi; 

                            // Opsional: Kasih notifikasi kecil (Toast)
                            const Toast = Swal.mixin({
                                toast: true, position: 'top-end', 
                                showConfirmButton: false, timer: 2000,
                                background: '#dcfce7', color: '#166534'
                            });
                            Toast.fire({icon: 'success', title: 'Data disinkronkan'});
                        }
                    });
                }
            }, 2000); // 2000ms = 2 detik
        });

        // --- (Fungsi renameDoc, setStatus, dll biarkan sama seperti sebelumnya) ---
        
        function renameDoc() {
            var newTitle = document.getElementById('docTitle').value;
            $.post('<?= base_url("rename/") ?>' + docId, { judul: newTitle }, function(data) {
                if(data.status === 'success') {
                    const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 2000});
                    Toast.fire({icon: 'success', title: 'Judul diubah'});
                }
            });
        }

        function setStatus(state) {
            const el = $('#saveStatus');
            if(state === 'saving') {
                el.html('<i class="fa-solid fa-circle-notch fa-spin text-yellow-500"></i> <span class="text-slate-500">Menyimpan...</span>');
            } else if(state === 'saved') {
                el.html('<i class="fa-solid fa-check text-green-500"></i> <span class="text-slate-500">Tersimpan</span>');
                setTimeout(() => {
                    el.html('<i class="fa-solid fa-cloud text-slate-400"></i> <span class="text-slate-400">Tersimpan</span>');
                }, 2000);
            }
        }

        function openShareModal() {
            // (Kode Modal Share sama persis seperti sebelumnya)
            Swal.fire({
                title: '<strong>Bagikan Dokumen</strong>',
                html: `
                    <div class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Undang Kolaborator</label>
                            <div class="flex gap-2">
                                <input type="text" id="inviteUser" class="swal2-input m-0 w-full" placeholder="Masukkan username teman">
                                <button onclick="inviteUser()" class="swal2-confirm swal2-styled bg-blue-600 hover:bg-blue-700 m-0 px-4">Undang</button>
                            </div>
                        </div>
                        <hr class="my-4 border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800">Link Publik</h4>
                                <p class="text-xs text-gray-500">Siapapun yang punya link bisa melihat</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="togglePublic" class="sr-only peer" onchange="togglePublicLink()" <?= $doc['is_public'] ? 'checked' : '' ?>>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        
                        <div id="publicLinkBox" class="mt-3 <?= $doc['is_public'] ? '' : 'hidden' ?>">
                            <div class="flex items-center gap-2 bg-gray-100 p-2 rounded border">
                                <input type="text" value="<?= base_url('edit/'.$doc['id']) ?>" class="bg-transparent text-sm w-full text-gray-600 outline-none" readonly>
                                <button onclick="navigator.clipboard.writeText('<?= base_url('edit/'.$doc['id']) ?>')" class="text-blue-600 hover:text-blue-800 text-sm font-bold">Copy</button>
                            </div>
                        </div>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false,
                focusConfirm: false
            });
        }

        function inviteUser() {
            var username = document.getElementById('inviteUser').value;
            if(!username) return;

            $.post('<?= base_url("share/invite/") ?>' + docId, { username: username }, function(data) {
                if(data.status === 'success') {
                    Swal.fire('Berhasil!', `User <b>${username}</b> telah ditambahkan.`, 'success');
                } else {
                    Swal.showValidationMessage(data.msg);
                }
            });
        }

        function togglePublicLink() {
            var isPublic = document.getElementById('togglePublic').checked ? 1 : 0;
            var box = document.getElementById('publicLinkBox');
            if(isPublic) box.classList.remove('hidden');
            else box.classList.add('hidden');
            $.post('<?= base_url("share/public/") ?>' + docId, { is_public: isPublic }, function(data) {});
        }
    </script>
</body>
</html>