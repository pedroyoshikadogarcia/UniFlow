document.addEventListener('DOMContentLoaded', function() {
    // --- Variáveis para controle de Tema e Elementos do DOM ---
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body; // Renomeado de elementoBody para consistência
    const sidebar = document.querySelector('.sidebar');
    const topNav = document.querySelector('.top-nav');
    const content = document.querySelector('.content');
    const cards = document.querySelectorAll('.dashboard-cards .card');
    const activityItems = document.querySelectorAll('.next-activities .activity-item');
    const themeIcon = themeToggle.querySelector('i');
    const sidebarLinks = document.querySelectorAll('.sidebar a');
    const sidebarIcons = document.querySelectorAll('.sidebar a i');
    const topNavIcons = document.querySelectorAll('.top-nav i');
    const dashboardHeaderElements = document.querySelectorAll('.dashboard-header h1, .dashboard-header p');
    const nextActivitiesTitle = document.querySelector('.next-activities h2');
    const dashboardTableTitle = document.querySelector('.dashboard-header-table h1');

    // Elementos de texto que precisam ser brancos no tema escuro
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

    const contentBoxes = document.querySelectorAll('.content-box');

    // Ícones
    const cardIcons = document.querySelectorAll('.dashboard-cards .card i');
    const mainActivityIcons = document.querySelectorAll('.next-activities .activity-icon i');
    const activityIconContainers = document.querySelectorAll('.next-activities .activity-icon');

    // Armazena a cor de fundo original do content (para tema claro)
    const originalContentBg = window.getComputedStyle(content).backgroundColor;

    // --- Definições de Cores para os Temas ---
    const lightThemeColors = {
        bodyBg: '#f4f6f8',
        sidebarBg: '#ffffff',
        topNavBg: '#fff',
        contentBg: originalContentBg,
        cardBg: '#fff',
        activityBg: '#fff',
        textColor: '#333',
        linkColor: '#333',
        iconColor: '#4956ca',
        topNavHoverBg: '#cfe0f0',
        topNavLinkHoverColor: '#333',
        activityIconColor: '#777',
        tableHeaderColor: '#555',
        tableTextColor: '#333',
        tableLinkColor: '#4956ca',
        tableCodeColor: '#777',
        tableStatusAprovadoColor: 'inherit',
        tableStatusExameColor: 'inherit',
        tableStatusReprovadoColor: 'inherit',
        tableBg: 'white',
        tableHeaderBg: 'white',
        contentBoxBg: 'white',
        contentBoxTextColor: '#333',
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
        iconColor: '#eee',
        topNavHoverBg: 'DarkSlateBlue',
        topNavLinkHoverColor: '#eee',
        activityIconColor: '#eee',
        tableHeaderColor: '#eee',
        tableTextColor: '#eee',
        tableLinkColor: '#eee',
        tableCodeColor: '#ccc',
        tableStatusAprovadoColor: '#a7f070',
        tableStatusExameColor: '#ffeb3b',
        tableStatusReprovadoColor: '#f44336',
        tableBg: '#444',
        tableHeaderBg: '#444',
        contentBoxBg: '#333',
        contentBoxTextColor: '#eee',
    };

    // --- Lógica de Tema ---
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

        contentBoxes.forEach(box => {
            box.style.backgroundColor = theme.contentBoxBg;
            box.style.color = theme.contentBoxTextColor;
        });

        cards.forEach(card => {
            card.style.backgroundColor = theme.cardBg;
            card.style.color = theme.textColor;
            const icon = card.querySelector('i');
            if (icon) {
                icon.style.color = theme.iconColor;
            }
        });

        activityItems.forEach(item => {
            item.style.backgroundColor = theme.activityBg;
            item.style.color = theme.textColor;
            const iconContainer = item.querySelector('.activity-icon');
            const icon = item.querySelector('.activity-icon i');
            if (icon) {
                icon.style.color = theme.iconColor;
            }
        });

        sidebarLinks.forEach(link => link.style.color = theme.linkColor);
        sidebarIcons.forEach(icon => icon.style.color = theme.iconColor);
        topNavIcons.forEach(icon => icon.style.color = theme.iconColor);
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
            dashboardTableContainer.style.color = theme.tableTextColor;
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
                    status.style.color = theme.tableTextColor;
                    if (status.classList.contains('aprovado')) {
                        status.style.color = theme.tableStatusAprovadoColor;
                    } else if (status.classList.contains('exame')) {
                        status.style.color = theme.tableStatusExameColor;
                    } else if (status.classList.contains('reprovado')) {
                        status.style.color = theme.tableStatusReprovadoColor;
                    }
                });
            }
        }
        if (dashboardHeaderTable) {
            dashboardHeaderTable.style.backgroundColor = theme.tableHeaderBg;
            dashboardHeaderTable.style.color = theme.textColor;
            const h1 = dashboardHeaderTable.querySelector('h1');
            if (h1) {
                h1.style.color = theme.textColor;
            }
        }

        cardTitles.forEach(title => title.style.color = theme.textColor);
        cardValues.forEach(value => value.style.color = theme.textColor);
        if (studentName) {
            studentName.style.color = theme.textColor;
        }
        activityTitles.forEach(title => title.style.color = theme.textColor);
        activityDetails.forEach(detail => detail.style.color = theme.textColor);
        activityDates.forEach(date => date.style.color = theme.textColor);

        cardIcons.forEach(icon => {
            icon.style.color = theme.iconColor;
        });

        activityIconContainers.forEach(container => {
            container.style.backgroundColor = isDarkMode ? '#555' : '#f0f0f0';
            const icon = container.querySelector('i');
            if (icon) {
                icon.style.color = theme.iconColor;
            }
        });
    }

    // --- Event Listeners para o Tema ---
    themeToggle.addEventListener('click', function() {
        isDarkMode = !isDarkMode;
        const currentTheme = isDarkMode ? darkThemeColors : lightThemeColors;
        applyTheme(currentTheme);
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
            } else {
                this.style.backgroundColor = '#cfe0f0';
                this.style.color = 'white';
            }
        });

        link.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.color = isDarkMode ? darkThemeColors.linkColor : lightThemeColors.linkColor;
        });
    });

    // --- Lógica de Acessibilidade (Tamanho da Fonte) ---
    const btnAumentarFonte = document.getElementById('aumentar-fonte');
    const btnDiminuirFonte = document.getElementById('diminuir-fonte');

    const tamanhoFontePadrao = 16; // Tamanho base comum para navegadores
    const fatorAumento = 1.2; // Aumento de 20%
    const fatorDiminuicao = 0.8; // Diminuição de 20%
    const tamanhoFonteMinimo = 0.8 * tamanhoFontePadrao; // 80% do padrão
    const tamanhoFonteMaximo = 1.6 * tamanhoFontePadrao; // 160% do padrão

    // Obtém o tamanho da fonte salvo no localStorage ou usa o tamanho padrão
    let tamanhoFonteAtual = parseFloat(localStorage.getItem('tamanhoFonte')) || tamanhoFontePadrao;

    // Aplica o tamanho da fonte inicial ao carregar a página
    body.style.fontSize = `${tamanhoFonteAtual}px`; // Usa a variável 'body' já existente

    if (btnAumentarFonte) {
        btnAumentarFonte.addEventListener('click', function() {
            tamanhoFonteAtual *= fatorAumento;
            tamanhoFonteAtual = Math.min(tamanhoFonteAtual, tamanhoFonteMaximo);
            body.style.fontSize = `${tamanhoFonteAtual}px`;
            localStorage.setItem('tamanhoFonte', tamanhoFonteAtual); // Salva a preferência
        });
    }

    if (btnDiminuirFonte) {
        btnDiminuirFonte.addEventListener('click', function() {
            tamanhoFonteAtual *= fatorDiminuicao;
            tamanhoFonteAtual = Math.max(tamanhoFonteAtual, tamanhoFonteMinimo);
            body.style.fontSize = `${tamanhoFonteAtual}px`;
            localStorage.setItem('tamanhoFonte', tamanhoFonteAtual); // Salva a preferência
        });
    }

    // --- Lógica de Navegação da Sidebar ---
    const navLinks = document.querySelectorAll('.sidebar .nav-link'); // Mantido aqui para garantir que esteja acessível

    navLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const targetId = this.getAttribute('data-target');

            // Oculta todas as caixas de conteúdo
            contentBoxes.forEach(box => {
                box.style.display = 'none';
            });

            // Exibe a caixa de conteúdo alvo
            const targetBox = document.getElementById(targetId);
            if (targetBox) {
                targetBox.style.display = 'block';
            }

            // Remove a classe 'active' de todos os links da sidebar
            navLinks.forEach(navLink => {
                navLink.classList.remove('active');
            });

            // Adiciona a classe 'active' ao link clicado
            this.classList.add('active');
        });
    });

    // Define o primeiro link como ativo inicialmente e mostra sua box
    if (navLinks.length > 0) {
        navLinks[0].classList.add('active');
        const initialTargetId = navLinks[0].getAttribute('data-target');
        const initialTargetBox = document.getElementById(initialTargetId);
        if (initialTargetBox) {
            initialTargetBox.style.display = 'block';
        }
    }
});