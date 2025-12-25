<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('') }}/assets/images/favicon.png">
    <title>@yield('title')</title>
    {{-- {{ config('app.name') }} --}}

    <!-- Include styles  -->
    @include('partials.styles')
    @yield('custom-style')
    <!-- End styles -->

    <!-- AI Chatbot Styles -->
    <style>
        /* Floating AI Button */
        .ai-chatbot-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ai-chatbot-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(102, 126, 234, 0.6);
        }

        .ai-chatbot-btn:active {
            transform: scale(0.95);
        }

        /* Chatbot Modal */
        .ai-chatbot-modal {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 420px;
            height: 650px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            z-index: 9998;
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }

        .ai-chatbot-modal.active {
            display: flex;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Chatbot Header */
        .chatbot-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .chatbot-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .chatbot-header h6 {
            color: white;
            font-weight: 600;
            font-size: 15px;
        }

        .chatbot-header small {
            color: rgba(255, 255, 255, 0.85);
            font-size: 12px;
        }

        .close-chatbot {
            background: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            transition: all 0.2s ease;
        }

        .close-chatbot:hover {
            transform: rotate(90deg);
        }

        /* Quick Actions */
        .quick-actions {
            border-bottom: 1px solid #e9ecef;
        }

        .quick-btn {
            font-size: 11px;
            padding: 8px 4px;
            border-radius: 8px;
        }

        /* Messages Area */
        .chatbot-messages {
            flex: 1;
            padding: 20px 15px;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .message-wrapper {
            display: flex;
            margin-bottom: 16px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .avatar-container {
            flex-shrink: 0;
        }

        .bot-avatar,
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        .user-avatar {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }

        .message-content {
            margin-left: 10px;
            flex: 1;
        }

        .user-message .message-content {
            margin-left: 0;
            margin-right: 10px;
            display: flex;
            justify-content: flex-end;
        }

        .message-bubble {
            background: white;
            padding: 10px 14px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            max-width: 85%;
        }

        .message-bubble.user {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
        }

        .message-text {
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 4px;
        }

        .message-time {
            font-size: 10px;
            opacity: 0.6;
            text-align: right;
        }

        .user-message {
            flex-direction: row-reverse;
        }

        /* Typing Indicator */
        .typing-dots {
            display: flex;
            gap: 4px;
            padding: 4px 0;
        }

        .typing-dots span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #667eea;
            animation: typing 1.4s infinite;
        }

        .typing-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-6px); }
        }

        /* Input Area */
        .chatbot-input-area {
            padding: 12px 15px;
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .chatbot-input {
            border-radius: 20px;
            border: 1px solid #e9ecef;
            padding: 10px 16px;
            font-size: 13px;
        }

        .chatbot-input:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            border-color: #667eea;
            outline: none;
        }

        .voice-btn {
            border-radius: 50%;
            width: 38px;
            height: 38px;
            padding: 0;
            margin-right: 8px;
        }

        .chatbot-send-btn {
            border-radius: 50%;
            width: 38px;
            height: 38px;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            margin-left: 8px;
        }

        .chatbot-send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .status-area {
            margin-top: 6px;
            min-height: 18px;
        }

        /* Scrollbar */
        .chatbot-messages::-webkit-scrollbar {
            width: 5px;
        }

        .chatbot-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chatbot-messages::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ai-chatbot-modal {
                width: calc(100% - 30px);
                right: 15px;
                bottom: 90px;
                height: 600px;
            }

            .ai-chatbot-btn {
                right: 15px;
                bottom: 15px;
            }
        }
    </style>

</head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">




        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        @include('partials.sidebar')
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->


        <!-- Mobile Header -->
        <header class="mobile-topbar d-md-none d-print-none">
            <button class="mobile-menu-toggle" id="sidebarToggle">
                <i class="mdi mdi-menu"></i>
            </button>
            <a class="mobile-brand" href="{{ url('/dashboard') }}">
                <img src="{{ url('') }}/assets/images/bank.webp" width="32" height="32" alt="Bank Logo" />
                <span>Online Banking</span>
            </a>
            <a href="{{route('profile')}}" class="mobile-user-avatar">
                <img src="{{ url('') }}/assets/images/users/oms.jpeg" alt="user" />
            </a>
        </header>
        <!-- Mobile Overlay -->
        <div class="sidebar-overlay d-md-none" id="sidebarOverlay"></div>

        <!-- Page wrapper  -->
        <div class="page-wrapper ">

            <!-- Page Title -->
            <div class="page-breadcrumb d-print-none">
                <div class="row align-items-center">
                    <div class="col-12">
                        <h4 class="page-title">@yield('title')</h4>
                    </div>
                </div>
            </div>


            <!-- Container fluid  -->
            <div class="container-fluid">
                @include('partials.alert')
                @yield('content')
            </div>
            <!-- End Container fluid  -->

            <!-- footer -->
            @include('partials.footer')
            <!-- End footer -->

        </div>
        <!-- End Page wrapper  -->
    </div>
    <!-- End Wrapper -->

    <!-- Floating AI Chatbot Button -->
    <button class="ai-chatbot-btn" id="aiChatbotBtn" title="AI Assistant">
        <i class="fas fa-robot"></i>
    </button>

    <!-- AI Chatbot Modal -->
    <div class="ai-chatbot-modal" id="aiChatbotModal">
        <div class="chatbot-header bg-gradient-primary">
            <div class="d-flex align-items-center">
                <div class="chatbot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="ml-3">
                    <h6 class="mb-0">🤖 Akıllı Asistan</h6>
                    <small>AI destekli bankacılık asistanı</small>
                </div>
            </div>
            <button class="close-chatbot" id="closeChatbot">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Quick Action Buttons -->
        <div class="quick-actions p-3 bg-light">
            <div class="row g-2">
                <div class="col-4">
                    <button class="btn btn-outline-primary btn-sm w-100 quick-btn" data-message="bakiyemi göster">
                        <i class="fas fa-wallet"></i> Bakiye
                    </button>
                </div>
                <div class="col-4">
                    <button class="btn btn-outline-success btn-sm w-100 quick-btn" data-message="transfer yapmak istiyorum">
                        <i class="fas fa-exchange-alt"></i> Transfer
                    </button>
                </div>
                <div class="col-4">
                    <button class="btn btn-outline-info btn-sm w-100 quick-btn" data-message="kartlarımı göster">
                        <i class="fas fa-credit-card"></i> Kartlar
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="message-wrapper bot-message">
                <div class="avatar-container">
                    <div class="bot-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
                <div class="message-content">
                    <div class="message-bubble bot">
                        <div class="message-text">
                            Merhaba! 👋 Ben sizin kişisel bankacılık asistanınızım.
                            Hesap bakiyenizden para transferine, kartlarınızdan işlem geçmişinize kadar
                            tüm bankacılık sorularınızda size yardımcı olabilirim.
                            <br><br>
                            Başlamak için yukarıdaki hızlı erişim butonlarını kullanabilir
                            veya doğrudan soru sorabilirsiniz! 💬
                        </div>
                        <div class="message-time">Şimdi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="typingIndicator" class="typing-indicator-wrapper" style="display: none;">
            <div class="message-wrapper bot-message">
                <div class="avatar-container">
                    <div class="bot-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
                <div class="message-content">
                    <div class="message-bubble bot typing">
                        <div class="typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input-area">
            <form id="chatbotForm" onsubmit="return false;">
                <div class="input-group">
                    <button class="btn btn-outline-secondary voice-btn" type="button" id="voiceButton" title="Sesli komut">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <input type="text" class="form-control chatbot-input" id="chatbotInput"
                           placeholder="Mesajınızı yazın..." autocomplete="off">
                    <button class="btn btn-primary chatbot-send-btn" type="button" id="chatbotSendButton">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
            <div class="status-area">
                <small class="text-muted" id="chatbotStatus"></small>
            </div>
        </div>
    </div>



    <!-- All Jquery -->

    <!-- For Ziggy -->
    @routes
    <!-- End Ziggy -->

    {{-- Scripts --}}
    @include('partials.scripts')
    {{-- End Scripts --}}


    @yield('custom-script')

    <!-- AI Chatbot Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chatbotBtn = document.getElementById('aiChatbotBtn');
            var chatbotModal = document.getElementById('aiChatbotModal');
            var closeChatbot = document.getElementById('closeChatbot');
            var chatbotInput = document.getElementById('chatbotInput');
            var chatbotSendButton = document.getElementById('chatbotSendButton');
            var chatbotMessages = document.getElementById('chatbotMessages');
            var chatbotStatus = document.getElementById('chatbotStatus');
            var typingIndicator = document.getElementById('typingIndicator');
            var chatbotUrl = "{{ route('chatbot.respond') }}";
            var csrfToken = "{{ csrf_token() }}";

            // Generate or retrieve session ID
            var sessionId = localStorage.getItem('chatbot_session_id');
            if (!sessionId) {
                sessionId = 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
                localStorage.setItem('chatbot_session_id', sessionId);
            }

            // Toggle chatbot
            chatbotBtn.addEventListener('click', function() {
                chatbotModal.classList.toggle('active');
                if (chatbotModal.classList.contains('active')) {
                    chatbotInput.focus();
                }
            });

            // Close chatbot
            closeChatbot.addEventListener('click', function() {
                chatbotModal.classList.remove('active');
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!chatbotModal.contains(e.target) && !chatbotBtn.contains(e.target)) {
                    chatbotModal.classList.remove('active');
                }
            });

            function setStatus(text) {
                if (chatbotStatus) {
                    chatbotStatus.textContent = text || '';
                }
            }

            function showTypingIndicator() {
                if (typingIndicator) {
                    typingIndicator.style.display = 'block';
                    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
                }
            }

            function hideTypingIndicator() {
                if (typingIndicator) {
                    typingIndicator.style.display = 'none';
                }
            }

            function appendMessage(text, type, sentiment) {
                var messageWrapper = document.createElement('div');
                messageWrapper.className = 'message-wrapper ' + type + '-message';

                var currentTime = new Date().toLocaleTimeString('tr-TR', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                var sentimentEmoji = '';
                if (sentiment) {
                    switch(sentiment) {
                        case 'positive': sentimentEmoji = ' 😊'; break;
                        case 'negative': sentimentEmoji = ' 😔'; break;
                    }
                }

                if (type === 'bot') {
                    messageWrapper.innerHTML =
                        '<div class="avatar-container">' +
                            '<div class="bot-avatar">' +
                                '<i class="fas fa-robot"></i>' +
                            '</div>' +
                        '</div>' +
                        '<div class="message-content">' +
                            '<div class="message-bubble bot">' +
                                '<div class="message-text">' + text + sentimentEmoji + '</div>' +
                                '<div class="message-time">' + currentTime + '</div>' +
                            '</div>' +
                        '</div>';
                } else {
                    messageWrapper.innerHTML =
                        '<div class="message-content">' +
                            '<div class="message-bubble user">' +
                                '<div class="message-text">' + text + '</div>' +
                                '<div class="message-time">' + currentTime + '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="avatar-container">' +
                            '<div class="user-avatar">' +
                                '<i class="fas fa-user"></i>' +
                            '</div>' +
                        '</div>';
                }

                chatbotMessages.appendChild(messageWrapper);
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }

            function sendMessage() {
                var message = chatbotInput.value.trim();
                if (message.length === 0) {
                    setStatus('Lütfen bir mesaj yazın.');
                    return;
                }

                appendMessage(message, 'user');
                chatbotInput.value = '';
                chatbotInput.disabled = true;
                chatbotSendButton.disabled = true;

                showTypingIndicator();
                setStatus('AI düşünüyor...');

                fetch(chatbotUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        session_id: sessionId
                    })
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw response;
                    }
                    return response.json();
                })
                .then(function(data) {
                    hideTypingIndicator();

                    if (data.session_id) {
                        sessionId = data.session_id;
                        localStorage.setItem('chatbot_session_id', sessionId);
                    }

                    appendMessage(
                        data.message || 'Bilinmeyen bir yanıt alındı.',
                        'bot',
                        data.sentiment
                    );
                    setStatus('');
                })
                .catch(function(error) {
                    console.error('Chatbot error:', error);
                    hideTypingIndicator();

                    if (error.json) {
                        error.json().then(function(err) {
                            var validationMessage = (err && err.message) ? err.message : 'Mesaj gönderilirken bir sorun oluştu.';
                            appendMessage('Üzgünüm, bir hata oluştu: ' + validationMessage, 'bot');
                            setStatus(validationMessage);
                        }).catch(function() {
                            appendMessage('Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.', 'bot');
                            setStatus('Mesaj gönderilirken bir sorun oluştu.');
                        });
                    } else {
                        appendMessage('Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.', 'bot');
                        setStatus('Mesaj gönderilirken bir sorun oluştu.');
                    }
                })
                .finally(function() {
                    chatbotInput.disabled = false;
                    chatbotSendButton.disabled = false;
                    chatbotInput.focus();
                });
            }

            // Send message on button click
            chatbotSendButton.addEventListener('click', function(e) {
                e.preventDefault();
                sendMessage();
            });

            // Send message on Enter key
            chatbotInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Quick action buttons
            document.querySelectorAll('.quick-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    var message = this.getAttribute('data-message');
                    chatbotInput.value = message;
                    sendMessage();
                });
            });

            // Voice input
            var voiceButton = document.getElementById('voiceButton');
            var recognition = null;
            var isRecording = false;

            if (voiceButton && 'webkitSpeechRecognition' in window) {
                recognition = new webkitSpeechRecognition();
                recognition.continuous = false;
                recognition.interimResults = false;
                recognition.lang = 'tr-TR';

                voiceButton.addEventListener('click', function() {
                    if (!isRecording) {
                        try {
                            recognition.start();
                        } catch (error) {
                            setStatus('Ses tanıma başlatılamadı.');
                        }
                    } else {
                        recognition.stop();
                    }
                });

                recognition.onstart = function() {
                    isRecording = true;
                    voiceButton.classList.add('recording');
                    voiceButton.innerHTML = '<i class="fas fa-stop"></i>';
                    setStatus('Dinliyorum...');
                };

                recognition.onresult = function(event) {
                    var transcript = event.results[0][0].transcript;
                    chatbotInput.value = transcript;
                    setStatus('Ses kaydedildi: "' + transcript + '"');
                };

                recognition.onend = function() {
                    isRecording = false;
                    voiceButton.classList.remove('recording');
                    voiceButton.innerHTML = '<i class="fas fa-microphone"></i>';
                };

                recognition.onerror = function(event) {
                    isRecording = false;
                    voiceButton.classList.remove('recording');
                    voiceButton.innerHTML = '<i class="fas fa-microphone"></i>';
                    setStatus('Ses tanıma hatası. Tekrar deneyin.');
                };
            } else if (voiceButton) {
                voiceButton.style.display = 'none';
            }
        });
    </script>

</body>

</html>
