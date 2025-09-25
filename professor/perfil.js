document.addEventListener('DOMContentLoaded', () => {
    // Lógica para o Tema Escuro/Claro
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        // Carrega o tema salvo no localStorage ou define como claro por padrão
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.body.classList.add(currentTheme + '-theme');

        // Atualiza o ícone do botão de tema
        function updateThemeIcon() {
            if (document.body.classList.contains('dark-theme')) {
                themeToggle.innerHTML = '<i class="fas fa-sun"></i>'; // Sol se estiver escuro
            } else {
                themeToggle.innerHTML = '<i class="fas fa-moon"></i>'; // Lua se estiver claro
            }
        }
        updateThemeIcon(); // Chama na carga da página

        themeToggle.addEventListener('click', () => {
            if (document.body.classList.contains('dark-theme')) {
                document.body.classList.remove('dark-theme');
                document.body.classList.add('light-theme');
                localStorage.setItem('theme', 'light');
            } else {
                document.body.classList.remove('light-theme');
                document.body.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeIcon(); // Atualiza o ícone após a troca
        });
    }

    // Lógica para o Modal de Edição de Perfil
    const openEditProfileBtn = document.getElementById('openEditProfile');
    const editProfileModal = document.getElementById('editProfileModal');
    const closeButton = editProfileModal ? editProfileModal.querySelector('.close-button') : null;
    const editProfileForm = document.getElementById('editProfileForm');
    const profilePicInput = document.getElementById('profileNewPic');
    const profilePics = document.querySelectorAll('.profile-pic, .id-card-pic'); // Seleciona todas as imagens de perfil e crachá

    if (openEditProfileBtn) {
        openEditProfileBtn.addEventListener('click', () => {
            editProfileModal.style.display = 'flex'; // Altera para 'flex' para centralizar com CSS
        });
    }

    if (closeButton) {
        closeButton.addEventListener('click', () => {
            editProfileModal.style.display = 'none';
        });
    }

    // Fecha o modal se clicar fora da área do conteúdo
    if (editProfileModal) {
        window.addEventListener('click', (event) => {
            if (event.target === editProfileModal) {
                editProfileModal.style.display = 'none';
            }
        });
    }

    if (editProfileForm) {
        editProfileForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Impede o envio do formulário padrão

            // AQUI ESTÁ A MUDANÇA PRINCIPAL:
            // Agora, você só vai pegar os valores dos campos que são editáveis,
            // que são "Sobre Mim" e "Links Úteis" e a "Foto de Perfil".
            const newBio = document.getElementById('profileBio').value;
            const newLinks = document.getElementById('profileLinks').value;

            // Para uma aplicação real, você enviaria newBio, newLinks e profilePicInput.files[0]
            // para um servidor aqui (via AJAX/Fetch API).

            // Lógica para pré-visualizar e aplicar nova foto
            if (profilePicInput && profilePicInput.files && profilePicInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePics.forEach(img => {
                        img.src = e.target.result;
                    });
                };
                reader.readAsDataURL(profilePicInput.files[0]);
            }

            // Exemplo de como você atualizaria o "Sobre Mim" e "Links"
            // Se você tiver elementos na sua página principal para exibir essas informações,
            // você precisaria de IDs ou classes para acessá-los.
            // Por exemplo, se tivesse um <p id="bio-display"> no perfil principal:
            // document.getElementById('bio-display').textContent = newBio;
            // E para links, algo similar.

            // Feedback ao usuário
            alert('Suas informações de "Sobre Mim", "Links Úteis" e a foto de perfil foram atualizadas com sucesso!');
            editProfileModal.style.display = 'none'; // Fecha o modal
        });
    }
});