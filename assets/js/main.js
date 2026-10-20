document.addEventListener('DOMContentLoaded', function() {
    const projectMenu = document.querySelector('.project-menu');
    if (projectMenu) {
        const menuToggle = projectMenu.querySelector('.project-menu-toggle');
        const menuPanel = projectMenu.querySelector('.project-menu-panel');

        const setMenuOpen = function(isOpen) {
            menuToggle.setAttribute('aria-expanded', String(isOpen));
            menuToggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
            menuPanel.hidden = !isOpen;
            projectMenu.classList.toggle('is-open', isOpen);
        };

        menuToggle.addEventListener('click', function() {
            setMenuOpen(menuToggle.getAttribute('aria-expanded') !== 'true');
        });
        document.addEventListener('click', function(event) {
            if (!projectMenu.contains(event.target)) {
                setMenuOpen(false);
            }
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                setMenuOpen(false);
                menuToggle.focus();
            }
        });
        menuPanel.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                setMenuOpen(false);
            });
        });
    }

    // Live Search Filter Project
    const searchInput = document.getElementById('searchInput');
    const projectCards = document.querySelectorAll('.project-item');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();

            projectCards.forEach(card => {
                const title = card.getAttribute('data-title') ? card.getAttribute('data-title').toLowerCase() : '';
                const author = card.getAttribute('data-author') ? card.getAttribute('data-author').toLowerCase() : '';

                if (title.includes(query) || author.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    const projectDetailModal = document.getElementById('projectDetailModal');
    if (projectDetailModal) {
        const detailTitle = document.getElementById('projectDetailTitle');
        const detailDescription = document.getElementById('projectDetailDescription');
        const detailImage = document.getElementById('projectDetailImage');
        const detailAuthor = document.getElementById('projectDetailAuthor');
        const detailAuthorImage = document.getElementById('projectDetailAuthorImage');
        const detailRepository = document.getElementById('projectDetailRepository');
        const detailDemo = document.getElementById('projectDetailDemo');
        const detailLanguage = document.getElementById('projectDetailLanguage');

        const showProjectDetails = function(trigger) {
            detailTitle.textContent = trigger.dataset.title || '';
            detailDescription.textContent = trigger.dataset.description || '';
            detailImage.src = trigger.dataset.image || '';
            detailImage.alt = trigger.dataset.title || '';
            detailImage.onerror = function() {
                this.src = 'https://via.placeholder.com/800x450';
            };
            detailAuthor.textContent = trigger.dataset.author || 'Pengguna Dihapus';
            detailAuthorImage.src = trigger.dataset.authorImage || 'uploads/default.png';
            detailAuthorImage.onerror = function() {
                this.src = 'https://via.placeholder.com/100';
            };
            detailRepository.href = trigger.dataset.repository || '#';
            detailRepository.classList.toggle('d-none', !trigger.dataset.repository);
            detailDemo.href = trigger.dataset.demo || '#';
            detailDemo.classList.toggle('d-none', !trigger.dataset.demo);
            detailLanguage.replaceChildren();
            const technologies = (trigger.dataset.language || 'Belum diisi')
                .split(',')
                .map(technology => technology.trim())
                .filter(Boolean);
            technologies.forEach(technology => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-light text-dark border rounded-pill';
                badge.textContent = technology;
                detailLanguage.appendChild(badge);
            });
            projectDetailModal.classList.add('project-modal-open');
            projectDetailModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('project-modal-body-open');
        };

        document.querySelectorAll('.project-detail-trigger').forEach(button => {
            button.addEventListener('click', function() {
                showProjectDetails(button);
            });
        });

        const hideProjectDetails = function() {
            projectDetailModal.classList.remove('project-modal-open');
            projectDetailModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('project-modal-body-open');
        };

        projectDetailModal.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', hideProjectDetails);
        });
        projectDetailModal.addEventListener('click', function(event) {
            if (event.target === projectDetailModal) {
                hideProjectDetails();
            }
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && projectDetailModal.classList.contains('project-modal-open')) {
                hideProjectDetails();
            }
        });
    }

    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const input = document.getElementById(button.getAttribute('aria-controls'));
            if (!input) {
                return;
            }

            const isVisible = input.type === 'password';
            input.type = isVisible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(isVisible));
            button.setAttribute('aria-label', isVisible ? 'Sembunyikan sandi' : 'Lihat sandi');
            button.innerHTML = `<i class="bi ${isVisible ? 'bi-eye-slash' : 'bi-eye'}" aria-hidden="true"></i>`;
        });
    });

    // Instant Preview Upload Image
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const targetPreview = document.querySelector('.image-preview-target');
                    if (targetPreview) {
                        targetPreview.src = e.target.result;
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    });
});
