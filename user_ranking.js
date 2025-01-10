document.addEventListener('DOMContentLoaded', () => {
    const userFilters = document.querySelectorAll('.filter-button[data-filter]');
    const usersList = document.getElementById('users-list');

    userFilters.forEach(filterButton => {
        filterButton.addEventListener('click', () => {
            const filter = filterButton.getAttribute('data-filter');
            userFilters.forEach(btn => btn.classList.remove('active'));
            filterButton.classList.add('active');

            fetch(`get_users.php?filter=${filter}`)
                .then(response => response.json())
                .then(data => {
                    usersList.innerHTML = '';
                    data.forEach(user => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <img src="${user.avatar}" alt="${user.username}" class="user-avatar" />
                            <div class="ranking-info">
                                <span class="username">${user.username}</span>
                            </div>
                        `;
                        usersList.appendChild(li);
                    });
                })
                .catch(error => console.error('Error fetching users:', error));
        });
    });
});