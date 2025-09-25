document.addEventListener('DOMContentLoaded', function() {
    const historyButton = document.getElementById('history-button');
    const historyModal = document.getElementById('history-modal');
    const closeModalButton = historyModal.querySelector('.close-button');
    const currentMonthYearSpan = document.getElementById('current-month-year');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');
    const historyListDiv = document.getElementById('history-list');
    const themeToggleButton = document.getElementById('theme-toggle');

    const mainTitle = document.getElementById('main-title');
    const currentDateDisplay = document.getElementById('current-date-display');
    const attendanceTableBody = document.getElementById('attendance-table-body');
    const submitButtonContainer = document.querySelector('.submit-button-container');
    const backToHistoryContainer = document.querySelector('.back-to-history-container');
    const backToHistoryButton = document.getElementById('back-to-history-button');
    const attendanceCardDate = document.getElementById('attendance-card-date');

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    let originalAttendanceContent = attendanceTableBody.innerHTML;
    let originalTitle = mainTitle.textContent;
    let originalDateDisplay = currentDateDisplay.textContent;

    // --------------------- Função para gerar histórico ---------------------
    function generateHistory(month, year) {
        historyListDiv.innerHTML = '<p>Carregando histórico...</p>';
        const disciplinaId = document.getElementById('disciplina_id').value;

        fetch(`get_historico.php?disciplina_id=${disciplinaId}&mes=${month+1}&ano=${year}`)
            .then(response => response.json())
            .then(data => {
                historyListDiv.innerHTML = '';
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                for(let i = daysInMonth; i >= 1; i--) {
                    const dayDate = new Date(year, month, i);
                    const dateStringFormatted = dayDate.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    const dateStringISO = `${year}-${String(month + 1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;

                    let statusClass = 'future';
                    let statusText = 'Ainda não ocorrido';

                    const diaData = data.find(d => d.data === dateStringISO);
                    if(diaData) {
                        if(dayDate.getDay() === 0 || dayDate.getDay() === 6){
                            statusClass = 'weekend';
                            statusText = 'Fim de Semana';
                        } else {
                            const presencas = Object.values(diaData.presencas);
                            const faltas = presencas.filter(p => p == 0).length;
                            statusClass = faltas > 0 ? 'absent' : 'present';
                            statusText = faltas > 0 ? 'Falta' : 'Presente';
                        }
                    }

                    const historyDay = document.createElement('div');
                    historyDay.classList.add('history-day');
                    historyDay.innerHTML = `
                        <span class="day-date">${dateStringFormatted}</span>
                        <span class="day-status ${statusClass}">${statusText}</span>
                        <span class="details-link">Ver detalhes <i class="fas fa-chevron-right"></i></span>
                    `;

                    const detailsLink = historyDay.querySelector('.details-link');
                    if(statusClass !== 'future' && statusClass !== 'weekend'){
                        detailsLink.addEventListener('click', () => {
                            showAttendanceForDate(dateStringISO);
                        });
                    } else {
                        detailsLink.style.cursor = 'default';
                        detailsLink.style.opacity = '0.6';
                    }

                    historyListDiv.appendChild(historyDay);
                }

                updateMonthYearDisplay(month, year);
            })
            .catch(err => {
                console.error('Erro ao carregar histórico:', err);
                historyListDiv.innerHTML = '<p>Erro ao carregar histórico.</p>';
            });
    }

    function updateMonthYearDisplay(month, year) {
        const date = new Date(year, month);
        currentMonthYearSpan.textContent = date.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
    }

    // --------------------- Função para mostrar chamada do dia ---------------------
    function showAttendanceForDate(dateISOString) {
        const disciplinaId = document.getElementById('disciplina_id').value;
        
        console.log('Disciplina:', disciplinaId, 'Data:', dateISOString);

        fetch(`get_chamada_dia.php?disciplina_id=${disciplinaId}&data=${dateISOString}`)

            .then(response => response.json())
            .then(alunos => {
                historyModal.style.display = 'none';

                const displayDate = new Date(dateISOString);
                const formattedDate = displayDate.toLocaleDateString('pt-BR', { day:'2-digit', month:'2-digit', year:'numeric' });

                mainTitle.textContent = 'Chamada do Dia';
                currentDateDisplay.textContent = `Data: ${formattedDate}`;
                attendanceCardDate.textContent = `Data: ${formattedDate}`;
                attendanceCardDate.style.display = 'block';

                let tableHtml = '';
                alunos.forEach(aluno => {
                    const presentChecked = aluno.presente == 1 ? 'checked' : '';
                    const absentChecked = aluno.presente == 0 ? 'checked' : '';
                    const matricula = aluno.matricula || '';

                    tableHtml += `
                        <tr>
                            <td>${matricula}</td>
                            <td>${aluno.nome}</td>
                            <td><input type="radio" name="presence-${aluno.aluno_id}" value="1" ${presentChecked} disabled></td>
                            <td><input type="radio" name="presence-${aluno.aluno_id}" value="0" ${absentChecked} disabled></td>
                        </tr>
                    `;
                });

                attendanceTableBody.innerHTML = tableHtml;
                submitButtonContainer.style.display = 'none';
                backToHistoryContainer.style.display = 'block';
            })
            .catch(err => {
                console.error('Erro ao carregar chamada do dia:', err);
                alert('Não foi possível carregar a chamada do dia selecionado.');
            });
    }

    // --------------------- Função para resetar a visualização ---------------------
    function resetAttendanceView() {
        mainTitle.textContent = originalTitle;
        currentDateDisplay.textContent = originalDateDisplay;
        attendanceTableBody.innerHTML = originalAttendanceContent;
        attendanceCardDate.style.display = 'none';
        attendanceCardDate.textContent = '';
        document.querySelectorAll('.presence-radio').forEach(radio => radio.removeAttribute('disabled'));
        submitButtonContainer.style.display = 'flex';
        backToHistoryContainer.style.display = 'none';
    }

    // --------------------- Navegação de meses ---------------------
    prevMonthButton.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        generateHistory(currentMonth, currentYear);
    });

    nextMonthButton.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        generateHistory(currentMonth, currentYear);
    });

    // --------------------- Modal histórico ---------------------
    historyButton.addEventListener('click', () => {
        historyModal.style.display = 'flex';
        generateHistory(currentMonth, currentYear);
        resetAttendanceView();
    });

    closeModalButton.addEventListener('click', () => { historyModal.style.display = 'none'; });
    window.addEventListener('click', (event) => { if(event.target == historyModal) historyModal.style.display = 'none'; });
    backToHistoryButton.addEventListener('click', resetAttendanceView);

    // --------------------- Tema escuro ---------------------
    if (themeToggleButton) {
        themeToggleButton.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            const isDark = document.body.classList.contains('dark-theme');
            themeToggleButton.innerHTML = isDark ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
            themeToggleButton.innerHTML = '<i class="fas fa-moon"></i>';
        } else {
            themeToggleButton.innerHTML = '<i class="fas fa-sun"></i>';
        }
    }

    // --------------------- Inicialização da tela ---------------------
    function initializeAttendanceDisplay() {
        const todayAttendance = new Date();
        const optionsAttendance = { year: 'numeric', month: '2-digit', day: '2-digit' };
        currentDateDisplay.textContent = `Data: ${todayAttendance.toLocaleDateString('pt-BR', optionsAttendance)}`;
        mainTitle.textContent = "Lista de Presença";
        attendanceCardDate.style.display = 'none';
    }

    initializeAttendanceDisplay();
});
