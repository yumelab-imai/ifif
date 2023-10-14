<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面 - Top</title>
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
                <h2 class="text-xl font-semibold mb-4">ログインユーザー情報</h2>

                <dl>
                    <dt class="font-semibold">Eメール:</dt>
                    <dd class="mb-2">{{ Auth::guard('manager')->user()->email }}</dd>

                    <dt class="font-semibold">名字:</dt>
                    <dd class="mb-2">ifif</dd>
                    <dd class="mb-2">{{ Auth::guard('manager')->user()->last_name }}</dd>

                    <dt class="font-semibold">名前:</dt>
                    <dd class="mb-2">太郎</dd>
                    <dd class="mb-2">{{ Auth::guard('manager')->user()->first_name }}</dd>

                    <dt class="font-semibold">名字(カナ):</dt>
                    <dd class="mb-2">イフイフ</dd>
                    <dd class="mb-2">{{ Auth::guard('manager')->user()->last_name_kana }}</dd>

                    <dt class="font-semibold">名前(カナ):</dt>
                    <dd class="mb-2">タロウ</dd>
                    <dd class="mb-2">{{ Auth::guard('manager')->user()->first_name_kana }}</dd>

                    <dt class="font-semibold">チャネルID:</dt>
                    <dd class="mb-2">{{ $decryptedChannel_id }}</dd>

                    <dt class="font-semibold">チャネルシークレットトークン:</dt>
                    <dd class="mb-2">{{ $decryptedChannel_secret }}</dd>

                    <dt class="font-semibold">チャネルアクセストークン:</dt>
                    <dd class="mb-2">{{ $decryptedChannel_token }}</dd>

                    <dt class="font-semibold">Webhook URL</dt>
                    <dd class="mb-2">{{ $webhookURL }}</dd>

                </dl>
            </div>
        @else
            <p>ログインしてください。</p>
        @endauth
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.js"></script>
</body>
</html>
