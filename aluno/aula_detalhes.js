document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const sidebar = document.querySelector('.sidebar');
    const topNav = document.querySelector('.top-nav');
    const content = document.querySelector('.content');
    const cards = document.querySelectorAll('.dashboard-cards .card'); // Cards principais
    const activityItems = document.querySelectorAll('.next-activities .activity-item'); // Itens de atividade
    const themeIcon = themeToggle.querySelector('i');
    const sidebarLinks = document.querySelectorAll('.sidebar a');
    // const sidebarIcons = document.querySelectorAll('.sidebar a i'); // REMOVIDO: Será controlado por CSS
    // const topNavIcons = document.querySelectorAll('.top-nav i'); // REMOVIDO: Será controlado por CSS
    const dashboardHeaderElements = document.querySelectorAll('.dashboard-header h1, .dashboard-header p');
    const nextActivitiesTitle = document.querySelector('.next-activities h2');
    const dashboardTableTitle = document.querySelector('.dashboard-header-table h1'); // Título "Minhas Disciplinas"

    // Elementos de texto que precisam ser brancos no tema escuro (já existentes)
    const cardTitles = document.querySelectorAll('.card-info span');
    const cardValues = document.querySelectorAll('.card-info p');
    const studentName = document.querySelector('.user-info span');
    const activityTitles = document.querySelectorAll('.activity-details h3');
    const activityDetails = document.querySelectorAll('.activity-details p');
    const activityDates = document.querySelectorAll('.activity-date');
    const tableHeaders = document.querySelectorAll('.dashboard-table-container th');
    const tableData = document.querySelectorAll('.dashboard-table-container td');
    const disciplineLinks = document.querySelectorAll('.dashboard-table-container td a');
    const disciplineCodes = document.querySelectorAll('.dashboard-table-container .codigo-disciplina');
    const statusTexts = document.querySelectorAll('.dashboard-table-container .status');

    // NOVOS ELEMENTOS ADICIONADOS PARA TEMA ESCURO NO CSS (seletores)
    const aulaBox = document.querySelector('.aula-box');
    const selecaoInfoSpans = document.querySelectorAll('.selecao-info span');
    const boxModulos = document.querySelectorAll('.box-modulo, .box-certificacoes, .box-alunos');
    const geralObj = document.querySelector('.geral-obj');
    const infoVisao = document.querySelector('.info-visao');
    const atvProx = document.querySelector('.atv-prox');
    const projetoCard = document.querySelector('.projeto');
    const provaCard = document.querySelector('.prova');
    const moduloItems = document.querySelectorAll('.modulo1, .modulo2');
    const moduloMainTitles = document.querySelectorAll('.modulo-main h2, .modulo-main2 h2');
    // const moduloMainIcons = document.querySelectorAll('.modulo-main i, .modulo-main2 i'); // REMOVIDO: Será controlado por CSS
    const moduloHorarios = document.querySelectorAll('.modulo-horario');
    const atividadeItems = document.querySelectorAll('.atividade1, .atividade2');
    const atividadeMainTitles = document.querySelectorAll('.atividade-main h2, .atividade-main2 h2');
    // const atividadeMainIcons = document.querySelectorAll('.atividade-main i, .atividade-main2 i'); // REMOVIDO: Será controlado por CSS
    const atividadeDatas = document.querySelectorAll('.atividade-data');
    const statusAtividadeEntregue = document.querySelectorAll('.status-atividade-entregue');
    const statusAtividadePendente = document.querySelectorAll('.status-atividade-pendente');
    const notaAtividade = document.querySelectorAll('.nota-atividade');
    const detalhesAtividade = document.querySelectorAll('.detalhes-atividade');
    const entregarAtividade = document.querySelectorAll('.entregar-atividade');
    const progressoContent = document.getElementById('progresso-content');
    const progressoContentTitle = document.querySelector('#progresso-content h2');
    const progressItemTexts = document.querySelectorAll('.progress-item span:first-child');
    const progressBarContainers = document.querySelectorAll('.progress-bar-container');
    const progressBarFills = document.querySelectorAll('.progress-bar-fill');
    const progressValues = document.querySelectorAll('.progress-value');


    // Ícones (já existentes)
    // const cardIcons = document.querySelectorAll('.dashboard-cards .card i'); // REMOVIDO: Será controlado por CSS
    const mainActivityIcons = document.querySelectorAll('.next-activities .activity-icon i'); // Ícones das atividades (relógio, arquivo, lista)
    const activityIconContainers = document.querySelectorAll('.next-activities .activity-icon'); // Container dos ícones de atividade

    // --- Definição de Cores para Temas ---
    // Manteremos as cores de referência aqui, mas o JS não as aplicará diretamente nos ícones
    // Se uma cor de ícone for aplicada, será para casos muito específicos ou quando não houver regra CSS.

    const lightThemeColors = {
        bodyBg: '#f4f6f8',
        sidebarBg: '#ffffff',
        topNavBg: '#fff',
        contentBg: 'transparent',
        cardBg: '#fff',
        activityBg: '#fff',
        textColor: '#333',
        linkColor: '#333',
        iconColor: '#4956ca', // Cor original dos ícones no tema claro (azul) - MANTIDO para casos genéricos, mas CSS prevalecerá
        topNavHoverBg: '#cfe0f0',
        topNavLinkHoverColor: 'white',
        activityIconBg: '#f0f0f0',
        activityIconColor: '#777', // Cor dos ícones de atividade no tema claro
        tableHeaderColor: '#555',
        tableTextColor: '#333',
        tableLinkColor: '#4956ca',
        tableCodeColor: '#777',
        tableStatusAprovadoBg: '#509667',
        tableStatusAprovadoText: 'white',
        tableStatusExameBg: '#ffffff',
        tableStatusExameBorder: 'red',
        tableStatusExameText: 'red',
        tableStatusReprovadoBg: '#ffffff',
        tableStatusReprovadoBorder: 'red',
        tableStatusReprovadoText: 'red',
        tableBg: 'white',
        tableHeaderBg: 'white',

        // NOVAS CORES PARA ELEMENTOS ADICIONADOS
        aulaBoxBg: '#ffffff',
        selecaoInfoColor: '#000',
        boxModuloBg: '#8a97e9',
        boxCertificacoesBg: '#509667',
        boxAlunosBg: '#ca83c1',
        geralObjBg: '#ffffff',
        infoVisaoBg: '#ffffff',
        atvProxBg: '#ffffff',
        projetoBg: '#bd7171',
        provaBg: '#795caf',
        moduloItemBg: '#ffffff',
        moduloMainIconColor: '#509667', // Cor original do ícone de módulo (agora via CSS)
        moduloHorarioColor: '#509667',
        atividadeItemBg: '#ffffff',
        atividadeMainIconColor: '#509667', // Cor original do ícone de atividade (agora via CSS)
        atividadeDataColor: '#509667',
        detalhesAtividadeBg: 'white',
        entregarAtividadeBg: '#4956ca',
        progressoContentBg: '#f8f9fa',
        progressBarContainerBg: '#e0e0e0',
        progressBarFillBg: '#8a97e9',
    };

    const darkThemeColors = {
        bodyBg: '#333',
        sidebarBg: '#444',
        topNavBg: '#444',
        contentBg: '#000',
        cardBg: '#444',
        activityBg: '#444',
        textColor: '#eee',
        linkColor: '#eee',
        iconColor: '#eee',   // Branco para os ícones - MANTIDO para casos genéricos, mas CSS prevalecerá
        topNavHoverBg: 'DarkSlateBlue',
        topNavLinkHoverColor: 'white',
        activityIconBg: '#555',
        activityIconColor: '#eee',
        tableHeaderColor: '#eee',
        tableTextColor: '#eee',
        tableLinkColor: '#eee',
        tableCodeColor: '#ccc',
        tableStatusAprovadoBg: '#4CAF50',
        tableStatusAprovadoText: '#eee',
        tableStatusExameBg: '#444',
        tableStatusExameBorder: '#FF6347',
        tableStatusExameText: '#FF6347',
        tableStatusReprovadoBg: '#444',
        tableStatusReprovadoBorder: '#FF6347',
        tableStatusReprovadoText: '#FF6347',
        tableBg: '#444',
        tableHeaderBg: '#444',

        // NOVAS CORES PARA ELEMENTOS ADICIONADOS NO DARK THEME
        aulaBoxBg: '#444',
        selecaoInfoColor: '#bbb',
        boxModuloBg: '#7d8ffc',
        boxCertificacoesBg: '#4CAF50',
        boxAlunosBg: '#9C27B0',
        geralObjBg: '#444',
        infoVisaoBg: '#444',
        atvProxBg: '#444',
        projetoBg: '#8B0000',
        provaBg: '#4B0082',
        moduloItemBg: '#444',
        moduloMainIconColor: '#90EE90', // Cor de dark theme para ícone de módulo (agora via CSS)
        moduloHorarioColor: '#ccc',
        atividadeItemBg: '#444',
        atividadeMainIconColor: '#90EE90', // Cor de dark theme para ícone de atividade (agora via CSS)
        atividadeDataColor: '#ccc',
        detalhesAtividadeBg: '#555',
        entregarAtividadeBg: 'DarkSlateBlue',
        progressoContentBg: '#444',
        progressBarContainerBg: '#666',
        progressBarFillBg: '#7d8ffc',
    };

    let isDarkMode = localStorage.getItem('darkMode') === 'true'; // Verifica a preferência salva
    body.classList.toggle('dark-theme', isDarkMode);
    updateThemeIcon();
    applyTheme(isDarkMode ? darkThemeColors : lightThemeColors); // Aplica o tema inicial

    function updateThemeIcon() {
        themeIcon.classList.toggle('fa-sun', !isDarkMode);
        themeIcon.classList.toggle('fa-moon', isDarkMode);
    }

    function applyTheme(theme) {
        body.style.backgroundColor = theme.bodyBg;
        sidebar.style.backgroundColor = theme.sidebarBg;
        topNav.style.backgroundColor = theme.topNavBg;
        content.style.backgroundColor = theme.contentBg;


        // Aplica cores para elementos gerais
        const allTextElements = document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, label, th, td');
        allTextElements.forEach(el => {
            if (el.classList.contains('status-atividade-entregue') && !isDarkMode) {
                el.style.color = lightThemeColors.tableStatusAprovadoText;
            } else if (el.classList.contains('status-atividade-pendente') && !isDarkMode) {
                el.style.color = lightThemeColors.tableStatusExameText;
            } else if (el.classList.contains('nota-atividade') && !isDarkMode) {
                el.style.color = 'rgb(0,0,0)';
            }
            else {
                el.style.color = theme.textColor;
            }
        });

        // REMOVIDO: Aplicação genérica de cor para 'i' via JS. Deixe o CSS controlar.
        // allIcons.forEach(icon => { /* ... */ });

        cards.forEach(card => {
            card.style.backgroundColor = theme.cardBg;
            // REMOVIDO: Aplicação de cor para ícone do card via JS. Deixe o CSS controlar.
            // const icon = card.querySelector('i'); if (icon) { /* ... */ }
        });

        activityItems.forEach(item => {
            item.style.backgroundColor = theme.activityBg;
            const iconContainer = item.querySelector('.activity-icon');
            const icon = item.querySelector('.activity-icon i'); // Ícone dentro do container
            if (iconContainer) {
                iconContainer.style.backgroundColor = theme.activityIconBg;
            }
            if (icon) {
                // A cor dos ícones de atividade ainda será controlada pelo JS aqui,
                // já que não temos uma regra CSS específica para .activity-icon i no seu CSS.
                icon.style.color = theme.activityIconColor;
            }
        });

        // Específicos para os links da sidebar e seus ícones
        sidebarLinks.forEach(link => link.style.color = theme.linkColor);
        // REMOVIDO: sidebarIcons.forEach(icon => icon.style.color = isDarkMode ? theme.iconColor : '#4956ca');
        // REMOVIDO: topNavIcons.forEach(icon => icon.style.color = theme.iconColor);
        // Os ícones da sidebar e da top nav agora serão controlados 100% pelo CSS.
        // Se precisar de cor específica para 'themeIcon', ele já é tratado separadamente.
        themeIcon.style.color = theme.iconColor; // A cor do ícone de tema ainda pode ser definida aqui, é um caso específico.


        dashboardHeaderElements.forEach(element => {
            element.style.color = theme.textColor;
        });
        if (nextActivitiesTitle) {
            nextActivitiesTitle.style.color = theme.textColor;
        }
        if (dashboardTableTitle) {
            dashboardTableTitle.style.color = theme.textColor;
        }

        const dashboardTableContainer = document.querySelector('.dashboard-table-container');
        const dashboardHeaderTable = document.querySelector('.dashboard-header-table');

        if (dashboardTableContainer) {
            dashboardTableContainer.style.backgroundColor = theme.tableBg;
            const table = dashboardTableContainer.querySelector('table');
            if (table) {
                const headers = table.querySelectorAll('th');
                headers.forEach(header => header.style.color = theme.tableHeaderColor);
                const data = table.querySelectorAll('td');
                data.forEach(d => d.style.color = theme.tableTextColor);
                const links = table.querySelectorAll('td a');
                links.forEach(link => link.style.color = theme.tableLinkColor);
                const codes = table.querySelectorAll('.codigo-disciplina');
                codes.forEach(code => code.style.color = theme.tableCodeColor);
                const statuses = table.querySelectorAll('.status');
                statuses.forEach(status => {
                    if (status.classList.contains('aprovado')) {
                        status.style.backgroundColor = theme.tableStatusAprovadoBg;
                        status.style.color = theme.tableStatusAprovadoText;
                        status.style.border = 'none';
                    } else if (status.classList.contains('exame')) {
                        status.style.backgroundColor = theme.tableStatusExameBg;
                        status.style.color = theme.tableStatusExameText;
                        status.style.border = `1px solid ${theme.tableStatusExameBorder}`;
                    } else if (status.classList.contains('reprovado')) {
                        status.style.backgroundColor = theme.tableStatusReprovadoBg;
                        status.style.color = theme.tableStatusReprovadoText;
                        status.style.border = `1px solid ${theme.tableStatusReprovadoBorder}`;
                    }
                });
            }
        }

        if (dashboardHeaderTable) {
            dashboardHeaderTable.style.backgroundColor = theme.tableHeaderBg;
            const h1 = dashboardHeaderTable.querySelector('h1');
            if (h1) {
                h1.style.color = theme.textColor;
            }
        }

        // --- NOVOS ELEMENTOS ---
        if (aulaBox) {
            aulaBox.style.backgroundColor = theme.aulaBoxBg;
        }

        selecaoInfoSpans.forEach(span => {
            span.style.color = theme.selecaoInfoColor;
            // A cor da aba ativa (active-tab) é controlada pelo CSS,
            // mas se você tem uma classe 'active' no JS que também altera a cor do texto,
            // pode ser necessário forçar aqui, ou remover a lógica de cor 'active' do JS
            // e deixar 100% no CSS. Recomendo deixar no CSS como está agora.
            if (span.classList.contains('active')) {
                // Removendo o style.color direto para active-tab para deixar o CSS controlar
                span.style.color = '';
            }
        });

        boxModulos.forEach(box => {
            if (box.classList.contains('box-modulo')) {
                box.style.backgroundColor = theme.boxModuloBg;
            } else if (box.classList.contains('box-certificacoes')) {
                box.style.backgroundColor = theme.boxCertificacoesBg;
            } else if (box.classList.contains('box-alunos')) {
                box.style.backgroundColor = theme.boxAlunosBg;
            }
        });

        if (geralObj) {
            geralObj.style.backgroundColor = theme.geralObjBg;
            geralObj.style.color = theme.textColor;
        }
        if (infoVisao) {
            infoVisao.style.backgroundColor = theme.infoVisaoBg;
            infoVisao.style.color = theme.textColor;
        }
        if (atvProx) {
            atvProx.style.backgroundColor = theme.atvProxBg;
            atvProx.style.color = theme.textColor;
        }
        if (projetoCard) {
            projetoCard.style.backgroundColor = theme.projetoBg;
            projetoCard.style.color = theme.textColor;
        }
        if (provaCard) {
            provaCard.style.backgroundColor = theme.provaBg;
            provaCard.style.color = theme.textColor;
        }

        moduloItems.forEach(item => {
            item.style.backgroundColor = theme.moduloItemBg;
        });

        moduloMainTitles.forEach(title => {
            title.style.color = theme.textColor;
        });
        // REMOVIDO: moduloMainIcons.forEach(icon => { /* ... */ });
        // As cores dos ícones de módulo agora são definidas 100% pelo CSS.

        moduloHorarios.forEach(horario => {
            horario.style.color = theme.moduloHorarioColor;
        });

        atividadeItems.forEach(item => {
            item.style.backgroundColor = theme.atividadeItemBg;
        });
        atividadeMainTitles.forEach(title => {
            title.style.color = theme.textColor;
        });
        // REMOVIDO: atividadeMainIcons.forEach(icon => { /* ... */ });
        // As cores dos ícones de atividade agora são definidas 100% pelo CSS.

        atividadeDatas.forEach(data => {
            data.style.color = theme.atividadeDataColor;
        });

        statusAtividadeEntregue.forEach(status => {
            status.style.backgroundColor = theme.tableStatusAprovadoBg;
            status.style.color = theme.tableStatusAprovadoText;
        });
        statusAtividadePendente.forEach(status => {
            status.style.backgroundColor = theme.tableStatusExameBg;
            status.style.color = theme.tableStatusExameText;
            status.style.border = `1px solid ${theme.tableStatusExameBorder}`;
        });
        notaAtividade.forEach(nota => {
            nota.style.color = theme.textColor;
        });
        detalhesAtividade.forEach(detalhe => {
            detalhe.style.backgroundColor = theme.detalhesAtividadeBg;
            detalhe.style.color = theme.textColor;
        });
        entregarAtividade.forEach(entregar => {
            entregar.style.backgroundColor = theme.entregarAtividadeBg;
        });

        if (progressoContent) {
            progressoContent.style.backgroundColor = theme.progressoContentBg;
        }
        if (progressoContentTitle) {
            progressoContentTitle.style.color = theme.textColor;
        }
        progressItemTexts.forEach(text => {
            text.style.color = theme.textColor;
        });
        progressBarContainers.forEach(container => {
            container.style.backgroundColor = theme.progressBarContainerBg;
        });
        progressBarFills.forEach(fill => {
            fill.style.backgroundColor = theme.progressBarFillBg;
        });
        progressValues.forEach(value => {
            value.style.color = theme.textColor;
        });
    }

    themeToggle.addEventListener('click', function() {
        isDarkMode = !isDarkMode;
        // const currentTheme = isDarkMode ? darkThemeColors : lightThemeColors; // Não precisamos mais disso para aplicar diretamente

        body.classList.toggle('dark-theme');

        // Para garantir que as cores sejam reavaliadas pelo CSS após a mudança da classe,
        // podemos chamar applyTheme, mas ele agora só cuidará de backgrounds e textos, não ícones.
        applyTheme(isDarkMode ? darkThemeColors : lightThemeColors);
        updateThemeIcon();
        localStorage.setItem('darkMode', isDarkMode);
        console.log('Tema alterado para:', isDarkMode ? 'escuro' : 'claro');
    });

    // Modifica o hover na sidebar para azul royal no tema escuro
    sidebarLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            if (isDarkMode) {
                this.style.backgroundColor = 'DarkSlateBlue';
                this.style.color = 'white';
                // Ícones da sidebar no hover no tema escuro: a cor é controlada pelo CSS agora (.sidebar a:hover i)
            } else {
                this.style.backgroundColor = '#cfe0f0';
                this.style.color = 'white';
                // Ícones da sidebar no hover no tema claro: a cor é controlada pelo CSS agora (.sidebar a:hover i)
            }
        });

        link.addEventListener('mouseleave', function() {
            this.style.backgroundColor = ''; // Remove background do hover
            if (!this.classList.contains('active')) {
                this.style.color = isDarkMode ? darkThemeColors.linkColor : lightThemeColors.linkColor;
            } else {
                // Se a aba estiver ativa, a cor já é definida pelo CSS, então não precisamos resetar aqui
                this.style.color = ''; // Remove o style inline para que o CSS controle a cor do link ativo
            }
            // Ícones da sidebar: suas cores são controladas pelo CSS e não precisam ser resetadas aqui
        });
    });

    // --- Lógica de Abas para "Seleção Info" ---
    const tabSpans = document.querySelectorAll('.selecao-info span');
    const tabContents = document.querySelectorAll('.tab-content');

    tabSpans.forEach(span => {
        span.addEventListener('click', function() {
            tabSpans.forEach(s => {
                s.classList.remove('active');
                s.classList.remove('active-tab'); // Remova também a classe 'active-tab' para garantir
                // s.style.color = ''; // Remova qualquer style inline de cor que possa ter sido aplicado aqui.
            });
            tabContents.forEach(c => c.classList.remove('active'));

            this.classList.add('active'); // Mantém a classe 'active' para o JS
            this.classList.add('active-tab'); // Adiciona a classe 'active-tab' para o CSS aplicar o estilo

            const targetId = this.id.replace('-tab', '-content');
            const targetContent = document.getElementById(targetId);

            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });

    // Ativa a primeira aba ao carregar a página
    document.getElementById('visao-geral-tab').click();

    // Função para atualizar as barras de progresso
    function updateProgressBars() {
        const generalProgressBar = document.querySelector('.progress-item:nth-child(1) .progress-bar-fill');
        const generalProgressValue = document.querySelector('.progress-item:nth-child(1) .progress-value');
        const generalPercentage = 9;
        if (generalProgressBar) {
            generalProgressBar.style.width = generalPercentage + '%';
        }
        if (generalProgressValue) {
            generalProgressValue.textContent = generalPercentage + '%';
        }

        const modulesProgressBar = document.querySelector('.progress-item:nth-child(2) .progress-bar-fill');
        const modulesProgressValue = document.querySelector('.progress-item:nth-child(2) .progress-value');
        const completedModules = 3;
        const totalModules = 5;
        const modulesPercentage = (completedModules / totalModules) * 100;
        if (modulesProgressBar) {
            modulesProgressBar.style.width = modulesPercentage + '%';
        }
        if (modulesProgressValue) {
            modulesProgressValue.textContent = `${completedModules}/${totalModules}`;
        }

        const activitiesProgressBar = document.querySelector('.progress-item:nth-child(3) .progress-bar-fill');
        const activitiesProgressValue = document.querySelector('.progress-item:nth-child(3) .progress-value');
        const deliveredActivities = 1;
        const totalActivities = 3;
        const activitiesPercentage = (deliveredActivities / totalActivities) * 100;
        if (activitiesProgressBar) {
            activitiesProgressBar.style.width = activitiesPercentage + '%';
        }
        if (activitiesProgressValue) {
            activitiesProgressValue.textContent = `${deliveredActivities}/${totalActivities}`;
        }
    }

    updateProgressBars();
});