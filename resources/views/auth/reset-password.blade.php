<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-3xl shadow-lg border">
        <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>

        @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" value="{{ $email }}" disabled
                    class="w-full px-4 py-3 bg-gray-100 rounded-xl outline-none border-none text-gray-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Password Baru</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 bg-green-50 rounded-xl outline-none focus:ring-2 focus:ring-sky-300">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 bg-green-50 rounded-xl outline-none focus:ring-2 focus:ring-sky-300">
            </div>

            <button type="submit"
                class="w-full bg-sky-400 text-black font-bold py-3 rounded-full hover:bg-sky-500 transition-all shadow-md">
                Simpan Password
            </button>
        </form>
    </div>
</body>

</html>