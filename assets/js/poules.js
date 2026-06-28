function genererPoules() {
    const nb = parseInt(document.getElementById('nb_poules').value);
    const container = document.getElementById('poules-container');
    container.innerHTML = '';
 
    for (let i = 0; i < nb; i++) {
        const pouleName = String.fromCharCode(65 + i);
        let html = `<div class="poule-block">
            <h3>Poule ${pouleName}</h3>
            <div class="teams-checkboxes">`;
        TEAMS_DATA.forEach(t => {
            html += `<label class="member-option">
                <input type="checkbox" name="poule_${pouleName}[]" value="${t.id}" class="poule-cb" data-poule="${pouleName}">
                ${t.name}
            </label>`;
        });
        html += `</div></div>`;
        container.innerHTML += html;
    }
 
    document.getElementById('btn-submit').style.display = 'inline-block';
}
 