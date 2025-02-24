document.addEventListener('DOMContentLoaded', () => {
    // Upewnij się, że zmienna projectId jest zdefiniowana w widoku
    if (typeof projectId === 'undefined') {
        console.error('projectId is not defined');
        return;
    }
    
    // Pobieramy spotkania dla danego projektu przy użyciu query string ?project_id=
    fetch('/api/meetings?project_id=' + projectId)
        .then(response => response.json())
        .then(data => {
            console.log('Meetings data:', data);
            const meetingsSection = document.getElementById('meetings');
            if (meetingsSection) {
                if (data.length) {
                    // Wyświetlamy informacje o spotkaniu: typ, datę i lokalizację
                    meetingsSection.innerHTML = data.map(meeting => 
                        `<p>${meeting.type} – ${meeting.date} – ${meeting.location}</p>`
                    ).join('');
                } else {
                    meetingsSection.innerHTML = '<p>Brak spotkań</p>';
                }
            }
        })
        .catch(error => console.error('Error fetching meetings:', error));
});
