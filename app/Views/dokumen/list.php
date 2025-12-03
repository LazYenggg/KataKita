<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KataKita - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        #tsparticles { position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(229, 231, 235, 0.5); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-gray-800 relative">

    <div id="tsparticles"></div>

    <nav class="glass-card sticky top-0 z-50 px-6 py-4 shadow-sm">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-file-word text-3xl text-blue-600"></i>
                <h1 class="text-2xl font-bold text-blue-600 tracking-tight">
                    KataKita
                </h1>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="hidden md:flex flex-col items-end">
                    <span class="text-sm font-bold text-gray-700">
                        <?= session()->get('name') ?? 'Tamu' ?>
                    </span>
                    <span class="text-xs text-green-600 flex items-center gap-1">
                        <i class="fa-solid fa-circle text-[8px]"></i> Online
                    </span>
                </div>

                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold border border-blue-200">
                    <?= strtoupper(substr(session()->get('name') ?? 'U', 0, 1)) ?>
                </div>

                <a href="<?= base_url('logout') ?>" class="text-gray-400 hover:text-red-500 transition-colors ml-2" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket text-xl"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Dokumen Saya</h2>
                <p class="text-gray-500 mt-1">Halo <b><?= session()->get('name') ?></b>, kelola file kolaborasi Anda di sini</p>
            </div>
            
            <a href="<?= base_url('create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 font-medium">
                <i class="fa-solid fa-plus"></i> Buat Baru
            </a>
        </div>

        <?php if(empty($docs)): ?>
            <div class="flex flex-col items-center justify-center h-80 text-center bg-white/80 rounded-2xl border-2 border-dashed border-gray-200">
                <div class="bg-blue-50 p-6 rounded-full mb-4 animate-pulse">
                    <i class="fa-regular fa-folder-open text-4xl text-blue-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700">Belum ada dokumen</h3>
                <p class="text-gray-400 mt-2 max-w-md">Dokumen yang Anda buat akan muncul di sini. Klik tombol "Buat Baru" untuk memulai.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach($docs as $doc): ?>
                <div class="group relative bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden cursor-pointer h-64 flex flex-col" onclick="window.location='<?= base_url('edit/' . $doc['id']) ?>'">
                    
                    <div class="absolute top-4 right-4 z-10">
                        <?php if($doc['owner_id'] == session()->get('user_id')): ?>
                            <span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-full">MILIK SAYA</span>
                        <?php else: ?>
                            <span class="bg-purple-100 text-purple-600 text-[10px] font-bold px-2 py-1 rounded-full">KOLABORASI</span>
                        <?php endif; ?>
                    </div>

                    <div class="p-6 flex-grow bg-slate-50/50 group-hover:bg-blue-50/30 transition-colors">
                        <div class="text-[11px] text-gray-400 font-mono leading-relaxed overflow-hidden h-[120px] select-none">
                            <?= strip_tags($doc['isi'] ?? '') ?>...
                        </div>
                    </div>
                    
                    <div class="p-4 bg-white border-t border-gray-100 relative z-20">
                        <h3 class="font-bold text-gray-800 truncate pr-8 group-hover:text-blue-600 transition-colors">
                            <?= $doc['judul'] ?>
                        </h3>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-400 flex items-center gap-1">
                                <i class="fa-regular fa-clock"></i> <?= date('d M, H:i', strtotime($doc['updated_at'])) ?>
                            </span>
                            <?php if($doc['owner_id'] == session()->get('user_id')): ?>
                            <button onclick="hapusDoc(event, <?= $doc['id'] ?>)" class="text-gray-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-full transition-all" title="Hapus Dokumen">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tsparticles-confetti@2.11.0/tsparticles.confetti.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.11.0/tsparticles.bundle.min.js"></script>
    
    <script>
        tsParticles.load("tsparticles", {
            background: { color: { value: "transparent" } },
            fpsLimit: 60,
            interactivity: {
                events: { onHover: { enable: true, mode: "grab" }, resize: true },
                modes: { grab: { distance: 140, links: { opacity: 0.5 } } }
            },
            particles: {
                color: { value: "#3b82f6" }, 
                links: { color: "#3b82f6", distance: 150, enable: true, opacity: 0.2, width: 1 },
                move: { enable: true, speed: 1.5, direction: "none", random: false, straight: false, outModes: { default: "bounce" } },
                number: { density: { enable: true, area: 800 }, value: 50 },
                opacity: { value: 0.3 },
                shape: { type: "circle" },
                size: { value: { min: 1, max: 3 } }
            },
            detectRetina: true
        });

        function hapusDoc(e, id) {
            e.stopPropagation();
            Swal.fire({
                title: 'Hapus dokumen ini?',
                text: "File yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('<?= base_url('delete/') ?>' + id, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'success') {
                            Swal.fire({ title: 'Terhapus!', text: 'Dokumen berhasil dihapus.', icon: 'success', confirmButtonColor: '#2563eb', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                        }
                    });
                }
            })
        }
    </script>
</body>
</html>