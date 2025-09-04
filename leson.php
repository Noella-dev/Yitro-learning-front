<!-- Formation 2 -->
            <div class="container mt-4">
              <div class="container-cards">
                <?php foreach ($cours as $coursItem): ?>
                <div class="training-card">
                  <div class="training-img">
                    <img src="<?= $coursItem['photo'] ? 'Uploads/cours/' . htmlspecialchars($coursItem['photo']) : 'assets/images/default_course.jpg' ?>" alt="Image cours">
                  </div>
                  <div class="training-content">
                    <h3><?= htmlspecialchars($coursItem['titre']) ?></h3>
                    <p><?= substr(htmlspecialchars($coursItem['description']), 0, 100) ?>...</p>
                    <a href="detail_cours.php?id=<?= $coursItem['id'] ?>" class="btn-learn">En savoir plus</a>
                  </div>
                </div>
               <?php endforeach; ?>
              </div>
            </div>