function normalizeStep(step) {
    return step.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/\s+/g, '-');
}

function initKanban(candidates = candidatesData) {
    document.querySelectorAll('.kanban-column').forEach(column => {
        column.innerHTML = '';
    });
    const counters = {
        'Candidato': 0,
        'Análise Inicial': 0,
        'Teste de Perfil': 0,
        'Entrevista': 0,
        'Aprovado': 0
    };
    candidates.forEach((candidate, index) => {
        const step = candidate.person.step || 'Candidato';
        const column = document.querySelector(`.kanban-column[data-step="${step}"]`);
        if (column) {
            counters[step]++;
            const card = document.createElement('div');
            card.className = 'kanban-card'; // Removida a classe mb-2 para o novo estilo
            card.dataset.index = index;
            card.dataset.cpf = candidate.person.cpf;
            const creationDate = new Date(candidate.recruitment.creation_date);
            const daysSinceCreation = Math.floor((new Date() - creationDate) / (1000 * 60 * 60 * 24));
            
            let starsHtml = '';
            const stars = candidate.person.stars || 0;
            for (let i = 0; i < 3; i++) {
                starsHtml += `<i class="ti ti-star${i < stars ? '-filled' : ''}" style="color: #F59E0B;"></i>`;
            }

            card.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
<h6 class="mb-0">${candidate.person.full_name || 'N/A'}
${candidate.person.linkedin_url ? `<a href="${candidate.person.linkedin_url}" target="_blank" class="ms-1 text-primary" title="LinkedIn"><i class="ti ti-brand-linkedin fs-5"></i></a>` : ''}
</h6>
<div class="d-flex align-items-center text-muted small">
<i class="ti ti-clock me-1"></i>
<span>${daysSinceCreation} dias</span>
</div>
</div>
<div class="progress-steps-small my-2" data-current-step="${candidate.person.step || 'Candidato'}">
<div class="step-item-small" data-step="Candidato"></div>
<div class="step-item-small" data-step="Análise Inicial"></div>
<div class="step-item-small" data-step="Teste de Perfil"></div>
<div class="step-item-small" data-step="Entrevista"></div>
<div class="step-item-small" data-step="Aprovado"></div>
</div>
<div class="d-flex justify-content-between align-items-center">
<div class="kanban-stars">${starsHtml}</div>
<div class="kanban-icons-bottom">
<a href="#" class="me-2 text-primary view-candidate-btn" data-index="${index}" title="Ver detalhes">
<i class="ti ti-users fs-5"></i>
</a>
<a href="#" class="text-secondary curriculum-btn" data-index="${index}" title="Ver Currículo">
<i class="ti ti-file-text fs-5"></i>
</a>
</div>
</div>
            `;
            column.appendChild(card);
        }
    });
    for (const step in counters) {
        const countElement = document.getElementById(`${normalizeStep(step)}-count`);
        if (countElement) {
            countElement.textContent = counters[step];
        }
    }
    $('.kanban-column').sortable({
        connectWith: '.kanban-column',
        placeholder: 'card-placeholder',
        cursor: 'grab',
        revert: true,
        start: function(event, ui) {
            ui.item.addClass('dragging');
            ui.item.data('originColumn', this);
        },
        stop: function(event, ui) {
            ui.item.removeClass('dragging');
        },
        receive: function(event, ui) {
            const newStep = $(this).data('step');
            const candidateIndex = ui.item.data('index');
            const candidateCpf = ui.item.data('cpf');
            updateCandidateStep(candidateCpf, newStep, function(success) {
                if (success) {
                    if (candidateIndex !== undefined && filteredCandidates[candidateIndex]) {
                        filteredCandidates[candidateIndex].person.step = newStep;
                    }
                    updateKanbanCounters();
                    if (document.getElementById('candidateModal').classList.contains('show')) {
                        fillCandidateModal(currentCandidateIndex);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Não foi possível atualizar a etapa do candidato.',
                    });
                    const originColumn = ui.item.data('originColumn');
                    if (originColumn) {
                        $(originColumn).append(ui.item);
                    }
                    updateKanbanCounters();
                }
            });
        }
    }).disableSelection();
}

function updateKanbanCounters() {
    const counters = {
        'Candidato': 0,
        'Análise Inicial': 0,
        'Teste de Perfil': 0,
        'Entrevista': 0,
        'Aprovado': 0
    };
    document.querySelectorAll('.kanban-column').forEach(column => {
        const step = column.dataset.step;
        const count = column.querySelectorAll('.kanban-card').length;
        counters[step] = count;
    });
    for (const step in counters) {
        const countElement = document.getElementById(`${normalizeStep(step)}-count`);
        if (countElement) {
            countElement.textContent = counters[step];
        }
    }
}