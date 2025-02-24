document.addEventListener('DOMContentLoaded', () => {
    fetch('/api/attachments')
        .then(response => response.json())
        .then(data => {
            console.log('Attachments data:', data);
            const attachmentsSection = document.getElementById('attachments');
            if (attachmentsSection) {
                if (data.length) {
                    attachmentsSection.innerHTML = data.map(attachment => 
                        `<p>${attachment.original_name}</p>`
                    ).join('');
                } else {
                    attachmentsSection.innerHTML = '<p>Brak załączników</p>';
                }
            }
        })
        .catch(error => console.error('Error fetching attachments:', error));
});
