<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200">
    <nav class="bg-white p-6">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ route('manager.register') }}" class="text-lg font-semibold text-gray-900">新規登録</a>
                </div>
                <div>
                    <a href="{{ route('manager.login') }}" class="text-lg font-semibold text-gray-900">ログイン</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="flex items-center justify-center h-screen">
        <div class="bg-white p-8 rounded shadow-lg w-96">
            <h2 class="text-2xl mb-6 text-center">ログイン画面</h2>
            @if ($errors->has('login_check'))
            <p class="text-red-500 text-xs italic border-l-4 border-red-500 bg-red-50 p-4">
                {{ $errors->first('login_check') }}
            </p>
            @endif
            <form action="{{ route('manager.login') }}" method="post">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="email">メールアドレス</label>
                    <input value="{{ old('email') }}" class="shadow appearance-none border {{ $errors->has('email') ? 'border-red-500' : '' }} rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Email" name="email">
                    @if ($errors->has('email'))
                        <p class="text-red-500 text-xs italic">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="password">パスワード</label>
                    <input class="shadow appearance-none border {{ $errors->has('password') ? 'border-red-500' : '' }} rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="******************" name="password">
                    @if ($errors->has('password'))
                        <p class="text-red-500 text-xs italic">{{ $errors->first('password') }}</p>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white text-center font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        ログイン
                    </button>
                </div>
            </form>
            <div class="mt-4 text-center">
                <span class="text-gray-600">アカウントをお持ちでないですか？<br></span>
                <a href="{{ route('manager.register') }}" class="text-blue-500">>> 新規登録はこちら</a>
            </div>
        </div>
    </div>
</body>
</html>
