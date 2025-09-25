<?php
// Este arquivo é incluído em atividades.php
?>

<!-- seção entregas dos alunos -->
<div id="submissionsSection" class="main-section">
    <div class="submissions-content">
        <div class="modal-header">
            <h2 id="activityTitleSubmissions"></h2>
            <button id="backToActivitiesButton" class="action-button back-button">
                <i class="fas fa-arrow-left"></i> Voltar
            </button>
        </div>
        <div class="submissions-summary">
            <p><strong>Total de Alunos:</strong> <span id="totalStudents"></span></p>
            <p><strong>Entregas Recebidas:</strong> <span id="submittedCount"></span></p>
            <p><strong>Pendentes:</strong> <span id="pendingCount"></span></p>
        </div>
        <div class="students-submissions-list" id="studentsSubmissionsList"></div>
        <div class="overall-actions">
            <button class="add-activity-button">Salvar Todas as Notas</button>
        </div>
    </div>
</div>

<!-- detalhe das entregas dos alunos -->
<div id="studentDetailsSection" class="main-section">
    <div class="student-details-content">
        <div class="modal-header">
            <h2 id="studentSubmissionTitle"></h2>
            <button id="backToSubmissionsButton" class="action-button back-button">
                <i class="fas fa-arrow-left"></i> Voltar à Lista
            </button>
        </div>
        <div class="activity-enunciado-section">
            <h3>Enunciado da Atividade</h3>
            <p id="activityEnunciadoDetail"></p>
            <p id="professorFileLink">
                <i class="fas fa-file-alt"></i> 
                <a href="#" target="_blank" id="professorFileLinkHref"></a> (Arquivo do Professor)
            </p>
        </div>
        <div class="student-submission-section">
            <h3>Entrega do Aluno</h3>
            <div id="submissionContent"></div>
            <div class="grade-and-feedback-section">
                <h4>Nota e Feedback</h4>
                <div class="grade-input-wrapper">
                    <label for="studentFinalGrade">Nota Final:</label>
                    <input type="number" id="studentFinalGrade" min="0" max="10" />
                </div>
                <div class="feedback-wrapper">
                    <label for="professorFeedback">Feedback:</label>
                    <textarea id="professorFeedback" placeholder="Deixe seu feedback aqui..."></textarea>
                </div>
                <button class="add-activity-button save-student-grade-button">Salvar Nota e Feedback</button>
            </div>
        </div>
    </div>
</div>

<!-- modal: adicionar nova atividade -->
<div id="addActivityModal" class="modal-overlay">
    <div class="modal-content-new-activity">
        <div class="modal-header">
            <h2>Adicionar Nova Atividade</h2>
            <button id="closeAddActivityModal" class="action-button back-button">
                <i class="fas fa-times"></i> Fechar
            </button>
        </div>
        <form id="newActivityForm">
            <div class="form-group">
                <label for="newActivityTitle">Título da Atividade:</label>
                <input type="text" id="newActivityTitle" required />
            </div>
            <div class="form-group">
                <label for="newActivityType">Tipo de Atividade:</label>
                <select id="newActivityType" required>
                    <option value="">Selecione o Tipo</option>
                    <option value="trabalho">Trabalho</option>
                    <option value="atividade">Atividade</option>
                    <option value="prova">Prova</option>
                </select>
            </div>
            <div class="form-group">
                <label for="newActivityDeadline">Prazo:</label>
                <input type="date" id="newActivityDeadline" required />
            </div>
            <div class="form-group">
                <label for="newActivityValue">Valor (pontos):</label>
                <input type="number" id="newActivityValue" min="0" required />
            </div>
            <div class="form-group">
                <label for="newActivityEnunciado">Enunciado / Descrição:</label>
                <textarea id="newActivityEnunciado" rows="5" required></textarea>
            </div>
            <div class="form-group file-upload-group">
                <label for="newActivityFiles">Anexar Arquivos:</label>
                <div class="custom-file-input-wrapper">
                    <input type="file" id="newActivityFiles" multiple />
                    <label for="newActivityFiles" class="custom-file-upload-button">
                        <i class="fas fa-paperclip"></i> Escolher Arquivos
                    </label>
                </div>
                <div class="file-type-icons">
                    <span class="file-type-info"><i class="fas fa-image"></i> Mídia</span>
                    <span class="file-type-info"><i class="fas fa-link"></i> Links</span>
                    <span class="file-type-info"><i class="fas fa-file-alt"></i> Docs</span>
                </div>
                <button type="button" class="action-button upload-files-button">
                    <i class="fas fa-upload"></i> Enviar Anexos
                </button>
            </div>
            <button type="submit" class="add-activity-button save-new-activity-button">
                <i class="fas fa-save"></i> Salvar Nova Atividade
            </button>
        </form>
    </div>
</div>
<script src="atividade.js"></script>
<script src="main.js"></script>
