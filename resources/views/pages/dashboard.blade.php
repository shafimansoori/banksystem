@extends('layouts.master')

@section('title', 'Dashboard')

@section('custom-style')
<style>
    .chatbot-message {
        max-width: 80%;
        border-radius: 12px;
        padding: 10px 14px;
        margin-bottom: 8px;
        font-size: 14px;
        line-height: 1.4;
        background-color: #edf2f7;
        color: #2d3748;
        word-break: break-word;
    }

    .chatbot-message.user {
        margin-left: auto;
        background-color: #3182ce;
        color: #ffffff;
    }

    .chatbot-message.bot {
        margin-right: auto;
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
    }

    #chatbotMessages {
        height: 320px;
        overflow-y: auto;
        background-color: #f8fafc;
    }
</style>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12 h6">
        Banks
    </div>
    <div class="col-md-12">
        <div class="row">
        @foreach ($bankAccounts as $bankAccount)

            <div class="col-sm-6 col-md-6 col-lg-4">
                <div class="card" style="border-radius:10px !important">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-7">
                                <div>Bank Name.</div>
                                <div class="h6">{{ $bankAccount->bank->name }} {{ $bankAccount->bank_location->name }}</div>

                                <br/>

                                <div>Account Name.</div>
                                <div class="h6">{{ $bankAccount->name }}</div>

                                <div>Account No.</div>
                                <div class="h6">{{ $bankAccount->number }}</div>

                            </div>
                            <div class="col-sm-12 col-md-5 text-right">
                                @if(!empty($bankAccount->bank->picture))
                                    <img src="{{ $bankAccount->bank->picture }}" alt="{{ $bankAccount->bank->name }}" class="rounded-circle" width="50" height="50">
                                @endif

                                <br/>
                                <br/>

                                <div>Ledger Balance.</div>
                                <div class="h6 text-muted">{{ $bankAccount->bank_location->currency->symbol }} {{ $bankAccount->ledger_balance }}</div>

                                <div>Available Balance.</div>
                                <div class="h6">{{ $bankAccount->bank_location->currency->symbol }} {{ $bankAccount->available_balance }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
        </div>

    </div>
</div>

<!-- ============================================================== -->
<!-- Sales chart -->
<!-- ============================================================== -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title">Deposit</h4>
                        <h5 class="card-subtitle">Overview of Last 7days</h5>
                    </div>
                </div>
                <div class="row">
                    <!-- column -->
                    <div class="col-lg-12">
                        <canvas id="depositsChart" width="400" height="200"></canvas>
                    </div>
                    <!-- column -->
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Expenses</h4>
                <h5 class="card-subtitle">Overview of Last 7days</h5>
                <canvas id="expensesChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- Sales chart -->
<!-- ============================================================== -->


<div class="row mt-4">
    <div class="col-md-6 col-lg-5">
        <!-- Modern AI Assistant Card -->
        <div class="card ai-assistant-card border-0 shadow-lg">
            <div class="card-header bg-gradient-primary text-white border-0 rounded-top">
                <div class="d-flex align-items-center">
                    <div class="ai-avatar me-3">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div>
                        <h4 class="card-title mb-1 text-white">🤖 Akıllı Asistan</h4>
                        <small class="text-white-50">AI destekli bankacılık asistanı</small>
                    </div>
                    <div class="ms-auto">
                        <span class="status-indicator online"></span>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
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
                <div id="chatbotMessages" class="chat-messages">
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
                <div id="typingIndicator" class="typing-indicator" style="display: none;">
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
                <div class="chat-input-area">
                    <form id="chatbotForm" class="chat-form" onsubmit="return false;">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary voice-btn" type="button" id="voiceButton" title="Sesli komut">
                                <i class="fas fa-microphone"></i>
                            </button>
                            <input type="text" class="form-control chat-input" id="chatbotInput" 
                                   placeholder="Mesajınızı yazın..." autocomplete="off">
                            <button class="btn btn-primary send-btn" type="button" id="chatbotSendButton">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <div class="status-area">
                        <small class="text-muted" id="chatbotStatus"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('custom-script')

<script>

function loadDepositChart(){

    var ctx = document.getElementById('depositsChart').getContext('2d');
    var myLineChart = new Chart(ctx,{
        "type":"line",
        "data":{
            "labels":[
                @foreach($bankDepositDates as $bankDepositDate)
                    '{{$bankDepositDate->format('D d M, Y')}}',
                @endforeach
            ],
            "datasets":[
                {
                    "label":"Amount","data":[
                        @foreach($bankDepositAmounts as $bankDepositAmount)
                            '{{$bankDepositAmount}}',
                        @endforeach
                    ],
                    "fill":true,
                    "borderColor":"rgb(254, 121, 79)",
                    "lineTension":0.1
                }
            ]
        },
        "options":{

        }
    });


}


function loadExpensesChart(){

    var ctx = document.getElementById('expensesChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($bankExpensesDates as $bankExpensesDate)
                    '{{$bankExpensesDate->format('D d M, Y')}}',
                @endforeach
            ],
            datasets: [{
                label: 'Amount',
                data: [
                    @foreach($bankExpensesAmounts as $bankExpensesAmount)
                        '{{$bankExpensesAmount}}',
                    @endforeach
                ],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 0.5
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

}


function setupChatbot() {
    var chatbotForm = document.getElementById('chatbotForm');
    if (!chatbotForm) {
        console.log('Chatbot form bulunamadı');
        return;
    }

    var chatbotInput = document.getElementById('chatbotInput');
    var chatbotMessages = document.getElementById('chatbotMessages');
    var chatbotStatus = document.getElementById('chatbotStatus');
    var chatbotSendButton = document.getElementById('chatbotSendButton');
    var chatbotUrl = "{{ route('chatbot.respond') }}";
    var csrfToken = "{{ csrf_token() }}";
    
    // Generate or retrieve session ID
    var sessionId = localStorage.getItem('chatbot_session_id');
    if (!sessionId) {
        sessionId = 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
        localStorage.setItem('chatbot_session_id', sessionId);
    }

    console.log('Chatbot initialized with session:', sessionId);
    
    // Hide typing indicator on page load
    hideTypingIndicator();

    function appendMessage(text, type, sentiment, intent) {
        var messagesContainer = document.getElementById('chatbotMessages');
        if (!messagesContainer) {
            console.error('Chat messages container not found');
            return;
        }

        var messageWrapper = document.createElement('div');
        messageWrapper.className = `message-wrapper ${type}-message`;

        var currentTime = new Date().toLocaleTimeString('tr-TR', {
            hour: '2-digit',
            minute: '2-digit'
        });

        // Add emoji based on sentiment
        var sentimentEmoji = '';
        if (sentiment) {
            switch(sentiment) {
                case 'positive': sentimentEmoji = ' 😊'; break;
                case 'negative': sentimentEmoji = ' 😔'; break;
                case 'neutral': sentimentEmoji = ''; break;
            }
        }

        if (type === 'bot') {
            messageWrapper.innerHTML = `
                <div class="avatar-container">
                    <div class="bot-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
                <div class="message-content">
                    <div class="message-bubble bot">
                        <div class="message-text">${text}${sentimentEmoji}</div>
                        <div class="message-time">${currentTime}</div>
                    </div>
                </div>
            `;
        } else {
            messageWrapper.innerHTML = `
                <div class="message-content">
                    <div class="message-bubble user">
                        <div class="message-text">${text}</div>
                        <div class="message-time">${currentTime}</div>
                    </div>
                </div>
                <div class="avatar-container">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            `;
        }

        messagesContainer.appendChild(messageWrapper);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function showTypingIndicator() {
        var typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.style.display = 'block';
            var messagesContainer = document.getElementById('chatbotMessages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    function hideTypingIndicator() {
        var typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.style.display = 'none';
        }
    }

    function setStatus(text) {
        if (chatbotStatus) {
            chatbotStatus.textContent = text || '';
        }
    }

    function sendMessage() {
        var message = chatbotInput.value.trim();

        if (message.length === 0) {
            setStatus('Lütfen bir mesaj yazın.');
            return;
        }

        console.log('Mesaj gönderiliyor:', message);
        appendMessage(message, 'user');
        chatbotInput.value = '';
        chatbotInput.disabled = true;
        chatbotSendButton.disabled = true;
        
        // Show typing indicator
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
        .then(function (response) {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw response;
            }
            return response.json();
        })
        .then(function (data) {
            console.log('Bot response:', data);
            hideTypingIndicator();
            
            // Update session ID if provided
            if (data.session_id) {
                sessionId = data.session_id;
                localStorage.setItem('chatbot_session_id', sessionId);
            }
            
            appendMessage(
                data.message || 'Bilinmeyen bir yanıt alındı.', 
                'bot',
                data.sentiment,
                data.intent
            );
            setStatus('');
            
            // Show additional info if available
            if (data.entities && Object.keys(data.entities).length > 0) {
                console.log('Detected entities:', data.entities);
            }
            if (data.score) {
                console.log('Confidence score:', data.score);
            }
        })
        .catch(function (error) {
            console.error('Chatbot error:', error);
            hideTypingIndicator();
            
            if (error.json) {
                error.json().then(function (err) {
                    var validationMessage = (err && err.message) ? err.message : 'Mesaj gönderilirken bir sorun oluştu.';
                    if (err && err.errors && err.errors.message) {
                        validationMessage = err.errors.message[0];
                    }
                    appendMessage('Üzgünüm, bir hata oluştu: ' + validationMessage, 'bot');
                    setStatus(validationMessage);
                }).catch(function () {
                    appendMessage('Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.', 'bot');
                    setStatus('Mesaj gönderilirken bir sorun oluştu.');
                });
            } else {
                appendMessage('Üzgünüm, bir hata oluştu. Lütfen tekrar deneyin.', 'bot');
                setStatus('Mesaj gönderilirken bir sorun oluştu.');
            }
        })
        .finally(function () {
            chatbotInput.disabled = false;
            chatbotSendButton.disabled = false;
            chatbotInput.focus();
        });
    }

    // Form submit event (Enter tuşu)
    if (chatbotForm) {
        chatbotForm.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();
            sendMessage();
            return false;
        });
    }

    // Button click event
    if (chatbotSendButton) {
        chatbotSendButton.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            console.log('Send button clicked');
            sendMessage();
            return false;
        });
    }

    // Enter key support
    if (chatbotInput) {
        chatbotInput.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                sendMessage();
                return false;
            }
        });
    }

    // Quick action buttons
    document.querySelectorAll('.quick-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var message = this.getAttribute('data-message');
            chatbotInput.value = message;
            sendMessage();
        });
    });

    // Voice input button
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
                startRecording();
            } else {
                stopRecording();
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
            stopRecording();
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
            stopRecording();
            setStatus('Ses tanıma hatası. Tekrar deneyin.');
        };

        function startRecording() {
            try {
                recognition.start();
            } catch (error) {
                console.error('Could not start speech recognition:', error);
                setStatus('Ses tanıma başlatılamadı.');
            }
        }

        function stopRecording() {
            isRecording = false;
            voiceButton.classList.remove('recording');
            voiceButton.innerHTML = '<i class="fas fa-microphone"></i>';
            if (recognition) {
                recognition.stop();
            }
        }
    } else if (voiceButton) {
        // Tarayıcı ses tanımayı desteklemiyorsa
        voiceButton.style.display = 'none';
    }

    console.log('Chatbot setup completed');
}




loadDepositChart();
loadExpensesChart();
setupChatbot();

</script>

<style>
/* Modern AI Assistant Styles */
.ai-assistant-card {
    border-radius: 20px !important;
    overflow: hidden;
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9ff 100%);
}

.ai-assistant-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.ai-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    backdrop-filter: blur(10px);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    position: relative;
}

.status-indicator.online {
    background: #4CAF50;
    box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
}

.status-indicator.online::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #4CAF50;
    animation: ping 2s infinite;
}

@keyframes ping {
    0% { transform: scale(1); opacity: 1; }
    75%, 100% { transform: scale(2); opacity: 0; }
}

/* Quick Actions */
.quick-actions {
    border-bottom: 1px solid #e9ecef;
}

.quick-btn {
    border-radius: 12px !important;
    font-size: 11px;
    font-weight: 600;
    transition: all 0.2s ease;
    border: 1px solid #e9ecef !important;
}

.quick-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.quick-btn i {
    margin-bottom: 2px;
    display: block;
}

/* Chat Messages */
.chat-messages {
    height: 350px;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.message-wrapper {
    display: flex;
    margin-bottom: 15px;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bot-message {
    justify-content: flex-start;
}

.user-message {
    justify-content: flex-end;
}

.avatar-container {
    margin-right: 10px;
}

.bot-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #d63384;
    font-size: 14px;
    margin-left: 10px;
}

.message-content {
    max-width: 75%;
}

.user-message .avatar-container {
    margin-right: 0;
    margin-left: 10px;
    order: 2;
}

.user-message .message-content {
    order: 1;
}

.message-bubble {
    padding: 12px 16px;
    border-radius: 18px;
    position: relative;
    word-wrap: break-word;
}

.message-bubble.bot {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-left-radius: 6px;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

.message-bubble.user {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    color: #333;
    border-bottom-right-radius: 6px;
    box-shadow: 0 2px 10px rgba(252, 182, 159, 0.3);
}

.message-time {
    font-size: 10px;
    opacity: 0.7;
    margin-top: 5px;
    text-align: right;
}

/* Typing Indicator */
#typingIndicator {
    display: none !important;
}

.typing {
    padding: 15px 20px !important;
}

.typing-dots {
    display: flex;
    align-items: center;
    justify-content: center;
}

.typing-dots span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.8);
    margin: 0 2px;
    animation: typing 1.4s infinite;
}

.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 80%, 100% { opacity: 0.3; transform: scale(0.8); }
    40% { opacity: 1; transform: scale(1); }
}

/* Input Area */
.chat-input-area {
    padding: 20px;
    background: white;
    border-top: 1px solid #e9ecef;
}

.chat-input {
    border-radius: 25px !important;
    border: 1px solid #e9ecef !important;
    padding: 12px 20px !important;
    font-size: 14px;
    transition: all 0.2s ease;
}

.chat-input:focus {
    border-color: #667eea !important;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
}

.voice-btn {
    border-radius: 25px !important;
    border: 1px solid #e9ecef !important;
    color: #6c757d;
    transition: all 0.2s ease;
}

.voice-btn:hover {
    background: #f8f9fa !important;
    color: #667eea !important;
    border-color: #667eea !important;
}

.voice-btn.recording {
    background: #dc3545 !important;
    color: white !important;
    border-color: #dc3545 !important;
    animation: pulse 1.5s infinite;
}

.send-btn {
    border-radius: 25px !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    color: white;
    transition: all 0.2s ease;
}

.send-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.send-btn:disabled {
    opacity: 0.6;
    transform: none !important;
}

.status-area {
    margin-top: 10px;
    text-align: center;
}

/* Scrollbar Styling */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #5a6fd8;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .ai-assistant-card {
        margin: 0 10px;
    }
    
    .chat-messages {
        height: 280px;
    }
    
    .message-content {
        max-width: 85%;
    }
    
    .quick-btn {
        font-size: 10px;
        padding: 6px 8px;
    }
}
</style>

@endsection
