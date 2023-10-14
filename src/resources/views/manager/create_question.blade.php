<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面 - 質問作成</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind CSS -->

</head>
<body class="bg-gray-100">

    @include('layouts.manager.header')

    <div class="container mx-auto p-6">
        <div class="p-6 bg-white shadow rounded">
            <h2 class="text-xl font-semibold mb-4">質問作成画面</h2>

            <!-- Form to create a new question -->
            <form action="{{ route('manager.create_question') }}" method="post">
                @csrf

                <div class="mb-4">
                    <label for="question" class="block text-gray-700">質問</label>
                    <input type="text" name="question" id="question" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>

                <div class="mb-4">
                    <label for="options" class="block text-gray-700">選択肢</label>
                    <textarea name="options" id="options" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" required></textarea>
                    <p class="text-xs text-gray-600">選択肢は、コンマで区切ってください。<br>(例: 質問１の答え,質問2の答え,質問3の答え)</p>
                </div>
                <div class="mb-4">
                    <label for="question" class="block text-gray-700">順番(昇順)</label>
                    <input type="text" name="sort_num" id="sort_num" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-700">保存する</button>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.js"></script>
</body>
</html>
