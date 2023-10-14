<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャット画面</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.15/tailwind.min.css" rel="stylesheet">

    {{-- @vite('resources/css/app.css') --}}
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-200">

    @include('layouts.manager.header')

    <div id="chat-app" class="container mx-auto px-4 py-5 max-w-3xl min-h-screen">
        <!-- React chat app will be rendered here -->
    </div>


    <script crossorigin src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.js"></script>

    <script type="text/babel">
        const { useState, useRef, useEffect } = React;

        function ChatApp() {
            const [messages, setMessages] = useState([
                { sender: "ユーザー", text: "こんにちは、よろしくお願いします。", time: "15:00" },
                { sender: "ボット", text: "はじめまして、よろしくお願いします。", time: "15:01" }
            ]);

            const messagesEndRef = useRef(null);

            const scrollToBottom = () => {
                if (messagesEndRef.current) {
                    messagesEndRef.current.scrollIntoView({ behavior: "smooth" });
                }
            };

            useEffect(scrollToBottom, [messages]);

            const [newMessage, setNewMessage] = useState('');

            const handleInputChange = (event) => {
                setNewMessage(event.target.value);
            };

            const handleSubmit = (event) => {
                event.preventDefault();
                const updatedMessages = [...messages, { sender: "ユーザー", text: newMessage, time: new Date().toLocaleTimeString() }];
                setMessages(updatedMessages);
                setNewMessage('');

                // Simulate bot response
                setTimeout(() => {
                    setMessages([...updatedMessages, { sender: "ボット", text: "テスト検証", time: new Date().toLocaleTimeString() }]);
                }, 1000);
            };

            return (
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-auto p-4 h-42rem">
                        {messages.map((message, index) => (
                            <div key={index} class={`mb-4 ${message.sender === "ボット" ? "text-right" : ""}`}>
                                <div class={`inline-block ${message.sender === "ボット" ? "bg-green-500" : "bg-blue-500"} text-white rounded-full px-4 py-2 mb-2`}>{message.text}</div>
                                <div class="text-sm text-gray-500">{message.sender}, {message.time}</div>
                            </div>
                        ))}
                        <div ref={messagesEndRef} />
                    </div>

                    <div class="border-t border-gray-200 p-4">
                        <form onSubmit={handleSubmit}>
                            <div class="flex items-center">
                                <input onChange={handleInputChange} value={newMessage} class="flex-grow rounded-full py-2 px-4 mr-4 border border-gray-300" type="text" placeholder="メッセージを入力してください" />
                                <button class="bg-blue-500 text-white rounded-full px-4 py-2">送信</button>
                            </div>
                        </form>
                    </div>
                </div>
            );
        }

        ReactDOM.render(
            <ChatApp />,
            document.getElementById('chat-app')
        );
    </script>

</body>
</html>
