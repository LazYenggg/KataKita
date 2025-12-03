<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar - KataKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-96 border border-gray-100">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-blue-600">Buat Akun</h1>
            <p class="text-gray-400 text-sm">Bergabung dengan KataKita</p>
        </div>

        <?php if(session()->getFlashdata('errors')): ?>
            <div class="bg-red-50 text-red-500 p-3 rounded-lg mb-4 text-xs">
                <ul>
                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                    <li>• <?= esc($error) ?></li>
                <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('register') ?>" method="post">
            <div class="mb-3">
                <label class="block text-gray-600 text-xs font-bold mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-3">
                <label class="block text-gray-600 text-xs font-bold mb-1">Username</label>
                <input type="text" name="username" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-600 text-xs font-bold mb-1">Password</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-bold">Daftar</button>
        </form>
        <div class="mt-4 text-center text-sm">
            Sudah punya akun? <a href="<?= base_url('login') ?>" class="text-blue-600 font-bold hover:underline">Login</a>
        </div>
    </div>
</body>
</html>