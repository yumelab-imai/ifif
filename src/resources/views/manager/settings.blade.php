<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面 - 設定</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind CSS -->

</head>
<body class="bg-gray-100">
    @include('layouts.manager.header')

    <div class="container mx-auto p-6">
        @auth('manager')
            <div class="p-6 bg-white shadow rounded">
                <h2 class="text-xl font-semibold mb-4">ユーザー設定</h2>

                @if (session('success'))
                    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded">
                        <strong class="font-bold">情報を更新しました</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded">
                        <strong class="font-bold">更新処理が失敗しました</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('manager.settings.update') }}">
                    @csrf

                    <div>
                        <label for="last_name" class="font-semibold">名字:</label>
                        <input id="last_name" type="text" name="last_name" value="{{ $manager->last_name }}">
                    </div>

                    <div>
                        <label for="first_name" class="font-semibold">名前:</label>
                        <input id="first_name" type="text" name="first_name" value="{{ $manager->first_name }}">
                    </div>

                    <div>
                        <label for="last_name_kana" class="font-semibold">名字(カナ):</label>
                        <input id="last_name_kana" type="text" name="last_name_kana" value="{{ $manager->last_name_kana }}">
                    </div>

                    <div>
                        <label for="first_name_kana" class="font-semibold">名前(カナ):</label>
                        <input id="first_name_kana" type="text" name="first_name_kana" value="{{ $manager->first_name_kana }}">
                    </div>

                    <div>
                        <label for="channel_id" class="font-semibold">チャネルID:</label>
                        <input id="channel_id" type="text" name="channel_id" value="{{ $decryptedChannel_id }}">
                    </div>

                    <div>
                        <label for="channel_secret" class="font-semibold">チャネルシークレット:</label>
                        <input id="channel_secret" type="text" name="channel_secret" value="{{ $decryptedChannel_secret }}">
                    </div>

                    <div>
                        <label for="channel_token" class="font-semibold">チャネルアクセストークン:</label>
                        <input id="channel_token" type="text" name="channel_token" value="{{ $decryptedChannel_token }}">
                    </div>

                    <!-- Add other fields as necessary -->

                    <div class="mt-4">
                        <button type="submit" class="text-white bg-blue-500 rounded px-4 py-2">更新</button>
                    </div>
                </form>
            </div>
        @else
            <p>ログインしてください。</p>
        @endauth
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.js"></script>

    <!-- Scripts omitted for brevity -->
    <script>
        // 成功メッセージを5秒後に消す
        setTimeout(function() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>
