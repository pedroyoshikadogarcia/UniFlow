document.addEventListener('DOMContentLoaded', () => {

    // --- Lógica de Inicialização (Removida do HTML) ---
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
            themeToggle.querySelector('i').classList.replace('fa-sun', 'fa-moon');
        } else {
            document.body.classList.remove('dark-theme');
            themeToggle.querySelector('i').classList.replace('fa-moon', 'fa-sun');
        }

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            if (document.body.classList.contains('dark-theme')) {
                localStorage.setItem('theme', 'dark');
                themeToggle.querySelector('i').classList.replace('fa-sun', 'fa-moon');
            } else {
                localStorage.setItem('theme', 'light');
                themeToggle.querySelector('i').classList.replace('fa-moon', 'fa-sun');
            }
        });
    }

    const today = new Date();
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    const dateEl = document.getElementById('current-date-display');
    if (dateEl) dateEl.textContent = today.toLocaleDateString('pt-BR', options);

    // --- Lógica do Chat ---
    const chatContainer = document.querySelector('.chat-container'); 
    const chatHeaderImg = document.getElementById('chat-student-img');
    const chatHeaderName = document.getElementById('chat-student-name');
    const chatMessagesContainer = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const fileButton = document.getElementById('file-button');
    const fileInput = document.getElementById('file-input');
    const backButton = document.getElementById('back-button');

    let activeStudentId = null; 

    // Dados de exemplo para alunos e mensagens
    const students = [
        { id: 1, name: 'João Silva', photo: 'img/aluno2.jpg', lastMessage: 'Olá, professor! Tenho uma dúvida sobre a atividade de hoje.' },
        { id: 2, name: 'Maria Santos', photo: 'img/aluno3.jpg', lastMessage: 'Gostaria de saber minha nota da última prova.' },
        { id: 3, name: 'Carlos Pereira', photo: 'img/aluno4.jpg', lastMessage: 'Prof, posso entregar o trabalho atrasado?' },
        { id: 4, name: 'Carolina Costa', photo: 'img/aluno5.jpg', lastMessage: 'Quando será a recuperação de matemática?' },
    ];

    // Histórico simulado
    const messagesHistory = {
        1: [
            { sender: 'student', text: 'Olá, professor! Tenho uma dúvida sobre a atividade de hoje.', time: '10:00' },
            { sender: 'professor', text: 'Olá João! Qual sua dúvida?', time: '10:05' },
            { sender: 'student', text: 'É sobre o exercício 5 da página 30, não entendi a segunda parte.', time: '10:10' }
        ],
        2: [
            { sender: 'student', text: 'Professora, gostaria de saber qual foi minha nota na última prova de história.', time: 'Ontem 14:30' },
            { sender: 'professor', text: 'Maria, sua nota foi 8.5. Quer conversar sobre?', time: 'Ontem 14:35' }
        ],
        3: [
            { sender: 'student', text: 'Professor, tive um problema e não consegui terminar o trabalho de ciências a tempo. Posso entregar atrasado?', time: '2 dias atrás' }
        ],
        4: [
            { sender: 'student', text: 'Olá professor, você sabe quando será a recuperação de matemática? Estou preocupada.', time: '3 dias atrás' }
        ]
    };

    function renderMessages(studentId) {
        chatMessagesContainer.innerHTML = '';
        const messages = messagesHistory[studentId] || [];

        if (messages.length === 0) {
            chatMessagesContainer.innerHTML = '<p class="chat-placeholder">Nenhuma mensagem nesta conversa ainda.</p>';
        } else {
            messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message');
                messageDiv.classList.add(msg.sender === 'professor' ? 'sent' : 'received');

                if (msg.attachmentName) {
                    messageDiv.classList.add('attachment');
                    messageDiv.innerHTML = `<i class="fas fa-paperclip" aria-hidden="true"></i> Arquivo: <strong>${msg.attachmentName}</strong>`;
                } else {
                    messageDiv.textContent = msg.text;
                }

                chatMessagesContainer.appendChild(messageDiv);
            });
            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
        }
    }

    function selectStudent(studentItem) {
        document.querySelectorAll('.student-item').forEach(item => item.classList.remove('active'));
        studentItem.classList.add('active');

        activeStudentId = parseInt(studentItem.dataset.studentId, 10);
        const selectedStudent = students.find(s => s.id === activeStudentId);

        if (selectedStudent) {
            chatHeaderImg.src = selectedStudent.photo;
            chatHeaderName.textContent = selectedStudent.name;

            messageInput.disabled = false;
            sendButton.disabled = false;
            if (fileButton) fileButton.disabled = false;

            renderMessages(activeStudentId);

            const lastMsgEl = studentItem.querySelector('.student-last-msg');
            if (lastMsgEl) lastMsgEl.textContent = 'Mensagem vista.';
            
            if (chatContainer) chatContainer.setAttribute('data-mode', 'chat');

            messageInput.focus();
        }
    }

    function sendMessage() {
        const messageText = messageInput.value.trim();
        if (messageText === '' || activeStudentId === null) return;

        if (!messagesHistory[activeStudentId]) messagesHistory[activeStudentId] = [];

        messagesHistory[activeStudentId].push({
            sender: 'professor',
            text: messageText,
            time: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
        });

        renderMessages(activeStudentId);
        messageInput.value = '';
    }

    function sendAttachment(file) {
        if (!file || activeStudentId === null) return;

        if (!messagesHistory[activeStudentId]) messagesHistory[activeStudentId] = [];

        messagesHistory[activeStudentId].push({
            sender: 'professor',
            attachmentName: file.name,
            time: new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
        });

        renderMessages(activeStudentId);
    }

    document.querySelectorAll('.student-item').forEach(item => {
        item.addEventListener('click', () => selectStudent(item));
    });

    sendButton.addEventListener('click', sendMessage);

    messageInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') sendMessage();
    });

    if (fileButton && fileInput) {
        fileButton.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (file) {
                sendAttachment(file);
                fileInput.value = '';
            }
        });
    }

    if (backButton) {
        backButton.addEventListener('click', () => {
            messageInput.disabled = true;
            sendButton.disabled = true;
            if (fileButton) fileButton.disabled = true;

            if (chatContainer) chatContainer.setAttribute('data-mode', 'list');

            chatHeaderImg.src = 'img/aluno-placeholder.jpg';
            chatHeaderName.textContent = 'Selecione um Aluno';
            chatMessagesContainer.innerHTML = '<p class="chat-placeholder">Selecione um aluno para iniciar a conversa.</p>';
            
            document.querySelectorAll('.student-item').forEach(item => item.classList.remove('active'));
            activeStudentId = null;
        });
    }
});