<nav class="bg-white p-6">
    <div class="container mx-auto">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('manager.top') }}" class="text-lg font-semibold text-gray-900">管理画面</a>
            </div>

            @auth('manager')
                <div>
                    <a href="{{ route('manager.settings') }}" class="text-gray-500">設定</a>
                </div>
                {{-- <div>
                    <a href="{{ route('manager.line_info') }}" class="text-gray-500">LINE管理アカウント</a>
                </div> --}}

                <!-- Dropdown menu -->
                <div>
                    <button id="questionDropdownButton" data-dropdown-toggle="questionDropdown" class="text-gray-500 hover:text-gray-900 font-medium text-sm px-2 py-1 text-center inline-flex items-center" type="button">質問管理 <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg></button>

                    <!-- Dropdown menu -->
                    <div id="questionDropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44">
                        <ul class="py-2 text-sm text-gray-700" aria-labelledby="questionDropdownButton">
                            <li>
                                <a href="{{ route('manager.create_question') }}" class="block px-4 py-2 hover:bg-blue-100">質問作成</a>
                            </li>
                            <li>
                                <a href="{{ route('manager.question_list') }}" class="block px-4 py-2 hover:bg-blue-100">質問一覧</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div>
                    <a href="{{ route('manager.line_info') }}" class="text-gray-500">ユーザーチャット(未実装)</a>
                </div>
                <div>
                    <a href="{{ route('manager.analysis') }}" class="text-gray-500">回答分析(未実装)</a>
                </div>
                <div class="ml-4">
                    <form method="POST" action="{{ route('manager.logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500">ログアウト</button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>
