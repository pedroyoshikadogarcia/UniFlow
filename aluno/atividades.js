document.addEventListener('DOMContentLoaded', function() {
    // --- Variáveis para o Tema ---
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const themeIcon = themeToggle.querySelector('i');
    
    // --- Variáveis para o Modal de Detalhes ---
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModalButton = document.getElementById('closeModalButton');
    const modalTitle = document.getElementById('modalTitle');
    const modalBodyContent = document.getElementById('modalBodyContent');

    // --- Variáveis para o Modal de Upload ---
    const uploadDialog = document.getElementById('uploadDialog');
    const closeUploadBtn = document.getElementById('closeUploadBtn');
    const cancelUploadBtn = document.getElementById('cancelUploadBtn');
    const uploadForm = document.getElementById('uploadForm');
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    // --- Controle de Tema ---
    let isDarkMode = localStorage.getItem('darkMode') === 'true';
    body.classList.toggle('dark-theme', isDarkMode);
    updateThemeIcon();

    function updateThemeIcon() {
        if (themeIcon) {
            themeIcon.classList.toggle('fa-sun', !isDarkMode);
            themeIcon.classList.toggle('fa-moon', isDarkMode);
        }
    }

    themeToggle.addEventListener('click', function() {
        isDarkMode = !isDarkMode;
        body.classList.toggle('dark-theme', isDarkMode);
        updateThemeIcon();
        localStorage.setItem('darkMode', isDarkMode);
    });

    // --- Funções do Modal de Detalhes ---
    function openModal(title, contentHtml, buttonsConfig = []) {
        modalTitle.textContent = title;
        modalBodyContent.innerHTML = contentHtml;

        const oldButtons = document.querySelector('.modal-buttons');
        if (oldButtons) oldButtons.remove();

        if (buttonsConfig.length > 0) {
            const buttonsContainer = document.createElement('div');
            buttonsContainer.className = 'modal-buttons';

            buttonsConfig.forEach(config => {
                const button = document.createElement('button');
                button.className = 'modal-action-button';
                button.textContent = config.text;
                button.onclick = config.action;
                if (config.class) button.classList.add(config.class);
                buttonsContainer.appendChild(button);
            });

            document.querySelector('.modal-content').appendChild(buttonsContainer);
        }

        modalOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modalOverlay.style.display = 'none';
        document.body.style.overflow = '';
        modalBodyContent.innerHTML = '';
        const oldButtons = document.querySelector('.modal-buttons');
        if (oldButtons) oldButtons.remove();
    }

    closeModalButton.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', function(event) {
        if (event.target === modalOverlay) closeModal();
    });

    // --- Modal de Upload ---
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameDisplay.textContent = this.files[0].name;
            fileNameDisplay.style.display = 'block';
        } else {
            fileNameDisplay.textContent = '';
            fileNameDisplay.style.display = 'none';
        }
    });

    closeUploadBtn.addEventListener('click', () => { uploadDialog.close(); uploadForm.reset(); fileNameDisplay.textContent = ''; });
    cancelUploadBtn.addEventListener('click', () => { uploadDialog.close(); uploadForm.reset(); fileNameDisplay.textContent = ''; });

    uploadForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const file = fileInput.files[0];
        console.log('Arquivo selecionado para entrega:', file);
        uploadDialog.close();
        const notification = document.createElement('div');
        notification.classList.add('temporary-notification');
        notification.textContent = 'Atividade entregue com sucesso!';
        document.body.appendChild(notification);
        setTimeout(() => { notification.remove(); }, 5000);
    });

    // --- Página de Atividades ---
    const activityLinks = document.querySelectorAll('.activities-container .activity-link');

    activityLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const title = this.dataset.title;
            const discipline = this.dataset.discipline;
            const professor = this.dataset.professor;
            const dueDate = this.dataset.duedate;
            const status = this.dataset.status;
            const description = this.dataset.description;
            const tag = this.dataset.tag;
            const arquivo = this.dataset.file; // novo atributo

            let activityDetailsContent = `
                <p><strong>Disciplina:</strong> ${discipline}</p>
                <p><strong>Professor(a):</strong> ${professor}</p>
                <p><strong>Prazo:</strong> ${dueDate}</p>
                <p><strong>Tipo:</strong> ${tag}</p>
                <p style="margin-top: 15px;"><strong>Descrição:</strong> ${description}</p>
            `;

            // Se houver arquivo enviado, adiciona link para download
            if (arquivo) {
                activityDetailsContent += `<p style="margin-top:10px;"><strong>Arquivo enviado:</strong> <a href="${arquivo}" target="_blank">Download</a></p>`;
            }

            let buttonsConfig = [];
            if (status === 'aberta') {
                buttonsConfig = [
                    {
                        text: 'Entregar Atividade',
                        class: 'complete-button',
                        action: function() {
                            closeModal();
                            document.getElementById('atividadeIdInput').value = link.dataset.id;
                            uploadDialog.showModal();
                        }
                    },
                    {
                        text: 'Tirar Dúvida com Professor',
                        class: 'question-button',
                        action: function() { window.location.href = 'mensagens.html'; }
                    }
                ];
            }

            openModal(title, activityDetailsContent, buttonsConfig);
        });
    });
});
