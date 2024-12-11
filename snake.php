<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Oyunu - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col antialiased">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-2xl font-bold">ADMIN PANEL</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="search" placeholder="Ara" class="bg-blue-700 text-white placeholder-blue-200 rounded-full py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <i class="fas fa-search absolute right-3 top-2.5 text-blue-200"></i>
                    </div>
                    <span class="font-medium"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="bg-blue-700 hover:bg-blue-600 rounded-full py-2 px-4 transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Çıkış
                    </a>
                </div>
            </div>
        </header>

        <div class="flex-1 flex">
            <!-- Sidebar -->
            <aside class="bg-gray-800 text-white w-64 flex-shrink-0 hidden md:block">
                <nav class="mt-5 px-2">
                    <a href="dashboard.php" class="group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
                        <i class="fas fa-home mr-4 h-6 w-6"></i>
                        Dashboard
                    </a>
                    <a href="users.php" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
                        <i class="fas fa-users mr-4 h-6 w-6"></i>
                        Kullanıcılar
                    </a>
                    <a href="snake.php" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-white bg-gray-900 focus:outline-none focus:bg-gray-700 transition ease-in-out duration-150">
                        <i class="fas fa-gamepad mr-4 h-6 w-6"></i>
                        Snake Oyunu
                    </a>
                    <a href="#" class="mt-1 group flex items-center px-2 py-2 text-base leading-6 font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
                        <i class="fas fa-cog mr-4 h-6 w-6"></i>
                        Ayarlar
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-gray-700 text-3xl font-medium">Snake Oyunu</h3>
                        <div class="flex space-x-4">
                            <div class="text-2xl">Seviye: <span id="level">1</span></div>
                            <div class="text-2xl">Skor: <span id="score">0</span></div>
                            <div class="text-2xl">En Yüksek Skor: <span id="highScore">0</span></div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <div class="flex justify-center mb-4">
                            <label class="mr-4">Hız: 
                                <select id="speedSelect" class="border rounded px-2 py-1">
                                    <option value="200">Yavaş</option>
                                    <option value="100" selected>Normal</option>
                                    <option value="50">Hızlı</option>
                                </select>
                            </label>
                        </div>
                        <div class="flex justify-center mb-4">
                            <button id="startBtn" class="bg-green-500 text-white px-6 py-2 rounded-full hover:bg-green-600 transition duration-300 mr-4">
                                <i class="fas fa-play mr-2"></i>Başlat
                            </button>
                            <button id="pauseBtn" class="bg-yellow-500 text-white px-6 py-2 rounded-full hover:bg-yellow-600 transition duration-300 mr-4">
                                <i class="fas fa-pause mr-2"></i>Duraklat
                            </button>
                            <button id="resetBtn" class="bg-red-500 text-white px-6 py-2 rounded-full hover:bg-red-600 transition duration-300">
                                <i class="fas fa-redo mr-2"></i>Yeniden Başlat
                            </button>
                        </div>
                        
                        <div class="flex justify-center">
                            <canvas id="gameCanvas" class="border-4 border-gray-300 rounded-lg" width="400" height="400"></canvas>
                        </div>

                        <div class="mt-6 text-center text-gray-600">
                            <p class="mb-2">Kontroller:</p>
                            <p>Yön tuşları veya W, A, S, D tuşları ile yılanı yönlendirebilirsiniz.</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const startBtn = document.getElementById('startBtn');
        const pauseBtn = document.getElementById('pauseBtn');
        const resetBtn = document.getElementById('resetBtn');
        const scoreElement = document.getElementById('score');

        const gridSize = 20;
        const tileCount = canvas.width / gridSize;
        let snake = [
            {x: 10, y: 10},
        ];
        let food = {x: 15, y: 15};
        let dx = 0;
        let dy = 0;
        let score = 0;
        let gameLoop = null;
        let isPaused = false;
        let level = 1;
        let highScore = localStorage.getItem('snakeHighScore') || 0;
        let foodType = 'normal'; // normal, bonus
        const speeds = {
            slow: 200,
            normal: 100,
            fast: 50
        };

        function drawGame() {
            clearCanvas();
            moveSnake();
            checkCollision();
            drawSnake();
            drawFood();
            updateScore();
        }

        function clearCanvas() {
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        function drawSnake() {
            ctx.fillStyle = '#4CAF50';
            snake.forEach(segment => {
                ctx.fillRect(segment.x * gridSize, segment.y * gridSize, gridSize - 2, gridSize - 2);
            });
        }

        function drawFood() {
            if (foodType === 'bonus') {
                ctx.fillStyle = '#FFD700'; // Altın rengi bonus yemek
            } else {
                ctx.fillStyle = '#FF4444'; // Normal kırmızı yemek
            }
            ctx.fillRect(food.x * gridSize, food.y * gridSize, gridSize - 2, gridSize - 2);
        }

        function moveSnake() {
            if (isPaused) return;

            const head = {x: snake[0].x + dx, y: snake[0].y + dy};
            snake.unshift(head);

            if (head.x === food.x && head.y === food.y) {
                score += 10;
                generateFood();
            } else {
                snake.pop();
            }
        }

        function generateFood() {
            foodType = Math.random() < 0.2 ? 'bonus' : 'normal'; // %20 bonus yemek şansı
            food = {
                x: Math.floor(Math.random() * tileCount),
                y: Math.floor(Math.random() * tileCount)
            };
            if (snake.some(segment => segment.x === food.x && segment.y === food.y)) {
                generateFood();
            }
        }

        function checkCollision() {
            const head = snake[0];

            // Duvarlarla çarpışma kontrolü
            if (head.x < 0 || head.x >= tileCount || head.y < 0 || head.y >= tileCount) {
                gameOver();
            }

            // Kendisiyle çarpışma kontrolü
            for (let i = 1; i < snake.length; i++) {
                if (head.x === snake[i].x && head.y === snake[i].y) {
                    gameOver();
                }
            }
        }

        function gameOver() {
            clearInterval(gameLoop);
            gameLoop = null;
            alert(`Oyun Bitti! Skorunuz: ${score}`);
            resetGame();
        }

        function updateScore() {
            if (foodType === 'bonus') {
                score += 20;
            } else {
                score += 10;
            }
            
            // Her 100 puanda level artışı
            level = Math.floor(score / 100) + 1;
            
            if (score > highScore) {
                highScore = score;
                localStorage.setItem('snakeHighScore', highScore);
            }
            
            scoreElement.textContent = score;
            document.getElementById('highScore').textContent = highScore;
            document.getElementById('level').textContent = level;
        }

        function resetGame() {
            snake = [{x: 10, y: 10}];
            food = {x: 15, y: 15};
            dx = 0;
            dy = 0;
            score = 0;
            level = 1;
            isPaused = false;
            foodType = 'normal';
            updateScore();
            clearCanvas();
            drawSnake();
            drawFood();
        }

        document.addEventListener('keydown', (e) => {
            switch(e.key) {
                case 'ArrowUp':
                case 'w':
                case 'W':
                    if (dy !== 1) { dx = 0; dy = -1; }
                    break;
                case 'ArrowDown':
                case 's':
                case 'S':
                    if (dy !== -1) { dx = 0; dy = 1; }
                    break;
                case 'ArrowLeft':
                case 'a':
                case 'A':
                    if (dx !== 1) { dx = -1; dy = 0; }
                    break;
                case 'ArrowRight':
                case 'd':
                case 'D':
                    if (dx !== -1) { dx = 1; dy = 0; }
                    break;
            }
        });

        startBtn.addEventListener('click', () => {
            if (!gameLoop) {
                gameLoop = setInterval(drawGame, 100);
            }
        });

        pauseBtn.addEventListener('click', () => {
            isPaused = !isPaused;
            pauseBtn.innerHTML = isPaused ? 
                '<i class="fas fa-play mr-2"></i>Devam Et' : 
                '<i class="fas fa-pause mr-2"></i>Duraklat';
        });

        resetBtn.addEventListener('click', () => {
            clearInterval(gameLoop);
            gameLoop = null;
            resetGame();
        });

        // Hız kontrolü için event listener
        document.getElementById('speedSelect').addEventListener('change', (e) => {
            if (gameLoop) {
                clearInterval(gameLoop);
                gameLoop = setInterval(drawGame, parseInt(e.target.value));
            }
        });

        // İlk çizim
        resetGame();
    </script>
</body>
</html>
