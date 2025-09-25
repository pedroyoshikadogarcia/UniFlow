document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const sidebar = document.querySelector('.sidebar');
    const topNav = document.querySelector('.top-nav');
    const content = document.querySelector('.content');
    const topInfoContainer = document.querySelector('.top-info-container');
    const studentProfile = document.querySelector('.student-profile');
    const profileContainer = document.querySelector('.profile-container');
    const themeIcon = themeToggle.querySelector('i');
    const sidebarLinks = document.querySelectorAll('.sidebar a');
    const sidebarIcons = document.querySelectorAll('.sidebar a i');
    const topNavIcons = document.querySelectorAll('.top-nav i');
    const studentNameElement = document.querySelector('.profile-header .student-name');
    const studentEmailElement = document.querySelector('.profile-header .student-email');
    const academicInfoElements = document.querySelectorAll('.academic-info h3, .academic-info p, .academic-info strong, .academic-info .status');
    const quickAccessTitle = document.querySelector('.quick-access h3');
    const quickAccessButtons = document.querySelectorAll('.quick-access button');
    const profileCard = document.querySelector('.profile-card');
    const profileInfoItems = document.querySelectorAll('.profile-card .info-item');
    const profileInfoStrong = document.querySelectorAll('.profile-card .info-item strong');
    const profileInfoParagraph = document.querySelectorAll('.profile-card .info-item p');


    const lightThemeColors = {
        bodyBg: '#f4f6f8',
        sidebarBg: '#ffffff',
        topNavBg: '#fff',
        contentBg: '#f4f6f8',
        cardBg: '#fff',
        textColor: '#333',
        linkColor: '#333',
        iconColor: '#4956ca',
        topNavHoverBg: '#cfe0f0',
        topNavLinkHoverColor: '#333',
        profileCardBg: '#fff',
        quickAccessButtonBg: '#fff', // Adicionado para a cor de fundo dos botões
    };

    const darkThemeColors = {
        bodyBg: '#333',
        sidebarBg: '#444',
        topNavBg: '#444',
        contentBg: '#000',
        cardBg: '#444',
        textColor: '#eee',
        linkColor: '#eee',
        iconColor: '#eee',
        topNavHoverBg: 'DarkSlateBlue',
        topNavLinkHoverColor: '#eee',
        profileCardBg: '#444',
        quickAccessButtonBg: '#444', // Adicionado para a cor de fundo dos botões
    };

    let isDarkMode = localStorage.getItem('darkMode') === 'true';
    body.classList.toggle('dark-theme', isDarkMode);
    updateThemeIcon();
    applyTheme(isDarkMode ? darkThemeColors : lightThemeColors);

    function updateThemeIcon() {
        themeIcon.classList.toggle('fa-sun', !isDarkMode);
        themeIcon.classList.toggle('fa-moon', isDarkMode);
    }

    function applyTheme(theme) {
        body.style.backgroundColor = theme.bodyBg;
        sidebar.style.backgroundColor = theme.sidebarBg;
        topNav.style.backgroundColor = theme.topNavBg;
        content.style.backgroundColor = theme.contentBg;

        if (topInfoContainer) {
            topInfoContainer.style.backgroundColor = theme.contentBg;
            topInfoContainer.style.color = theme.textColor;
        }

        if (studentProfile) {
            studentProfile.style.backgroundColor = theme.cardBg;
            studentProfile.style.color = theme.textColor;
            const links = studentProfile.querySelectorAll('a');
            links.forEach(link => link.style.color = theme.linkColor);
            const icons = studentProfile.querySelectorAll('i');
            icons.forEach(icon => icon.style.color = theme.iconColor);
            if (studentNameElement) studentNameElement.style.color = theme.textColor;
            if (studentEmailElement) studentEmailElement.style.color = theme.textColor;
            academicInfoElements.forEach(element => element.style.color = theme.textColor);
            if (quickAccessTitle) quickAccessTitle.style.color = theme.textColor;
            quickAccessButtons.forEach(button => {
                button.style.backgroundColor = theme.quickAccessButtonBg; // Usa a cor de fundo específica
                button.style.color = theme.textColor;
                const icon = button.querySelector('i');
                if (icon) icon.style.color = theme.iconColor;
            });
        }

        if (profileContainer) {
            profileContainer.style.backgroundColor = theme.contentBg;
            profileContainer.style.color = theme.textColor;
        }

        if (profileCard) {
            profileCard.style.backgroundColor = theme.profileCardBg;
            profileCard.style.color = theme.textColor;
            profileInfoItems.forEach(item => item.style.color = theme.textColor);
            profileInfoStrong.forEach(strong => strong.style.color = theme.textColor);
            profileInfoParagraph.forEach(p => p.style.color = theme.textColor);
        }

        sidebarLinks.forEach(link => link.style.color = theme.linkColor);
        sidebarIcons.forEach(icon => icon.style.color = theme.iconColor);
        topNavIcons.forEach(icon => icon.style.color = theme.iconColor);
    }

    themeToggle.addEventListener('click', function() {
        isDarkMode = !isDarkMode;
        const currentTheme = isDarkMode ? darkThemeColors : lightThemeColors;
        applyTheme(currentTheme);
        updateThemeIcon();
        localStorage.setItem('darkMode', isDarkMode);
        console.log('Tema alterado para:', isDarkMode ? 'escuro' : 'claro');
    });

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
});