document.addEventListener('DOMContentLoaded', () => {
    // Busca o elemento do calendário e o ID do aluno.
    const calendarPlaceholder = document.getElementById('calendar-placeholder');
    const alunoIdInput = document.getElementById('professor_id');
    
    if (!calendarPlaceholder || !alunoIdInput) {
        console.error('Um dos elementos necessários (calendar-placeholder ou professor_id) não foi encontrado.');
        return;
    }

    const alunoId = profeessorIdInput.value;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let eventsData = {};

    let eventModalOverlay, eventModalContent, eventModalTitle, eventModalDateDisplay;
    let eventTitleInput, eventTypeSelect, eventDescriptionTextarea, eventSaveButton;
    let selectedDateForModal = null;

    // --- 1. Função para carregar eventos do servidor ---
    function loadEvents() {
        fetch(`get_eventos.php?professor_id=${alunoId}&month=${currentMonth + 1}&year=${currentYear}`)
            .then(res => res.json())
            .then(data => {
                // Limpa os dados de eventos existentes
                eventsData = {};
                // Preenche o objeto eventsData com os dados do servidor
                data.forEach(ev => {
                    const dateKey = ev.data_evento;
                    if (!eventsData[dateKey]) eventsData[dateKey] = [];
                    eventsData[dateKey].push({
                        id: ev.id,
                        title: ev.titulo,
                        type: ev.tipo,
                        description: ev.descricao
                    });
                });
                renderCalendar();
            })
            .catch(err => console.error('Erro ao carregar eventos:', err));
    }

    // --- 2. Funções do modal (criação, abertura e fechamento) ---
    function createModalElements() {
        eventModalOverlay = document.createElement('div');
        eventModalOverlay.classList.add('calendar-modal-overlay');
        document.body.appendChild(eventModalOverlay);

        eventModalContent = document.createElement('div');
        eventModalContent.classList.add('calendar-modal-content');
        eventModalOverlay.appendChild(eventModalContent);

        const modalHeader = document.createElement('div');
        modalHeader.classList.add('calendar-modal-header');
        eventModalContent.appendChild(modalHeader);

        eventModalTitle = document.createElement('h3');
        modalHeader.appendChild(eventModalTitle);

        const closeButton = document.createElement('button');
        closeButton.classList.add('calendar-modal-close-button');
        closeButton.innerHTML = '<i class="fas fa-times"></i>';
        closeButton.addEventListener('click', closeModal);
        modalHeader.appendChild(closeButton);

        const form = document.createElement('div');
        eventModalContent.appendChild(form);

        const dateGroup = document.createElement('div');
        dateGroup.classList.add('calendar-modal-form-group');
        const dateLabel = document.createElement('label');
        dateLabel.textContent = 'Data do Evento:';
        eventModalDateDisplay = document.createElement('span');
        dateGroup.appendChild(dateLabel);
        dateGroup.appendChild(eventModalDateDisplay);
        form.appendChild(dateGroup);

        const titleGroup = document.createElement('div');
        titleGroup.classList.add('calendar-modal-form-group');
        const titleLabel = document.createElement('label');
        titleLabel.textContent = 'Título do Evento:';
        eventTitleInput = document.createElement('input');
        eventTitleInput.type = 'text';
        titleGroup.appendChild(titleLabel);
        titleGroup.appendChild(eventTitleInput);
        form.appendChild(titleGroup);

        const typeGroup = document.createElement('div');
        typeGroup.classList.add('calendar-modal-form-group');
        const typeLabel = document.createElement('label');
        typeLabel.textContent = 'Tipo:';
        eventTypeSelect = document.createElement('select');
        eventTypeSelect.innerHTML = `
            <option value="type-prova">Prova</option>
            <option value="type-trabalho">Trabalho</option>
            <option value="type-apresentacao">Apresentação</option>
            <option value="type-exercicio">Exercício</option>
        `;
        typeGroup.appendChild(typeLabel);
        typeGroup.appendChild(eventTypeSelect);
        form.appendChild(typeGroup);

        const descGroup = document.createElement('div');
        descGroup.classList.add('calendar-modal-form-group');
        const descLabel = document.createElement('label');
        descLabel.textContent = 'Descrição:';
        eventDescriptionTextarea = document.createElement('textarea');
        descGroup.appendChild(descLabel);
        descGroup.appendChild(eventDescriptionTextarea);
        form.appendChild(descGroup);

        const buttonsDiv = document.createElement('div');
        buttonsDiv.classList.add('calendar-modal-buttons');
        eventModalContent.appendChild(buttonsDiv);

        const cancelButton = document.createElement('button');
        cancelButton.classList.add('calendar-modal-button', 'secondary');
        cancelButton.textContent = 'Cancelar';
        cancelButton.addEventListener('click', closeModal);
        buttonsDiv.appendChild(cancelButton);

        eventSaveButton = document.createElement('button');
        eventSaveButton.classList.add('calendar-modal-button', 'primary');
        eventSaveButton.textContent = 'Salvar Evento';
        eventSaveButton.addEventListener('click', saveEvent);
        buttonsDiv.appendChild(eventSaveButton);
    }

    function openModal(dateKey, displayDate) {
        selectedDateForModal = dateKey;
        eventModalTitle.textContent = `Adicionar Evento em ${displayDate}`;
        eventModalDateDisplay.textContent = displayDate;
        eventTitleInput.value = '';
        eventTypeSelect.value = 'type-prova';
        eventDescriptionTextarea.value = '';
        eventModalOverlay.classList.add('show-modal');
    }

    function closeModal() {
        eventModalOverlay.classList.remove('show-modal');
        selectedDateForModal = null;
    }

    // --- 3. Função para salvar evento no banco ---
    function saveEvent() {
        const title = eventTitleInput.value.trim();
        const type = eventTypeSelect.value;
        const description = eventDescriptionTextarea.value.trim();

        if (!title) {
            alert('Insira um título para o evento.');
            return;
        }
        
        // Envia os dados para o servidor, incluindo o ID do aluno
        fetch('salvar_eventos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                aluno_id: alunoId,
                data: selectedDateForModal,
                titulo: title,
                tipo: type,
                descricao: description
            })
        })
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success') {
                alert('Evento salvo com sucesso!');
                // Recarrega os eventos para atualizar o calendário
                loadEvents();
                closeModal();
            } else {
                alert('Erro ao salvar: ' + response.message);
            }
        })
        .catch(() => alert('Erro ao comunicar com o servidor.'));
    }

    // --- 4. Renderização do calendário na página ---
    function renderCalendar() {
        calendarPlaceholder.innerHTML = '';

        const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
        const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);
        const daysInMonth = lastDayOfMonth.getDate();
        const firstDayOfWeek = firstDayOfMonth.getDay();

        const monthNames = [
            "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
        ];
        const monthYearString = `${monthNames[currentMonth]} ${currentYear}`;

        const calendarHTML = `
            <div class="calendar-wrapper">
                <div class="calendar-header-nav">
                    <button id="prevMonth" class="calendar-nav-button"><i class="fas fa-chevron-left"></i></button>
                    <h2 id="currentMonthYear">${monthYearString}</h2>
                    <button id="nextMonth" class="calendar-nav-button"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="calendar-grid-weekdays">
                    <div>Dom</div>
                    <div>Seg</div>
                    <div>Ter</div>
                    <div>Qua</div>
                    <div>Qui</div>
                    <div>Sex</div>
                    <div>Sáb</div>
                </div>
                <div id="calendarGrid" class="calendar-grid-dates"></div>
            </div>
        `;
        calendarPlaceholder.innerHTML = calendarHTML;

        const calendarGrid = document.getElementById('calendarGrid');
        const today = new Date();
        const todayKey = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

        const lastDayPrevMonth = new Date(currentYear, currentMonth, 0).getDate();
        for (let i = firstDayOfWeek; i > 0; i--) {
            const dayDiv = document.createElement('div');
            dayDiv.classList.add('calendar-day', 'inactive');
            dayDiv.innerHTML = `<span>${lastDayPrevMonth - i + 1}</span>`;
            calendarGrid.appendChild(dayDiv);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateKey = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayDiv = document.createElement('div');
            dayDiv.classList.add('calendar-day');
            dayDiv.innerHTML = `<span>${day}</span>`;

            if (dateKey === todayKey) dayDiv.classList.add('current-day');

            const events = eventsData[dateKey] || [];
            events.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.classList.add('calendar-event', event.type);
                eventDiv.textContent = event.title;
                eventDiv.title = event.description || '';
                dayDiv.appendChild(eventDiv);
            });

            dayDiv.addEventListener('click', () => {
                const displayDate = `${String(day).padStart(2, '0')}/${String(currentMonth + 1).padStart(2, '0')}/${currentYear}`;
                openModal(dateKey, displayDate);
            });

            calendarGrid.appendChild(dayDiv);
        }

        const totalCells = firstDayOfWeek + daysInMonth;
        const remaining = (7 - (totalCells % 7)) % 7;
        for (let i = 1; i <= remaining; i++) {
            const dayDiv = document.createElement('div');
            dayDiv.classList.add('calendar-day', 'inactive');
            dayDiv.innerHTML = `<span>${i}</span>`;
            calendarGrid.appendChild(dayDiv);
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            loadEvents();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            loadEvents();
        });
    }

    createModalElements();
    loadEvents();
});