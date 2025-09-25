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
    const sidebarIcons = document.querySelectorAll('.sidebar a i');
    const topNavIcons = document.querySelectorAll('.top-nav i');
    const dashboardHeaderElements = document.querySelectorAll('.dashboard-header h1, .dashboard-header p');
    const nextActivitiesTitle = document.querySelector('.next-activities h2');
    const dashboardTableTitle = document.querySelector('.dashboard-header-table h1'); // Título "Minhas Disciplinas"

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


    // Ícones
    const cardIcons = document.querySelectorAll('.dashboard-cards .card i'); // Ícones dos cards
    const mainActivityIcons = document.querySelectorAll('.next-activities .activity-icon i'); // Ícones das atividades (relógio, arquivo, lista)
    const activityIconContainers = document.querySelectorAll('.next-activities .activity-icon'); // Container dos ícones de atividade

    const lightThemeColors = {
        bodyBg: '#f4f6f8',
        sidebarBg: '#ffffff',
        topNavBg: '#fff',
        contentBg: window.getComputedStyle(content).backgroundColor,
        cardBg: '#fff',
        activityBg: '#fff',
        textColor: '#333',
        linkColor: '#333',
        iconColor: '#4956ca', // Cor original dos ícones no tema claro (azul)
        topNavHoverBg: '#cfe0f0',
        topNavLinkHoverColor: '#333',
        activityIconBg: '#f0f0f0',
        activityIconColor: '#777', // Cor dos ícones de atividade no tema claro
        tableHeaderColor: '#555',
        tableTextColor: '#333',
        tableLinkColor: '#4956ca',
        tableCodeColor: '#777',
        tableStatusAprovadoColor: 'inherit',
        tableStatusExameColor: 'inherit',
        tableStatusReprovadoColor: 'inherit',
        tableBg: 'white', // Cor de fundo da tabela no tema claro
        tableHeaderBg: 'white', // Cor de fundo do cabeçalho da tabela no tema claro
    };

    const darkThemeColors = {
        bodyBg: '#333',
        sidebarBg: '#444',
        topNavBg: '#444',
        contentBg: '#000',
        cardBg: '#444',
        activityBg: '#444',
        textColor: '#eee', // Branco para o texto
        linkColor: '#eee', // Branco para os links
        iconColor: '#eee',   // Branco para os ícones
        topNavHoverBg: 'DarkSlateBlue', // Roxo mais escuro para o hover na navbar
        topNavLinkHoverColor: '#eee',
        activityIconBg: '#555',
        activityIconColor: '#eee', // Branco para os ícones de atividade no tema escuro
        tableHeaderColor: '#eee',
        tableTextColor: '#eee',
        tableLinkColor: '#eee',
        tableCodeColor: '#ccc',
        tableStatusAprovadoColor: '#a7f070', // Verde claro
        tableStatusExameColor: '#ffeb3b',   // Amarelo
        tableStatusReprovadoColor: '#f44336', // Vermelho
        tableBg: '#444', // Cor de fundo da tabela no tema escuro
        tableHeaderBg: '#444', // Cor de fundo do cabeçalho da tabela no tema escuro
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
            if (iconContainer) {
                iconContainer.style.backgroundColor = theme.activityIconBg;
            }
            if (icon) {
                icon.style.color = theme.activityIconColor;
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
                    status.style.color = theme.tableTextColor; // Define uma cor padrão para o texto do status
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

        // Força a cor dos textos dos cards e atividades
        cardTitles.forEach(title => title.style.color = theme.textColor);
        cardValues.forEach(value => value.style.color = theme.textColor);
        if (studentName) {
            studentName.style.color = theme.textColor;
        }
        activityTitles.forEach(title => title.style.color = theme.textColor);
        activityDetails.forEach(detail => detail.style.color = theme.textColor);
        activityDates.forEach(date => date.style.color = theme.textColor);

        // Aplica a cor dos ícones dos cards
        cardIcons.forEach(icon => {
            icon.style.color = theme.iconColor;
        });
    }

    themeToggle.addEventListener('click', function() {
        isDarkMode = !isDarkMode;
        const currentTheme = isDarkMode ? darkThemeColors : lightThemeColors;
        applyTheme(currentTheme);
        updateThemeIcon();
        localStorage.setItem('darkMode', isDarkMode); // Salva a preferência
        console.log('Tema alterado para:', isDarkMode ? 'escuro' : 'claro');
    });

    // Modifica o hover na sidebar para azul royal no tema escuro
    sidebarLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            if (isDarkMode) {
                this.style.backgroundColor = 'DarkSlateBlue';
                this.style.color = 'white'; // Para garantir que o texto seja branco no hover
            } else {
                this.style.backgroundColor = '#cfe0f0';
                this.style.color = 'white';
            }
        });

        link.addEventListener('mouseleave', function() {
            this.style.backgroundColor = ''; // Remove a cor de fundo do hover
            this.style.color = isDarkMode ? darkThemeColors.linkColor : lightThemeColors.linkColor; // Restaura a cor original do texto
        });
    });
});