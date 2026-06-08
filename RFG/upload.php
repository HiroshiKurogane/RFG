<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Upload - Random Food Generator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Random Food Generator</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
        aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
        <div class="navbar-nav">
          <a class="nav-link" href="index.php">Home</a>
          <a class="nav-link" href="generator.php">Generator</a>
          <a class="nav-link active" aria-current="page" href="upload.php">Upload Food</a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="text-center mb-5">
          <h1 class="display-5 fw-bold mb-3">Upload Food</h1>
          <p class="lead text-muted">Add new dishes to the food generator</p>
        </div>

        <div id="successMessage" style="display: none;" class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Success!</strong> <span id="successText"></span>
          <button type="button" class="btn-close" onclick="this.parentElement.style.display='none';"></button>
        </div>

        <div id="errorMessage" style="display: none;" class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error!</strong> <span id="errorText"></span>
          <button type="button" class="btn-close" onclick="this.parentElement.style.display='none';"></button>
        </div>

        <div class="card bg-dark border-primary shadow-lg">
          <div class="card-body p-4">
            <form id="foodForm">
              <div class="mb-3">
                <label for="title" class="form-label">Food Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control bg-secondary text-white border-primary" id="title" 
                  placeholder="e.g., Zöldborsós sertésragu" required>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control bg-secondary text-white border-primary" id="description" 
                  rows="4" placeholder="Describe the dish..." required></textarea>
              </div>

              <div class="mb-3">
                <label for="recipe_url" class="form-label">Recipe URL <span class="text-danger">*</span></label>
                <input type="url" class="form-control bg-secondary text-white border-primary" id="recipe_url" 
                  placeholder="https://example.com/recipe" required>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                  📤 Upload Food
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="mt-5">
          <h4 class="text-primary mb-3">Recent Foods</h4>
          <div id="foodsList" class="list-group">
            <div class="text-muted text-center py-3">Loading foods...</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let allFoods = [];

    window.addEventListener('DOMContentLoaded', function() {
      loadFoods();
      document.getElementById('foodForm').addEventListener('submit', handleSubmit);
    });

    async function loadFoods() {
      try {
        const response = await fetch('./server.php');
        if (!response.ok) throw new Error('Failed to load foods');
        allFoods = await response.json();
        displayFoods();
      } catch (error) {
        console.error('Error:', error);
        showError('Failed to load foods');
      }
    }

    function displayFoods() {
      const list = document.getElementById('foodsList');
      
      if (allFoods.length === 0) {
        list.innerHTML = '<div class="text-muted text-center py-3">No foods yet</div>';
        return;
      }

      list.innerHTML = allFoods.slice(-5).reverse().map(food => `
        <div class="list-group-item bg-secondary border-primary">
          <div class="d-flex w-100 justify-content-between">
            <h6 class="mb-1">${food.Title}</h6>
            <small class="text-muted">ID: ${food.Id}</small>
          </div>
          <p class="mb-1 small text-light">${food.Description.substring(0, 100)}...</p>
          <small class="text-info"><a href="${food.Recipe_URL}" target="_blank">View Recipe</a></small>
        </div>
      `).join('');
    }

    async function handleSubmit(e) {
      e.preventDefault();

      const title = document.getElementById('title').value;
      const description = document.getElementById('description').value;
      const recipe_url = document.getElementById('recipe_url').value;

      if (!title || !description || !recipe_url) {
        showError('Please fill in all fields');
        return;
      }

      const submitBtn = document.getElementById('submitBtn');
      submitBtn.disabled = true;
      submitBtn.textContent = '⏳ Uploading...';

      try {
        const response = await fetch('./add_food.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            title: title,
            description: description,
            recipe_url: recipe_url
          })
        });

        const result = await response.json();

        if (!response.ok || result.error) {
          showError(result.error || 'Failed to upload food');
        } else {
          showSuccess('Food uploaded successfully!');
          document.getElementById('foodForm').reset();
          loadFoods();
        }
      } catch (error) {
        console.error('Error:', error);
        showError('Failed to upload food. Please try again.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = '📤 Upload Food';
      }
    }

    function showSuccess(message) {
      document.getElementById('successText').textContent = message;
      document.getElementById('successMessage').style.display = 'block';
      document.getElementById('errorMessage').style.display = 'none';
    }

    function showError(message) {
      document.getElementById('errorText').textContent = message;
      document.getElementById('errorMessage').style.display = 'block';
      document.getElementById('successMessage').style.display = 'none';
    }
  </script>

  <style>
    .form-control:focus {
      border-color: #008BF8;
      box-shadow: 0 0 0 0.2rem rgba(0, 139, 248, 0.25);
    }

    .list-group-item {
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .list-group-item:hover {
      background-color: #0d47a1 !important;
      transform: translateX(5px);
    }
  </style>
</body>

</html>
