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
            <h2 class="text-xl font-semibold mb-4">質問編集画面</h2>

            <!-- Form to edit a question -->
            <form action="{{ route('manager.update_question', ['id' => $question->id]) }}" method="post">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="question" class="block text-gray-700">質問</label>
                    <input type="text" name="question" id="question" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $question->question }}" required>
                </div>

                <div class="mb-4">
                    <label for="options" class="block text-gray-700">選択肢</label>
                    <textarea name="options" id="options" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3" required>@foreach($question->options as $option){{ $option->value }}{{ $loop->last ? '' : ',' }}@endforeach</textarea>
                    <p class="text-xs text-gray-600">選択肢は、コンマで区切ってください。<br>(例: 『質問の答え その１,質問の答え その２,質問の答え その3』)</p>
                </div>

                <div class="mb-4">
                    <label for="sort_num" class="block text-gray-700">順番(昇順)</label>
                    <input type="text" name="sort_num" id="sort_num" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $question->sort_num }}" required>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-700">更新する</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.js"></script>
</body>
</html>
