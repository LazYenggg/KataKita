<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - KataKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-96 border border-gray-100">
        <div class="text-center mb-8">
            <i class="fa-solid fa-file-word text-4xl text-blue-600 mb-2"></i>
            <h1 class="text-2xl font-bold text-blue-600">KataKita</h1>
            <p class="text-gray-400 text-sm">Masuk untuk berkolaborasi</p>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="bg-red-100 text-red-500 p-3 rounded-lg mb-4 text-sm text-center">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('success')): ?>
            <div class="bg-green-100 text-green-500 p-3 rounded-lg mb-4 text-sm text-center">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-semibold mb-2">Username</label>
                <input type="text" name="username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-600 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-bold">Masuk</button>
        </form>
        <div class="mt-6 text-center text-sm">
            Belum punya akun? <a href="<?= base_url('register') ?>" class="text-blue-600 font-bold hover:underline">Daftar</a>
        </div>
    </div>
</body>
</html>