document.addEventListener('DOMContentLoaded', () => {
    // Upewnij się, że zmienna projectId jest zdefiniowana w widoku
    if (typeof projectId === 'undefined') {
        console.error('projectId is not defined');
        return;
    }
    
    fetch('/api/questions?project_id=' + projectId)
        .then(response => response.json())
        .then(data => {
            console.log('Questions data:', data);
            const questionsSection = document.getElementById('questions');
            if (questionsSection) {
                if (data.length) {
                    questionsSection.innerHTML = data.map(question => 
                        `<p>${question.title}</p>`
                    ).join('');
                } else {
                    questionsSection.innerHTML = '<p>Brak pytań</p>';
                }
            }
        })
        .catch(error => console.error('Error fetching questions:', error));
});
