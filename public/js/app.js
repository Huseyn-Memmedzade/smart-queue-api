document.addEventListener('DOMContentLoaded', () => {
    loadQueue();

    
    document.getElementById('addCustomerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const nameInput = document.getElementById('customerName');
        const name = nameInput.value.trim();

        if (!name) return;

        const res = await fetch('api/queue.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name })
        });

        if (res.ok) {
            nameInput.value = '';
            loadQueue();
        }
    });

    
    document.getElementById('nextBtn').addEventListener('click', async () => {
        const res = await fetch('api/next.php', { method: 'POST' });
        const data = await res.json();

        if (data.customer) {
            document.getElementById('currentServing').innerHTML = 
                `Hal-hazırda xidmət göstərilir: <span>#${data.customer.id} - ${data.customer.name}</span>`;
        } else {
            document.getElementById('currentServing').innerHTML = `<span>${data.message}</span>`;
        }
        loadQueue();
    });

    
    document.getElementById('checkBtn').addEventListener('click', async () => {
        const id = document.getElementById('checkId').value;
        const resultEl = document.getElementById('checkResult');

        if (!id) return;

        const res = await fetch(`api/queue.php?id=${id}`);
        if (!res.ok) {
            resultEl.textContent = 'Müştəri tapılmadı.';
            return;
        }

        const data = await res.json();
        if (data.customer.status === 'Waiting') {
            resultEl.textContent = `Müştəri #${data.customer.id} növbədə ${data.position}-ci sıradadır.`;
        } else {
            resultEl.textContent = `Müştərinin statusu: ${data.customer.status}`;
        }
    });
});


async function loadQueue() {
    const res = await fetch('api/queue.php');
    const queue = await res.json();

    const list = document.getElementById('queueList');
    list.innerHTML = '';

    queue.forEach(item => {
        const li = document.createElement('li');
        li.innerHTML = `
            <span>#${item.id} - <strong>${item.name}</strong> (${new Date(item.created_at).toLocaleTimeString()})</span>
            <button class="btn btn-danger" onclick="deleteCustomer(${item.id})">Sil</button>
        `;
        list.appendChild(li);
    });
}


async function deleteCustomer(id) {
    const res = await fetch(`api/queue.php?id=${id}`, { method: 'DELETE' });
    if (res.ok) {
        loadQueue();
    }
}