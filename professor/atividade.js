document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const activitiesListContainer = document.getElementById('activitiesListContainer');
    const activitiesCount = document.getElementById('activitiesCount');
    const openAddActivity = document.getElementById('openAddActivity');
    const addActivityModal = document.getElementById('addActivityModal');
    const closeAddActivityModal = document.getElementById('closeAddActivityModal');
    const newActivityForm = document.getElementById('newActivityForm');
    const uploadFilesButton = document.getElementById('uploadFilesButton');
    const activitiesSection = document.getElementById('activitiesSection');
    const submissionsSection = document.getElementById('submissionsSection');
    const studentDetailsSection = document.getElementById('studentDetailsSection');
    const studentsSubmissionsList = document.getElementById('studentsSubmissionsList');
    const activityTitleSubmissions = document.getElementById('activityTitleSubmissions');
    const totalStudentsEl = document.getElementById('totalStudents');
    const submittedCountEl = document.getElementById('submittedCount');
    const pendingCountEl = document.getElementById('pendingCount');
    const backToActivitiesButton = document.getElementById('backToActivitiesButton');
    const backToSubmissionsButton = document.getElementById('backToSubmissionsButton');
    const saveAllNotesBtn = document.getElementById('saveAllNotesBtn');

    let currentActivities = [];
    let currentSubmissions = [];
    let currentActivityId = null;

    function showSection(idToShow) {
        [activitiesSection, submissionsSection, studentDetailsSection].forEach(s => s.classList.remove('show'));
        const el = document.getElementById(idToShow);
        if (el) el.classList.add('show');
    }

    async function fetchAtividades() {
        try {
            const res = await fetch('fetch_atividades.php', { credentials: 'same-origin' });
            const data = await res.json();
            if (!data.success) {
                console.error('Erro fetch_atividades:', data.message || data);
                return;
            }
            currentActivities = data.atividades || [];
            renderActivitiesList();
        } catch (err) {
            console.error('Erro fetching atividades:', err);
        }
    }

    function renderActivitiesList() {
        activitiesListContainer.innerHTML = '';
        activitiesCount.textContent = `${currentActivities.length} ATIVIDADE(S)`;
        if (currentActivities.length === 0) {
            activitiesListContainer.innerHTML = '<p>Nenhuma atividade cadastrada.</p>';
            return;
        }

        currentActivities.forEach(activity => {
            const card = document.createElement('div');
            card.className = 'activity-card';
            card.dataset.activityId = activity.id;
            card.innerHTML = `
                <div class="activity-header">
                    <span class="activity-type activity-type-${activity.tipo || 'atividade'}">${(activity.tipo || '').toUpperCase()}</span>
                    <span class="activity-status activity-status-${(activity.status||'aberto').toLowerCase()}">${(activity.status||'').toUpperCase()}</span>
                </div>
                <h4>${escapeHtml(activity.titulo)}</h4>
                <p class="activity-description">${escapeHtml(activity.descricao || '')}</p>
                <div class="activity-details">
                    <p><i class="fas fa-calendar-alt"></i> Prazo: ${activity.prazo_formatted || '-'}</p>
                    <p><i class="fas fa-star"></i> Valor: ${activity.valor ?? '-' } pontos</p>
                    <p><i class="fas fa-book"></i> Disciplina: ${escapeHtml(activity.disciplina_nome || '')}</p>
                </div>
                <div class="activity-footer">
                    <span class="activity-notes-status">Notas lançadas: ${activity.entregues || 0}/${activity.total_alunos || 0}</span>
                    <div class="activity-actions">
                        <button class="action-button launch-notes-button" data-activity-id="${activity.id}"><i class="fas fa-file-invoice"></i> Lançar Notas</button>
                        <button class="action-button delete-button" data-activity-id="${activity.id}"><i class="fas fa-trash-alt"></i> Excluir</button>
                    </div>
                </div>
            `;
            activitiesListContainer.appendChild(card);
        });

        // attach events
        document.querySelectorAll('.launch-notes-button').forEach(btn => {
            btn.onclick = (e) => {
                e.stopPropagation();
                const id = parseInt(btn.dataset.activityId);
                loadSubmissionsForActivity(id);
            };
        });

        document.querySelectorAll('.delete-button').forEach(btn => {
            btn.onclick = async (e) => {
                e.stopPropagation();
                const id = parseInt(btn.dataset.activityId);
                const title = btn.closest('.activity-card').querySelector('h4').innerText;
                if (!confirm(`Excluir atividade "${title}"?`)) return;
                try {
                    const res = await fetch(`delete_atividade.php?id=${id}`, { method: 'POST', credentials: 'same-origin' });
                    const data = await res.json();
                    if (data.success) {
                        alert('Atividade excluída.');
                        await fetchAtividades();
                    } else {
                        alert('Erro ao excluir: ' + (data.message || ''));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Erro ao excluir atividade.');
                }
            };
        });
    }

    async function loadSubmissionsForActivity(activityId) {
        try {
            const res = await fetch(`fetch_entregas.php?atividade_id=${activityId}`, { credentials: 'same-origin' });
            const data = await res.json();
            if (!data.success) {
                alert('Erro ao carregar entregas: ' + (data.message || ''));
                return;
            }
            currentActivityId = activityId;
            currentSubmissions = data.entregas || [];
            renderSubmissions();
        } catch (err) {
            console.error(err);
            alert('Erro ao carregar entregas (veja console).');
        }
    }

    function renderSubmissions() {
        const activity = currentActivities.find(a => parseInt(a.id) === parseInt(currentActivityId));
        if (!activity) return;

        activityTitleSubmissions.textContent = `Entregas - ${activity.titulo}`;
        totalStudentsEl.textContent = activity.total_alunos || 0;
        submittedCountEl.textContent = currentSubmissions.filter(s => s.entregue).length;
        pendingCountEl.textContent = (activity.total_alunos || 0) - submittedCountEl.textContent;

        studentsSubmissionsList.innerHTML = '';

        if (currentSubmissions.length === 0) {
            studentsSubmissionsList.innerHTML = '<p>Nenhuma entrega encontrada.</p>';
            return;
        }

        currentSubmissions.forEach(sub => {
            const div = document.createElement('div');
            div.className = 'student-submission-card';
            div.innerHTML = `
                <p><strong>${escapeHtml(sub.aluno_nome)}</strong> ${sub.entregue ? '<span class="status entregue">Entregue</span>' : '<span class="status pendente">Pendente</span>'}</p>
                <div class="submission-files">${sub.arquivo ? `<a href="${sub.arquivo}" target="_blank">Arquivo</a>` : 'Sem arquivo'}</div>
                <div class="grade-feedback">
                    <input type="number" class="student-grade" min="0" max="10" value="${sub.nota ?? ''}" placeholder="Nota">
                    <input type="text" class="student-feedback" value="${escapeHtml(sub.feedback ?? '')}" placeholder="Feedback">
                    <button class="save-grade-btn" data-aluno-id="${sub.aluno_id}"><i class="fas fa-save"></i> Salvar</button>
                </div>
            `;
            studentsSubmissionsList.appendChild(div);
        });

        // attach individual save
        document.querySelectorAll('.save-grade-btn').forEach(btn => {
            btn.onclick = async () => {
                const alunoId = btn.dataset.alunoId;
                const card = btn.closest('.student-submission-card');
                const nota = card.querySelector('.student-grade').value;
                const feedback = card.querySelector('.student-feedback').value;

                try {
                    const res = await fetch('salvar_nota.php', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ atividade_id: currentActivityId, aluno_id: alunoId, nota, feedback })
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert('Nota e feedback salvos.');
                        await loadSubmissionsForActivity(currentActivityId);
                    } else {
                        alert('Erro ao salvar: ' + (data.message || ''));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Erro ao salvar nota.');
                }
            };
        });

        showSection('submissionsSection');
    }

    // salvar todas notas
    if (saveAllNotesBtn) {
        saveAllNotesBtn.addEventListener('click', async () => {
            const payload = [];
            document.querySelectorAll('.student-submission-card').forEach(card => {
                const alunoId = card.querySelector('.save-grade-btn').dataset.alunoId;
                const nota = card.querySelector('.student-grade').value;
                const feedback = card.querySelector('.student-feedback').value;
                payload.push({ aluno_id: alunoId, nota, feedback });
            });

            try {
                const res = await fetch('salvar_notas.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ atividade_id: currentActivityId, notas: payload })
                });
                const data = await res.json();
                if (data.success) {
                    alert('Notas e feedbacks salvos com sucesso.');
                    await loadSubmissionsForActivity(currentActivityId);
                } else {
                    alert('Erro ao salvar notas: ' + (data.message || ''));
                }
            } catch (err) {
                console.error(err);
                alert('Erro ao salvar notas em lote.');
            }
        });
    }

    // helper escape HTML
    function escapeHtml(unsafe) {
        if (!unsafe && unsafe !== 0) return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // modal adicionar atividade
    if (openAddActivity) {
        openAddActivity.addEventListener('click', () => {
            const today = new Date();
            const t = new Date(today); t.setDate(today.getDate()+1);
            const inp = document.getElementById('newActivityDeadline');
            if (inp) inp.value = t.toISOString().split('T')[0];
            if (addActivityModal) addActivityModal.classList.add('show-modal');
            document.body.style.overflow = 'hidden';
        });
    }
    if (closeAddActivityModal) {
        closeAddActivityModal.addEventListener('click', () => {
            if (addActivityModal) addActivityModal.classList.remove('show-modal');
            document.body.style.overflow = '';
        });
    }

    if (uploadFilesButton) {
        uploadFilesButton.addEventListener('click', () => {
            const f = document.getElementById('newActivityFiles');
            if (f && f.files && f.files.length) {
                alert('Arquivo selecionado: ' + f.files[0].name + ' (o upload real será feito ao salvar a atividade).');
            } else {
                alert('Selecione um arquivo primeiro.');
            }
        });
    }

    if (newActivityForm) {
        newActivityForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(newActivityForm);
            try {
                const res = await fetch('salvar_atividade.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    alert('Atividade salva com sucesso.');
                    if (addActivityModal) addActivityModal.classList.remove('show-modal');
                    document.body.style.overflow = '';
                    await fetchAtividades();
                } else {
                    alert('Erro ao salvar: ' + (data.message || ''));
                }
            } catch (err) {
                console.error('Erro no envio:', err);
                alert('Erro ao salvar atividade.');
            }
        });
    }

    // back buttons
    if (backToActivitiesButton) backToActivitiesButton.addEventListener('click', () => showSection('activitiesSection'));
    if (backToSubmissionsButton) backToSubmissionsButton.addEventListener('click', () => showSection('submissionsSection'));

    // inicializa
    fetchAtividades();
    showSection('activitiesSection');
});
