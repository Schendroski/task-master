// TaskViewModel.js - O cérebro do Front-end
class TaskViewModel {
    constructor() {
        this.tasks = []; // O "Estado" da aplicação
        this.init();
    }

    async init() {
        // Mapeia elementos da View
        this.listElement = document.getElementById('taskList');
        this.titleInput = document.getElementById('taskTitle');
        this.descriptionInput = document.getElementById('taskDescription');
        this.dueDateInput = document.getElementById('taskDueDate');
        this.errorElement = document.getElementById('errorMessage');
        this.addBtn = document.getElementById('addBtn');

        // Eventos da View que disparam ações na ViewModel
        this.addBtn.addEventListener('click', () => this.addTask());
        this.listElement.addEventListener('click', (event) => {
            const btn = event.target.closest('button[data-action]');
            if (!btn) return;
            const id = Number(btn.dataset.id);
            if (btn.dataset.action === 'complete') this.completeTask(id);
            if (btn.dataset.action === 'delete') this.deleteTask(id);
        });

        // Busca os dados iniciais
        await this.fetchTasks();
    }

    // Chamada genérica à API: lança erro se a resposta não for OK
    async callApi(url, options = {}) {
        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || 'Erro ao falar com a API.');
        }
        return data;
    }

    showError(message) {
        this.errorElement.textContent = message;
        this.errorElement.hidden = false;
    }

    clearError() {
        this.errorElement.hidden = true;
    }

    async fetchTasks() {
        try {
            this.tasks = await this.callApi('api.php?action=list');
            this.clearError();
            this.render(); // Atualiza a View (Data Binding)
        } catch (error) {
            this.showError(error.message);
        }
    }

    async addTask() {
        try {
            await this.callApi('api.php?action=create', {
                method: 'POST',
                body: JSON.stringify({
                    title: this.titleInput.value,
                    description: this.descriptionInput.value,
                    due_date: this.dueDateInput.value
                })
            });
            this.titleInput.value = '';
            this.descriptionInput.value = '';
            this.dueDateInput.value = '';
            await this.fetchTasks();
        } catch (error) {
            this.showError(error.message);
        }
    }

    async completeTask(id) {
        try {
            await this.callApi(`api.php?action=complete&id=${id}`);
            await this.fetchTasks();
        } catch (error) {
            this.showError(error.message);
        }
    }

    async deleteTask(id) {
        if (!confirm('Tem certeza que deseja excluir esta tarefa?')) return;
        try {
            await this.callApi(`api.php?action=delete&id=${id}`);
            await this.fetchTasks();
        } catch (error) {
            this.showError(error.message);
        }
    }

    // Formatação de apresentação (antes ficava no Presenter): 2026-12-31 -> 31/12/2026
    formatDate(isoDate) {
        if (!isoDate) return 'Sem data';
        const [year, month, day] = isoDate.split('-');
        return `${day}/${month}/${year}`;
    }

    // O "Binder": sincroniza o array de tarefas com o HTML
    render() {
        this.listElement.innerHTML = '';
        this.tasks.forEach((task) => {
            const li = document.createElement('li');
            li.className = task.done ? 'done' : '';

            const info = document.createElement('div');

            const title = document.createElement('div');
            title.className = 'title';
            title.textContent = task.title.toUpperCase(); // textContent evita injeção de HTML
            info.appendChild(title);

            if (task.description) {
                const description = document.createElement('div');
                description.className = 'description';
                description.textContent = task.description;
                info.appendChild(description);
            }

            const meta = document.createElement('div');
            meta.className = 'meta';
            meta.textContent = 'Vence em: ' + this.formatDate(task.due_date);
            info.appendChild(meta);

            const actions = document.createElement('div');
            actions.className = 'actions';
            if (!task.done) {
                actions.innerHTML += `<button data-action="complete" data-id="${Number(task.id)}" title="Concluir">✅</button>`;
            }
            actions.innerHTML += `<button data-action="delete" data-id="${Number(task.id)}" title="Excluir">❌</button>`;

            li.appendChild(info);
            li.appendChild(actions);
            this.listElement.appendChild(li);
        });
    }
}

// Instancia a ViewModel
const vm = new TaskViewModel();