document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.user-table tbody tr').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
  });
  
  function openModal(action, type, id = null) {
    const modal = document.getElementById('userModal');
    const modalBody = document.getElementById('modalBody');
    
    let formContent = '';
    if (action === 'create') {
      formContent = `
        <h3>${type === 'formateurs' ? 'Ajouter un formateur' : type === 'apprenants' ? 'Ajouter un apprenant' : 'Ajouter un administrateur'}</h3>
        <form id="userForm" onsubmit="submitUser(event, '${type}')">
          <label>Nom: <input type="text" name="nom" required></label><br>
          <label>Email: <input type="email" name="email" required></label><br>
          <label>Mot de passe: <input type="password" name="mot_de_passe" ${action === 'create' ? 'required' : ''}></label><br>
          <button type="submit">Enregistrer</button>
        </form>`;
    } else if (action === 'view') {
      fetch(`../../admin/gestion_utilisateurs/user_actions.php?action=view&type=${type}&id=${id}`)
        .then(response => response.json())
        .then(data => {
          modalBody.innerHTML = `
            <h3>Détails de l'utilisateur</h3>
            <p><strong>ID:</strong> ${data.id}</p>
            <p><strong>Nom:</strong> ${data.nom || data.nom_prenom}</p>
            <p><strong>Email:</strong> ${data.email}</p>
            ${type === 'formateurs' ? `
              <p><strong>Téléphone:</strong> ${data.telephone || 'N/A'}</p>
              <p><strong>Ville/Pays:</strong> ${data.ville_pays || 'N/A'}</p>
              <p><strong>Statut:</strong> ${data.statut}</p>
            ` : ''}
            <button id="toggle-statut" onclick="toggleStatus('${type}', ${id})">${data.actif || data.statut !== 'supprime' ? 'Désactiver' : 'Activer'}</button>
          `;
        });
    }
    
    if (action !== 'view') {
      modalBody.innerHTML = formContent;
    }
    
    modal.style.display = 'flex';
  }
  
  function closeModal() {
    document.getElementById('userModal').style.display = 'none';
    document.getElementById('modalBody').innerHTML = '';
  }
  
  function submitUser(event, type) {
    event.preventDefault();
    const formData = new FormData(event.target);
    formData.append('action', 'create');
    formData.append('type', type);
    
    fetch('user_actions.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Utilisateur créé avec succès');
        location.reload();
      } else {
        alert('Erreur: ' + data.message);
      }
    });
  }
  
  function toggleStatus(type, id) {
    fetch(`user_actions.php?action=toggle&type=${type}&id=${id}`, { method: 'POST' })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Statut modifié avec succès');
          location.reload();
        } else {
          alert('Erreur: ' + data.message);
        }
      });
  }
  
  function deleteUser(type, id) {
    if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
      fetch(`user_actions.php?action=delete&type=${type}&id=${id}`, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Utilisateur supprimé avec succès');
            location.reload();
          } else {
            alert('Erreur: ' + data.message);
          }
        });
    }
  }