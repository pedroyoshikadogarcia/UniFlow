// main.js

const body = document.body;
const themeToggleBtn = document.getElementById('theme-toggle');
const sidebarLinks = document.querySelectorAll(".sidebar a");

// --- LÓGICA DE TEMA (GLOBAL) ---
// Carrega a preferência de tema imediatamente, sem esperar pelo evento DOMContentLoaded.
const currentTheme = localStorage.getItem('theme') || 'light';
if (currentTheme === 'dark') {
    body.classList.add('dark-theme');
}


// Atualiza o ícone do botão de tema
if (themeToggleBtn) {
    if (body.classList.contains('dark-theme')) {
        themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
    } else {
        themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
    }
}

// Listener para o botão de alternar tema
if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
        body.classList.toggle('dark-theme');
        if (body.classList.contains('dark-theme')) {
            localStorage.setItem('theme', 'dark');
            themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            localStorage.setItem('theme', 'light');
            themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
        }
        // Chama a função de atualização de botões do modal de aparência, se presente na página
        if (typeof updateAppearanceModalThemeButtons === 'function') {
            updateAppearanceModalThemeButtons();
        }
    });
}

// --- LÓGICA PARA ATIVAR O LINK DA SIDEBAR (GLOBAL) ---
// garante  que não tem links ativos
sidebarLinks.forEach(link => {
    link.classList.remove('active');
});

// --- LÓGICA PARA TAMANHO DA FONTE (GLOBAL) ---
function applyFontSize(size) {
    switch (size) {
        case 'small':
            body.style.fontSize = '14px';
            break;
        case 'medium':
            body.style.fontSize = '16px';
            break;
        case 'large':
            body.style.fontSize = '18px';
            break;
        default:
            body.style.fontSize = '16px';
    }
}

function loadFontSizePreference() {
    const savedFontSize = localStorage.getItem('fontSize') || 'medium';
    applyFontSize(savedFontSize);
    // Se o seletor de fonte existir (só em config.html), atualize seu valor
    const fontSizeSelector = document.getElementById('font-size-selector');
    if (fontSizeSelector) {
        fontSizeSelector.value = savedFontSize;
    }
}

// --- LÓGICA PARA ACESSIBILIDADE (GLOBAL) ---
function applyAccessibilityPreferences() {
    const savedHighContrast = localStorage.getItem('highContrast') === 'true';
    const savedDisableAnimations = localStorage.getItem('disableAnimations') === 'true';

    if (savedHighContrast) {
        body.classList.add('high-contrast');
    } else {
        body.classList.remove('high-contrast');
    }

    if (savedDisableAnimations) {
        body.classList.add('no-animations');
    } else {
        body.classList.remove('no-animations');
    }

    // Se os checkboxes existirem (só em config.html), atualize o estado deles
    const highContrastCheckbox = document.getElementById('high-contrast');
    const disableAnimationsCheckbox = document.getElementById('disable-animations');
    if (highContrastCheckbox) {
        highContrastCheckbox.checked = savedHighContrast;
    }
    if (disableAnimationsCheckbox) {
        disableAnimationsCheckbox.checked = savedDisableAnimations;
    }
}

// --- EXECUTA FUNÇÕES GLOBAIS ---
loadFontSizePreference(); // Aplica o tamanho da fonte
applyAccessibilityPreferences(); // Aplica as preferências de acessibilidade