document.addEventListener("DOMContentLoaded", function () {
    const settingItems = document.querySelectorAll(".setting-item");
    const modals = document.querySelectorAll(".modal");
    const overlay = document.getElementById('modal-overlay');
    const closeButtons = document.querySelectorAll(".modal .close-button");

    // Lógica para abrir qualquer modal
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Garante que o modal está escondido antes de adicionar o overlay
            modal.style.display = 'none';
            overlay.style.display = 'block';

            // Pequeno delay para garantir que o navegador entenda a mudança de estado
            setTimeout(() => {
                overlay.classList.add('active');
                modal.style.display = 'block';
                setTimeout(() => {
                    modal.classList.add('active');
                }, 10);
            }, 10);
        }
    }

    // Lógica para fechar qualquer modal
    function closeModal() {
        const activeModal = document.querySelector('.modal.active');
        if (activeModal) {
            activeModal.classList.remove('active');
            overlay.classList.remove('active');
            
            // Espera a animação de fadeOut terminar antes de esconder o elemento
            activeModal.addEventListener('transitionend', () => {
                activeModal.style.display = 'none';
                overlay.style.display = 'none';
            }, { once: true });
        }
    }
    
    // Adiciona evento de clique para os itens da lista
    settingItems.forEach(item => {
        item.addEventListener("click", () => {
            const modalId = item.getAttribute("data-modal-id");
            openModal(modalId);
        });
    });

    // Adiciona evento de clique para os botões de fechar de cada modal
    closeButtons.forEach(button => {
        button.addEventListener("click", closeModal);
    });

    // Adiciona evento de clique no overlay para fechar o modal
    if (overlay) {
        overlay.addEventListener("click", (event) => {
            if (event.target === overlay) {
                closeModal();
            }
        });
    }

    // Lógica para os sub-modais de Alterar Senha e Excluir Conta
    const changePasswordButton = document.getElementById('change-password-btn');
    if (changePasswordButton) {
        changePasswordButton.addEventListener('click', (event) => {
            event.stopPropagation();
            closeModal();
            setTimeout(() => openModal('changePasswordModal'), 100);
        });
    }

    const deleteAccountButton = document.getElementById('delete-account-btn');
    if (deleteAccountButton) {
        deleteAccountButton.addEventListener('click', (event) => {
            event.stopPropagation();
            closeModal();
            setTimeout(() => openModal('confirmDeleteAccountModal'), 100);
        });
    }

    // O restante da sua lógica para tema, fonte, etc., foi mantida
    const body = document.body;
    const themeToggleBtn = document.getElementById('theme-toggle');
    const lightModeBtn = document.getElementById('light-mode-btn');
    const darkModeBtn = document.getElementById('dark-mode-btn');
    const fontSizeSelector = document.getElementById('font-size-selector');
    const languageSelector = document.getElementById('language-selector');
    const highContrastCheckbox = document.getElementById('high-contrast');
    const disableAnimationsCheckbox = document.getElementById('disable-animations');

    function toggleTheme() {
        const isDark = body.classList.toggle('dark-theme');
        const theme = isDark ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        if (themeToggleBtn) themeToggleBtn.innerHTML = isDark ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
        updateAppearanceModalThemeButtons();
    }

    function updateAppearanceModalThemeButtons() {
        const isDark = body.classList.contains('dark-theme');
        if (lightModeBtn && darkModeBtn) {
            lightModeBtn.classList.toggle('active-theme', !isDark);
            darkModeBtn.classList.toggle('active-theme', isDark);
        }
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', toggleTheme);
    }
    
    if (lightModeBtn) {
        lightModeBtn.addEventListener('click', () => {
            body.classList.remove('dark-theme');
            localStorage.setItem('theme', 'light');
            if (themeToggleBtn) themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            updateAppearanceModalThemeButtons();
        });
    }

    if (darkModeBtn) {
        darkModeBtn.addEventListener('click', () => {
            body.classList.add('dark-theme');
            localStorage.setItem('theme', 'dark');
            if (themeToggleBtn) themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
            updateAppearanceModalThemeButtons();
        });
    }
    
    if (fontSizeSelector) {
        fontSizeSelector.addEventListener('change', (event) => {
            const selectedSize = event.target.value;
            localStorage.setItem('fontSize', selectedSize);
            body.style.fontSize = selectedSize === 'small' ? '0.875em' : (selectedSize === 'large' ? '1.125em' : '1em');
        });
    }

    if (languageSelector) {
        languageSelector.addEventListener('change', (event) => {
            const selectedLang = event.target.value;
            localStorage.setItem('language', selectedLang);
        });
        const savedLanguage = localStorage.getItem('language') || 'pt-br';
        languageSelector.value = savedLanguage;
    }

    function handleHighContrastChange() {
        const isChecked = highContrastCheckbox.checked;
        localStorage.setItem('highContrast', isChecked);
        body.classList.toggle('high-contrast', isChecked);
    }

    function handleAnimationsChange() {
        const isChecked = disableAnimationsCheckbox.checked;
        localStorage.setItem('disableAnimations', isChecked);
        body.classList.toggle('no-animations', isChecked);
    }

    if (highContrastCheckbox) {
        highContrastCheckbox.addEventListener('change', handleHighContrastChange);
        const savedHighContrast = localStorage.getItem('highContrast') === 'true';
        highContrastCheckbox.checked = savedHighContrast;
        if (savedHighContrast) body.classList.add('high-contrast');
    }

    if (disableAnimationsCheckbox) {
        disableAnimationsCheckbox.addEventListener('change', handleAnimationsChange);
        const savedDisableAnimations = localStorage.getItem('disableAnimations') === 'true';
        disableAnimationsCheckbox.checked = savedDisableAnimations;
        if (savedDisableAnimations) body.classList.add('no-animations');
    }

    const savedFontSize = localStorage.getItem('fontSize');
    if (savedFontSize) {
        fontSizeSelector.value = savedFontSize;
        body.style.fontSize = savedFontSize === 'small' ? '0.875em' : (savedFontSize === 'large' ? '1.125em' : '1em');
    }
    
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        body.classList.add('dark-theme');
        if (themeToggleBtn) themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
    } else {
        body.classList.remove('dark-theme');
        if (themeToggleBtn) themeToggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
    }
    updateAppearanceModalThemeButtons();

    const savePasswordBtn = document.getElementById('save-password-btn');
    const currentPasswordInput = document.getElementById('current-password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmNewPasswordInput = document.getElementById('confirm-new-password');
    const passwordMessage = document.getElementById('password-message');

    function showPasswordMessage(message, type) {
        passwordMessage.textContent = message;
        passwordMessage.classList.remove('hidden', 'success', 'error');
        passwordMessage.classList.add(type);
    }

    if (savePasswordBtn) {
        savePasswordBtn.addEventListener('click', () => {
            const currentPassword = currentPasswordInput.value;
            const newPassword = newPasswordInput.value;
            const confirmNewPassword = confirmNewPasswordInput.value;
            
            if (!currentPassword || !newPassword || !confirmNewPassword) {
                showPasswordMessage('Por favor, preencha todos os campos.', 'error');
                return;
            }
            if (newPassword !== confirmNewPassword) {
                showPasswordMessage('A nova senha e a confirmação não coincidem.', 'error');
                return;
            }

            setTimeout(() => {
                const isSuccess = Math.random() > 0.3;
                if (isSuccess) {
                    showPasswordMessage('Senha alterada com sucesso!', 'success');
                    setTimeout(() => closeModal(), 1500);
                } else {
                    showPasswordMessage('Erro ao alterar a senha. Senha atual incorreta ou problema no servidor.', 'error');
                }
            }, 1000);
        });
    }

    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            console.log('Funcionalidade de Excluir Conta será implementada aqui!');
            closeModal();
        });
    }
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', () => {
            closeModal();
        });
    }
});