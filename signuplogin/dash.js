let cardsData = [
    
];

// Selectors for columns and stats
const todoColumn = document.getElementById('todo-cards');
const inProgressColumn = document.getElementById('inprogress-cards');
const doneColumn = document.getElementById('done-cards');
const totalTasksElem = document.getElementById('total-tasks');
const completedTasksElem = document.getElementById('completed-tasks');
const completionPercentageElem = document.getElementById('completion-percentage');

// Instance of Chart.js
let kanbanChart = null;

// Function to create a card HTML element
function createCardElement(cardData) {
    const card = document.createElement('div');
    card.classList.add('card');
    card.dataset.id = cardData.id;
    card.innerHTML = `
        <h3>${cardData.title}</h3>
        <p><strong>Description :</strong> ${cardData.description}</p>
        <p><strong>Date d'échéance :</strong> ${cardData.dueDate}</p>
        <p><strong>Responsable :</strong> ${cardData.responsible}</p>
        <div class="actions">
            <button class="move-card" data-action="todo">À faire</button>
            <button class="move-card" data-action="inprogress">En cours</button>
            <button class="move-card" data-action="done">Terminé</button>
            <button class="delete-card">Supprimer</button>
        </div>
    `;
    return card;
}

// Function to add a card to the specified column
function addCardToColumn(columnId, cardData) {
    const column = document.getElementById(columnId);
    const card = createCardElement(cardData);
    column.appendChild(card);
}

// Function to render all cards in the appropriate columns
function renderCards() {
    todoColumn.innerHTML = '';
    inProgressColumn.innerHTML = '';
    doneColumn.innerHTML = '';
    
    cardsData.forEach(cardData => {
        addCardToColumn(cardData.status + '-cards', cardData);
    });
}

// Function to update stats and chart
function updateStatsAndChart() {
    const totalTasks = cardsData.length;
    const completedTasks = cardsData.filter(card => card.status === 'done').length;
    const completionPercentage = totalTasks ? (completedTasks / totalTasks * 100).toFixed(2) : 0;

    totalTasksElem.innerText = totalTasks;
    completedTasksElem.innerText = completedTasks;
    completionPercentageElem.innerText = `${completionPercentage}%`;

    // Update the chart
    if (kanbanChart) {
        kanbanChart.destroy();
    }

    const todoCount = todoColumn.childElementCount;
    const inProgressCount = inProgressColumn.childElementCount;
    const doneCount = doneColumn.childElementCount;

    const ctx = document.getElementById('kanban-chart').getContext('2d');
    kanbanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['À faire', 'En cours', 'Terminé'],
            datasets: [{
                label: 'Nombre de tâches',
                data: [todoCount, inProgressCount, doneCount],
                backgroundColor: ['#ff6384', '#36a2eb', '#4caf50']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Event listeners for adding and managing cards
document.querySelectorAll('.add-card').forEach(button => {
    button.addEventListener('click', () => {
        const title = prompt('Titre de la carte:');
        const description = prompt('Description de la carte:');
        const dueDate = prompt('Date d\'échéance (AAAA-MM-JJ):');
        const responsible = prompt('Responsable:');
        
        if (title && description && dueDate && responsible) {
            const newCard = {
                id: cardsData.length + 1,
                title,
                description,
                dueDate,
                responsible,
                status: 'todo'
            };
            cardsData.push(newCard);
            addCardToColumn('todo-cards', newCard);
            updateStatsAndChart();
        }
    });
});

// Event delegation for moving and deleting cards
document.addEventListener('click', event => {
    const target = event.target;
    if (target.classList.contains('move-card')) {
        const cardId = parseInt(target.closest('.card').dataset.id, 10);
        const newStatus = target.dataset.action;
        
        // Update card status in data
        const cardIndex = cardsData.findIndex(card => card.id === cardId);
        if (cardIndex !== -1) {
            cardsData[cardIndex].status = newStatus;
        }
        
        // Move card visually
        const cardElement = target.closest('.card');
        cardElement.parentNode.removeChild(cardElement);
        document.getElementById(`${newStatus}-cards`).appendChild(cardElement);
        
        // Update stats and chart
        updateStatsAndChart();
    }

    if (target.classList.contains('delete-card')) {
        const cardId = parseInt(target.closest('.card').dataset.id, 10);
        
        // Remove card from data
        cardsData = cardsData.filter(card => card.id !== cardId);
        
        // Remove card visually
        target.closest('.card').remove();
        
        // Update stats and chart
        updateStatsAndChart();
    }
});

// Initial render
renderCards();
updateStatsAndChart();

// Event listener for the deconnexion button
document.getElementById('logout-button').addEventListener('click', handleLogout);

function handleLogout() {
    // Perform logout actions here
    // For example, clear user data, redirect to login page, etc.
    alert('Êtes-vous sûr de vouloir vous déconnecter?');
    // Redirect to homepage
    window.location.href = 'index.php'; // Change this URL to your homepage URL
}