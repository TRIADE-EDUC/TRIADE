// Debounce helper
function debounce(fn, wait) {
  let t;
  return function(...args) {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), wait);
  };
}

function searchRequestV4(inputId, table, targetId, formId, champs) {
    const inputElem = document.getElementById(inputId);
    const resultatsDiv = document.getElementById(targetId);
    const formElem = document.getElementById(formId);

    if (!inputElem || !resultatsDiv) {
        console.error("Élément introuvable :", inputId, targetId);
        return;
    }

    inputElem.addEventListener('keyup', () => {
        const texte = inputElem.value.trim();

        if (texte.length < 2) {
            resultatsDiv.innerHTML = '';
            return;
        }

        const params = new URLSearchParams({
            input: texte,
            quoi: table,
            target: targetId,
            form: formId,
            champs: champs
        });

        fetch('librairie_php/rechercheV4.php?' + params.toString())
            .then(res => res.json())
            .then(data => {
//                console.log("Réponse JSON :", data); // pour vérifier
                resultatsDiv.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    resultatsDiv.textContent = 'Aucun résultat';
                    return;
                }

                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'result-item';
                    div.textContent = item; // ici ton "tintin"

		/*
		    const link = document.createElement('a');
                    link.href = lien + encodeURIComponent(item);
                    link.textContent = item;
                    link.style.textDecoration = 'none';
                    link.style.color = '#007bff';
		*/
	            div.style.cursor = 'pointer';
		    div.addEventListener('click', () => {
	                    inputElem.value = item;
        	            resultatsDiv.innerHTML = ''; // on vide la liste
                	    formElem.submit(); // soumission du formulaire
                    });
                    resultatsDiv.appendChild(div);

                });
            })
            .catch(err => {
                console.error('Erreur fetch :', err);
                resultatsDiv.textContent = 'Erreur de chargement';
            });
    });
}

