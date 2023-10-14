<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面 - 質問一覧</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind CSS -->

</head>
<body class="bg-gray-100">

    @include('layouts.manager.header')

    <div class="container mx-auto p-6">
        <div class="p-6 bg-white shadow rounded">
            <h2 class="text-xl font-semibold mb-4">質問一覧</h2>

            <!-- Questions list -->
            @forelse($questions as $question)
                <div class="p-4 border rounded my-4">
                    <h3 class="text-lg font-semibold">順番: {{ $question->sort_num }}</h3>
                    <h3 class="text-lg font-semibold">質問: {{ $question->question }}</h3>
                    <p class="mt-2">選択肢:</p>
                    <ul>
                        @foreach($question->options as $option)
                            <li class="ml-4">{{ $option->value }}</li>
                        @endforeach
                    </ul><br>
                    <a href="{{ route('manager.edit_question', ['id' => $question->id]) }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                        編集
                    </a>
                </div>
            @empty
                <p>質問が登録されていません。</p>
            @endforelse
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.js"></script>
</body>
</html>
