<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Generator - Random Food Generator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
  <!-- Navigation Bar -->
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
          <a class="nav-link active" aria-current="page" href="generator.php">Generator</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <!-- Header -->
        <div class="text-center mb-5">
          <h1 class="display-4 fw-bold mb-3">Food Generator</h1>
          <p class="lead text-muted">Can't decide what to eat? Click the button below to discover a random food!</p>
        </div>

        <!-- Generator Card -->
        <div class="card bg-dark border-primary shadow-lg mb-5">
          <div class="card-body p-5 text-center">
            <!-- Display Area -->
            <div id="foodDisplay" class="mb-4">
              <div class="placeholder-section">
                <i class="fas fa-utensils display-1 text-primary mb-3" style="font-size: 4rem;">🍽️</i>
                <p class="text-muted">Click "Generate" to get started!</p>
              </div>
            </div>



            <!-- Loading Spinner -->
            <div id="loadingSpinner" style="display: none;" class="mb-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="text-muted mt-2">Generating a delicious surprise...</p>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" style="display: none;" class="alert alert-danger mb-4" role="alert">
              <p id="errorText" class="mb-0"></p>
            </div>

            <!-- Buttons -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
              <button id="generateBtn" class="btn btn-primary btn-lg px-5" onclick="generateFood()">
                🎲 Generate Food
              </button>
              <button id="anotherBtn" class="btn btn-outline-primary btn-lg px-5" onclick="generateFood()" style="display: none;">
                Another One!
              </button>
              <button id="resetBtn" class="btn btn-secondary btn-lg px-5" onclick="resetGenerator()" style="display: none;">
                Reset
              </button>
            </div>
          </div>
        </div>
  <script>
    let allFoods = [];

    // Fetch all foods on page load
    window.addEventListener('DOMContentLoaded', function() {
      fetchAllFoods();
    });

    // Fetch all foods from server
    async function fetchAllFoods() {
      try {
        const response = await fetch('./server.php');
        if (!response.ok) throw new Error('Failed to fetch foods');
        allFoods = await response.json();
        console.log('Foods loaded:', allFoods.length);
      } catch (error) {
        console.error('Error fetching foods:', error);
        showError('Failed to load foods. Please refresh the page.');
      }
    }

    // Generate random food
    function generateFood() {
      if (allFoods.length === 0) {
        showError('No foods available. Please refresh the page.');
        return;
      }

      // Show loading state
      document.getElementById('loadingSpinner').style.display = 'block';
      document.getElementById('generateBtn').disabled = true;
      document.getElementById('anotherBtn').disabled = true;
      document.getElementById('errorMessage').style.display = 'none';

      // Simulate API delay for effect
      setTimeout(() => {
        const randomIndex = Math.floor(Math.random() * allFoods.length);
        const food = allFoods[randomIndex];

        // Display the food
        displayFood(food);

        // Hide loading state
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('generateBtn').disabled = false;
        document.getElementById('anotherBtn').disabled = false;
      }, 800);
    }

    // Display food details
    function displayFood(food) {
      const recipeLink = food.Recipe_URL ? `<a href="${food.Recipe_URL}" target="_blank" class="btn btn-info btn-sm mt-3">🔗 View Recipe</a>` : '';
      
      document.getElementById('foodDisplay').innerHTML = `
        <div>
          <h2 class="text-primary mb-3">${food.Title || 'Unknown Food'}</h2>
          <div class="food-info">
            <p class="text-light mb-3">${food.Description || 'A delicious dish to enjoy.'}</p>
            ${recipeLink}
          </div>
        </div>
      `;

      // Show/hide buttons
      document.getElementById('generateBtn').style.display = 'none';
      document.getElementById('anotherBtn').style.display = 'inline-block';
      document.getElementById('resetBtn').style.display = 'inline-block';
    }

    // Reset generator
    function resetGenerator() {
      document.getElementById('foodDetails').style.display = 'none';
      document.getElementById('foodDisplay').innerHTML = `
        <div class="placeholder-section">
          <i class="fas fa-utensils display-1 text-primary mb-3" style="font-size: 4rem;">🍽️</i>
          <p class="text-muted">Click "Generate" to get started!</p>
        </div>
      `;

      document.getElementById('generateBtn').style.display = 'inline-block';
      document.getElementById('anotherBtn').style.display = 'none';
      document.getElementById('resetBtn').style.display = 'none';
      document.getElementById('errorMessage').style.display = 'none';
    }

    // Show error message
    function showError(message) {
      document.getElementById('errorText').textContent = message;
      document.getElementById('errorMessage').style.display = 'block';
      document.getElementById('loadingSpinner').style.display = 'none';
      document.getElementById('generateBtn').disabled = false;
      document.getElementById('anotherBtn').disabled = false;
    }
  </script>

  <style>
    .feature-card {
      background: rgba(0, 139, 248, 0.1);
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .feature-card:hover {
      background: rgba(0, 139, 248, 0.2);
      transform: translateY(-5px);
    }

    .info-box {
      background: rgba(255, 255, 255, 0.05);
      padding: 15px;
      border-radius: 8px;
      border: 1px solid rgba(0, 139, 248, 0.3);
    }

    #foodDisplay {
      min-height: 150px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .placeholder-section {
      text-align: center;
    }

    .btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
  </style>
</body>

</html>
